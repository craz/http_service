from __future__ import annotations

from fastapi import APIRouter, Request
from sqlalchemy.ext.asyncio import AsyncSession

from ..db import TgMessage


router = APIRouter(prefix="/tg")


@router.post("/messages")
async def tg_messages(payload: dict, request: Request) -> dict:
    session_factory = request.app.state.session_factory  # type: ignore[attr-defined]
    session: AsyncSession
    async with session_factory() as session:
        chat = payload.get("chat") if isinstance(payload.get("chat"), dict) else {}
        user = payload.get("from") if isinstance(payload.get("from"), dict) else {}
        chat_id = chat.get("id") if isinstance(chat, dict) else payload.get("chat_id")
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


