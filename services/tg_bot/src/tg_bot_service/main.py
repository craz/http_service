from __future__ import annotations

import asyncio
import os
import httpx
from aiogram import Bot, Dispatcher
from aiogram.types import Message
from aiogram.filters import CommandStart


async def _on_start(message: Message) -> None:
    await message.answer("Бот запущен. Пришлите любое сообщение.")
    await _send_to_http_service(message)


async def _echo(message: Message) -> None:
    await message.answer(f"Вы написали: {message.text}")
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


async def run_async() -> None:
    token = os.getenv("TG_BOT_TOKEN")
    if not token:
        raise RuntimeError("TG_BOT_TOKEN не задан")
    bot = Bot(token=token)
    dp = Dispatcher()
    dp.message.register(_on_start, CommandStart())
    dp.message.register(_echo)
    await dp.start_polling(bot)


def run() -> None:
    asyncio.run(run_async())


if __name__ == "__main__":
    run()


