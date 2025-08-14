from __future__ import annotations

import os
from fastapi import APIRouter, Request


router = APIRouter(prefix="/tg")


@router.post("/messages")
async def tg_messages(payload: dict, request: Request) -> dict:
    # HTTP-сервис не хранит доменные данные Telegram; аудит выполняется middleware
    return {"ok": True}


@router.post("/webhook")
async def tg_webhook(payload: dict, request: Request) -> dict:
    # Минимальный фолбэк-ответ напрямую через inline webhook reply Telegram
    # https://core.telegram.org/bots/api#setwebhook - ответ JSON с полем method
    fallback_text = os.getenv("TG_FALLBACK_TEXT", "Мы приняли ваш запрос и сможем ответить позже.")
    message = payload.get("message") or payload.get("edited_message") or {}
    chat = message.get("chat") or {}
    chat_id = chat.get("id")
    if not chat_id:
        # Нечего отвечать — просто ack
        return {"ok": True}
    return {
        "method": "sendMessage",
        "chat_id": chat_id,
        "text": fallback_text,
    }


