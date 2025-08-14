from __future__ import annotations

from fastapi import APIRouter, Request


router = APIRouter(prefix="/tg")


@router.post("/messages")
async def tg_messages(payload: dict, request: Request) -> dict:
    # HTTP-сервис не хранит доменные данные Telegram; аудит выполняется middleware
    return {"ok": True}


