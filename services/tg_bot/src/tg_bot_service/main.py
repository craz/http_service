from __future__ import annotations

import asyncio
import os
from aiogram import Bot, Dispatcher
from aiogram.types import Message
from aiogram.filters import CommandStart


async def _on_start(message: Message) -> None:
    await message.answer("Бот запущен. Пришлите любое сообщение.")


async def _echo(message: Message) -> None:
    await message.answer(f"Вы написали: {message.text}")


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


