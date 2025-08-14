from __future__ import annotations

from fastapi import FastAPI
from pydantic import BaseModel
from sqlalchemy.ext.asyncio import AsyncSession

from .db import make_engine, init_models, make_session_factory, session_scope
import os
import httpx
import logging


class GenerateRequest(BaseModel):
    text: str
    chat_id: int | None = None
    user_id: int | None = None


class GenerateResponse(BaseModel):
    reply: str


app = FastAPI()
_engine = make_engine()
_session_factory = make_session_factory(_engine)
logger = logging.getLogger("ai_service")


@app.on_event("startup")
async def _startup() -> None:
    await init_models(_engine)


@app.post("/generate", response_model=GenerateResponse)
async def generate(req: GenerateRequest) -> GenerateResponse:
    async with session_scope(_session_factory) as session:
        session_id = await _ensure_session(session, req.chat_id, req.user_id)
        await _save_message(session, session_id, role="user", text=req.text)

    reply_text = await _generate_with_ollama(req.text)

    async with session_scope(_session_factory) as session:
        await _save_message(session, session_id, role="assistant", text=reply_text)

    return GenerateResponse(reply=reply_text)


async def _generate_with_ollama(prompt: str) -> str:
    base_url = os.getenv("OLLAMA_BASE_URL", "http://ollama:11434")
    model = os.getenv("AI_MODEL", "mistral")
    url = f"{base_url}/api/generate"
    payload = {"model": model, "prompt": prompt, "stream": False}
    timeout = httpx.Timeout(30.0)
    try:
        async with httpx.AsyncClient(timeout=timeout) as client:
            resp = await client.post(url, json=payload)
            if resp.status_code == 200:
                try:
                    data = resp.json()
                except Exception as parse_err:
                    logger.error("ollama json parse error: %s; body: %r", parse_err, await resp.aread())
                    return ""
                reply = data.get("response") or ""
                if not reply:
                    logger.warning("ollama returned empty response for model=%s", model)
                return reply
            else:
                logger.error("ollama HTTP %s: %s", resp.status_code, await resp.aread())
    except Exception as e:
        logger.exception("ollama request failed: %s", e)
    # Возврат пустой строки, чтобы бот не отвечал эхо‑фразой с префиксом
    return ""


@app.get("/health")
async def health() -> dict:
    base_url = os.getenv("OLLAMA_BASE_URL", "http://ollama:11434")
    try:
        async with httpx.AsyncClient(timeout=5.0) as client:
            r = await client.get(f"{base_url}/api/tags")
            ok = r.status_code == 200
            models = r.json().get("models", []) if ok else []
            return {"ok": ok, "models": [m.get("model") for m in models]}
    except Exception as e:
        return {"ok": False, "error": str(e)}


async def _ensure_session(session: AsyncSession, chat_id: int | None, user_id: int | None) -> int:
    from sqlalchemy import select, insert
    from .db import AiSession
    if chat_id is None and user_id is None:
        chat_id = 0
    res = await session.execute(select(AiSession.id).where(AiSession.chat_id == (chat_id or 0), AiSession.user_id == user_id))
    row = res.first()
    if row:
        return int(row[0])
    result = await session.execute(insert(AiSession).values(chat_id=chat_id or 0, user_id=user_id).returning(AiSession.id))
    new_id = result.scalar_one()
    return int(new_id)


async def _save_message(session: AsyncSession, session_id: int, role: str, text: str) -> None:
    from .db import AiMessage
    msg = AiMessage(session_id=session_id, role=role, text=text)
    session.add(msg)


