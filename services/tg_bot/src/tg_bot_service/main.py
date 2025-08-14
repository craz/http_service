from __future__ import annotations

import asyncio
import os
import httpx
from aiogram import Bot, Dispatcher
from aiogram.types import Message, ReplyKeyboardMarkup, KeyboardButton
from aiogram.filters import CommandStart

from .db import (
    init_db,
    was_greeted,
    mark_greeted,
    get_greeting_text,
    upsert_user_profile,
    save_incoming_message,
    save_outgoing_message,
)

# одноразовый lazy init фабрики сессий
_session_factory = None

async def _on_start(message: Message) -> None:
    # Приветствие при /start — отправляем и сохраняем в БД бота
    global _session_factory
    if _session_factory is None:
        _session_factory = await init_db()
    session_factory = _session_factory
    try:
        async with session_factory() as session:
            # сохраняем профиль пользователя и входящее сообщение
            await upsert_user_profile(session, message)
            await save_incoming_message(session, message)
            greet_text = await get_greeting_text(session)
            await message.answer(greet_text)
            await mark_greeted(session, user_id=message.from_user.id if message.from_user else 0)
    except Exception:
        # Не срываем UX при ошибках БД
        pass
    await _send_to_http_service(message)


async def _echo(message: Message) -> None:
    # Если пользователь ещё не получал приветствие (одно сообщение после старта), отправим и сохраним
    global _session_factory
    if _session_factory is None:
        _session_factory = await init_db()
    session_factory = _session_factory
    try:
        async with session_factory() as session:
            # сохраняем профиль пользователя и входящее сообщение
            await upsert_user_profile(session, message)
            await save_incoming_message(session, message)
            user_id = message.from_user.id if message.from_user else 0
            if not await was_greeted(session, user_id):
                greet_text = await get_greeting_text(session)
                kb = ReplyKeyboardMarkup(keyboard=[[KeyboardButton(text="Сменить приветствие")]], resize_keyboard=True)
                await message.answer(greet_text, reply_markup=kb)
                await mark_greeted(session, user_id=user_id)
            else:
                ai_reply = await _call_ai_service(message.text or "")
                if ai_reply:
                    await message.answer(ai_reply)
                    await save_outgoing_message(session, message.chat.id if message.chat else 0, user_id, ai_reply)
    except Exception:
        # Не срываем UX при ошибках БД
        pass
    await _send_to_http_service(message)


async def _send_to_http_service(message: Message) -> None:
    base_url = os.getenv("HTTP_SERVICE_BASE_URL", "http://http_service_app:8000")
    url = f"{base_url}/tg/messages"
    payload = message.model_dump()
    timeout = httpx.Timeout(3.0)
    try:
        async with httpx.AsyncClient(timeout=timeout) as client:
            await client.post(url, json=payload)
    except Exception:
        # проглатываем, чтобы не ломать UX бота
        pass


async def _call_ai_service(text: str) -> str | None:
    base_url = os.getenv("AI_SERVICE_BASE_URL", "http://ai_service_app:8010")
    url = f"{base_url}/generate"
    timeout = httpx.Timeout(5.0)
    try:
        async with httpx.AsyncClient(timeout=timeout) as client:
            resp = await client.post(url, json={"text": text})
            if resp.status_code == 200:
                data = resp.json()
                return data.get("reply")
    except Exception:
        return None
    return None


async def run_async() -> None:
    token = os.getenv("TG_BOT_TOKEN")
    if not token:
        raise RuntimeError("TG_BOT_TOKEN не задан")
    bot = Bot(token=token)
    # ранняя инициализация БД, чтобы создать таблицы до обработки апдейтов
    global _session_factory
    if _session_factory is None:
        _session_factory = await init_db()
    dp = Dispatcher()
    dp.message.register(_on_start, CommandStart())
    dp.message.register(_echo)
    await dp.start_polling(bot)


def run() -> None:
    asyncio.run(run_async())


if __name__ == "__main__":
    run()


