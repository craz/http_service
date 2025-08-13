from __future__ import annotations

import json
from fastapi import FastAPI, Query, HTTPException, Header, Request

from .config import Settings
from .logging_setup import configure_logging
from .middleware import RequestIdMiddleware, RequestDbLogMiddleware
from .outbound import OutboundClient
from .mocks import get_user_by_id
from .db import make_engine, make_session_factory, init_models
from .audit_service import AuditService
from .utils import Stopwatch
from .db import TgMessage
from sqlalchemy.ext.asyncio import AsyncSession


def create_app(settings: Settings | None = None) -> FastAPI:
    settings = settings or Settings()

    configure_logging(settings.log_level)

    app = FastAPI(title=settings.app_name)

    # DB
    engine = make_engine()
    session_factory = make_session_factory(engine)
    audit = AuditService(session_factory, max_size=settings.log_max_size)

    def _get_session():  # синхронно возвращает AsyncSession
        return session_factory()

    app.add_middleware(RequestIdMiddleware)
    app.add_middleware(RequestDbLogMiddleware, session_getter=_get_session)

    client = OutboundClient(settings)

    @app.on_event("startup")
    async def _startup() -> None:
        await init_models(engine)

    @app.on_event("shutdown")
    async def _shutdown() -> None:
        await client.aclose()

    @app.get("/ping")
    async def ping() -> dict:
        return {"status": "ok"}

    @app.post("/webhook/bitrix")
    async def webhook_bitrix(request: Request, x_webhook_token: str | None = Header(None), token: str | None = None) -> dict:
        provided_token = x_webhook_token or token
        ctype = request.headers.get("content-type", "").lower()
        # поддержка application/x-www-form-urlencoded и JSON-тела
        if provided_token is None:
            try:
                body_bytes = await request.body()
                body_text = body_bytes.decode("utf-8", errors="replace") if body_bytes else ""
                if "application/x-www-form-urlencoded" in ctype and body_text:
                    from urllib.parse import parse_qs
                    parsed = parse_qs(body_text, keep_blank_values=True)
                    values = parsed.get("token")
                    provided_token = values[0] if values else None
                elif "application/json" in ctype and body_text:
                    data = json.loads(body_text)
                    provided_token = data.get("token") if isinstance(data, dict) else None
            except Exception:
                provided_token = provided_token or None
        if settings.webhook_token is not None and provided_token != settings.webhook_token:
            raise HTTPException(status_code=401, detail="Invalid token")
        return {"ok": True}

    @app.get("/proxy")
    async def proxy(url: str = Query(..., description="Target URL to fetch as JSON")) -> dict:
        with Stopwatch() as sw:
            try:
                result = await client.get_json(url)
                data = result.get("json") if isinstance(result, dict) else result
                meta = result.get("_meta", {}) if isinstance(result, dict) else {}
            except Exception as exc:  # noqa: BLE001
                await audit.log_proxy(
                    target_url=url,
                    response_body=str(exc),
                    response_headers=None,
                    status=502,
                    duration_ms=sw.duration_ms,
                )
                raise HTTPException(status_code=502, detail=str(exc)) from exc
        await audit.log_proxy(
            target_url=url,
            response_body=data,
            response_headers=meta.get("headers", {}),
            status=200,
            duration_ms=sw.duration_ms,
        )
        return {"ok": True, "data": data}

    @app.get("/users/{user_id}")
    async def get_user(user_id: int) -> dict:
        user = get_user_by_id(user_id)
        if not user:
            raise HTTPException(status_code=404, detail="User not found")
        return user.model_dump()

    @app.post("/tg/messages")
    async def tg_messages(payload: dict, request: Request) -> dict:
        session: AsyncSession
        async with session_factory() as session:
            chat_id = payload.get("chat", {}).get("id") if isinstance(payload.get("chat"), dict) else payload.get("chat_id")
            user = payload.get("from", {}) if isinstance(payload.get("from"), dict) else {}
            user_id = user.get("id") if isinstance(user, dict) else payload.get("user_id")
            text = payload.get("text")
            date_ts = payload.get("date")
            msg = TgMessage(
                chat_id=chat_id or 0,
                user_id=user_id,
                text=text,
                date_ts=date_ts,
                raw_json=payload,
            )
            session.add(msg)
            await session.commit()
        return {"ok": True}

    return app


app = create_app()
