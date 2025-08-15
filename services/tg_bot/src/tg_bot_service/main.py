from __future__ import annotations

import asyncio
import os
import httpx
from aiogram import Bot, Dispatcher
from aiogram.types import Message, ReplyKeyboardMarkup, KeyboardButton, BotCommand
from aiogram.filters import CommandStart
from aiogram.filters import Command

from .db import (
    init_db,
    was_greeted,
    mark_greeted,
    get_greeting_text,
    get_ai_system_prompt,
    upsert_user_profile,
    save_incoming_message,
    save_outgoing_message,
    find_intent_answer,
    get_disclaimer_patterns,
)

# одноразовый lazy init фабрики сессий
_session_factory = None


def _log_kv(event: str, **fields: object) -> None:
    try:
        kv = " ".join(f"{k}={repr(v)}" for k, v in fields.items())
        print(f"{event} {kv}")
    except Exception:
        pass

def _humanize_reply_text(text: str) -> str:
    # Убираем роботизированные самоописания
    src = text or ""
    import re
    # Удаляем самоописания/дисклеймеры по правилам из БД (по строкам)
    # Правила применяются в вызове ниже из _filter_disclaimers_with_rules

    # Удаляем буллеты/нумерацию и лишние переносы
    src = re.sub(r"^\s*[-•]+\s*", "", src, flags=re.MULTILINE)  # - / •
    src = re.sub(r"^\s*\d+\)\s*", "", src, flags=re.MULTILINE)  # 1) 2)
    src = re.sub(r"^\s*\d+\.\s*", "", src, flags=re.MULTILINE)  # 1. 2.
    src = src.replace("\r", "")
    src = re.sub(r"\n{2,}", "\n", src)
    src = re.sub(r"\s{2,}", " ", src)

    # Не ограничиваем длину ответа — возвращаем полностью, только слегка нормализованный
    return src.strip()


async def _filter_disclaimers_with_rules(text: str) -> str:
    global _session_factory
    if _session_factory is None:
        _session_factory = await init_db()
    session_factory = _session_factory
    import re as _re
    async with session_factory() as session:
        patterns = await get_disclaimer_patterns(session)
    # 1) построчная фильтрация
    lines = text.splitlines()
    filtered_lines: list[str] = []
    for ln in lines:
        check = ln.strip()
        if not check:
            filtered_lines.append(ln)
            continue
        skip = False
        for patt in patterns:
            try:
                if _re.search(patt, check, flags=_re.IGNORECASE):
                    skip = True
                    break
            except Exception:
                continue
        if not skip:
            filtered_lines.append(ln)
    result = "\n".join(filtered_lines).strip()
    if result:
        return result
    # 2) если всё вычистилось (часто весь ответ в одной строке) — удалим только предложения
    sentences = _re.split(r"(?<=[.!?…])\s+", text.strip())
    kept: list[str] = []
    for sent in sentences:
        s = sent.strip()
        if not s:
            continue
        drop = False
        for patt in patterns:
            try:
                if _re.search(patt, s, flags=_re.IGNORECASE):
                    drop = True
                    break
            except Exception:
                continue
        if not drop:
            kept.append(s)
    return " ".join(kept).strip()


 

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
    # Если пользователь ещё не получал приветствие — отправим его И всё равно обработаем сообщение
    global _session_factory
    if _session_factory is None:
        _session_factory = await init_db()
    session_factory = _session_factory
    try:
        async with session_factory() as session:
            # сохраняем профиль пользователя и входящее сообщение
            await upsert_user_profile(session, message)
            inc_id = await save_incoming_message(session, message)
            user_id = message.from_user.id if message.from_user else 0
            # Обработка голосовых: пока не поддерживаем распознавание речи
            if getattr(message, "voice", None) is not None:
                info_text = "Пока не умею распознавать голосовые. Пришлите текст."
                await message.answer(info_text)
                await save_outgoing_message(session, message.chat.id if message.chat else 0, user_id, info_text)
                return

            # Приветствие при первом сообщении, но без прерывания основной обработки
            if not await was_greeted(session, user_id):
                greet_text = await get_greeting_text(session)
                kb = ReplyKeyboardMarkup(keyboard=[[KeyboardButton(text="Сменить приветствие")]], resize_keyboard=True)
                await message.answer(greet_text, reply_markup=kb)
                await mark_greeted(session, user_id=user_id)

            # Дальше всегда пробуем ответить по сути сообщения
            intent_answer = await find_intent_answer(session, message.text)
            if intent_answer:
                await message.answer(intent_answer)
                out_id = await save_outgoing_message(session, message.chat.id if message.chat else 0, user_id, intent_answer)
                _log_kv("bot.reply", text=intent_answer, chat_id=message.chat.id if message.chat else 0, user_id=user_id, incoming_id=inc_id, outgoing_id=out_id, via="intent")
            else:
                # Если интентов нет — зовём ИИ с системным промптом
                system_prompt = await get_ai_system_prompt(session)
                ai_reply = await _call_ai_service(
                    message.text or "",
                    message.chat.id if message.chat else 0,
                    user_id,
                    system_prompt,
                )
                if ai_reply:
                    # Применим фильтрацию дисклеймеров из БД
                    filtered = await _filter_disclaimers_with_rules(ai_reply)
                    if not filtered.strip():
                        filtered = ai_reply
                    ai_reply_human = _humanize_reply_text(filtered)
                    await message.answer(ai_reply_human)
                    out_id = await save_outgoing_message(session, message.chat.id if message.chat else 0, user_id, ai_reply_human)
                    _log_kv("bot.reply", text=ai_reply_human, chat_id=message.chat.id if message.chat else 0, user_id=user_id, incoming_id=inc_id, outgoing_id=out_id, via="ai")
                else:
                    # Резервный ответ при недоступности ИИ или ошибке
                    fallback_text = "Приняли ваш запрос — вернусь с ответом чуть позже. Если важно срочно, напишите, пожалуйста, что именно нужно закрыть сейчас."
                    await message.answer(fallback_text)
                    out_id = await save_outgoing_message(
                        session,
                        message.chat.id if message.chat else 0,
                        user_id,
                        fallback_text,
                    )
                    _log_kv("bot.reply", text=fallback_text, chat_id=message.chat.id if message.chat else 0, user_id=user_id, incoming_id=inc_id, outgoing_id=out_id, via="fallback")
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


async def _call_ai_service(text: str, chat_id: int | None = None, user_id: int | None = None, system: str | None = None) -> str | None:
    base_url = os.getenv("AI_SERVICE_BASE_URL", "http://ai_service_app:8010")
    url = f"{base_url}/generate"
    timeout = httpx.Timeout(60.0)
    try:
        async with httpx.AsyncClient(timeout=timeout) as client:
            payload = {"text": text, "chat_id": chat_id, "user_id": user_id, "system": system}
            resp = await client.post(url, json=payload)
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
    # /about и /cost просто перенаправляем в intent‑поиск, чтобы ответ был из БД
    async def _on_about(message: Message) -> None:
        global _session_factory
        if _session_factory is None:
            _session_factory = await init_db()
        async with _session_factory() as session:
            answer = await find_intent_answer(session, "about")
            if answer:
                await message.answer(answer)
                await save_outgoing_message(session, message.chat.id if message.chat else 0, message.from_user.id if message.from_user else None, answer)

    async def _on_cost(message: Message) -> None:
        global _session_factory
        if _session_factory is None:
            _session_factory = await init_db()
        async with _session_factory() as session:
            answer = await find_intent_answer(session, "cost")
            if answer:
                await message.answer(answer)
                await save_outgoing_message(session, message.chat.id if message.chat else 0, message.from_user.id if message.from_user else None, answer)

    dp.message.register(_on_about, Command("about"))
    dp.message.register(_on_cost, Command("cost"))
    dp.message.register(_echo)
    # Команды бота для меню
    try:
        commands = [
            BotCommand(command="start", description="Запуск"),
            BotCommand(command="about", description="О боте"),
            BotCommand(command="cost", description="Стоимость"),
        ]
        await bot.set_my_commands(commands)
    except Exception:
        pass
    # Параллельно поднимем админский API на встроенном Uvicorn
    from .admin_api import create_app
    import uvicorn
    admin_app = create_app()

    admin_host = os.getenv("ADMIN_API_HOST", "0.0.0.0")
    admin_port = int(os.getenv("ADMIN_API_PORT", "8070"))

    # Запустим uvicorn в фоне (в том же процессe)
    import threading

    def _run_admin():
        uvicorn.run(admin_app, host=admin_host, port=admin_port, log_level="info")

    t = threading.Thread(target=_run_admin, daemon=True)
    t.start()

    await dp.start_polling(bot)


def run() -> None:
    asyncio.run(run_async())


if __name__ == "__main__":
    run()


