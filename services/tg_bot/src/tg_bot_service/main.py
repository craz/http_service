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
)

# одноразовый lazy init фабрики сессий
_session_factory = None


def _humanize_reply_text(text: str) -> str:
    # Убираем роботизированные самоописания
    src = text or ""
    lower = src.strip().lower()
    bad_starts = [
        "я - интеллектуальный ассистент",
        "я — интеллектуальный ассистент",
        "как ии",
        "как нейросеть",
        "как искусственный интеллект",
        "как языковая модель",
    ]
    for bad in bad_starts:
        if lower.startswith(bad):
            parts = src.split(".", 1)
            src = parts[1].strip() if len(parts) > 1 else ""
            break

    # Удаляем буллеты/нумерацию и лишние переносы
    import re
    src = re.sub(r"^\s*[-•]+\s*", "", src, flags=re.MULTILINE)  # - / •
    src = re.sub(r"^\s*\d+\)\s*", "", src, flags=re.MULTILINE)  # 1) 2)
    src = re.sub(r"^\s*\d+\.\s*", "", src, flags=re.MULTILINE)  # 1. 2.
    src = src.replace("\r", "")
    src = re.sub(r"\n{2,}", "\n", src)
    src = re.sub(r"\s{2,}", " ", src)

    # Без канцелярита‑извинений по умолчанию
    src = src.replace("Извините, ", "")

    # Жёстко сокращаем ответ до ~50% или не более 2 коротких предложений
    def _split_sentences(s: str) -> list[str]:
        # Простая разбивка по рус./англ. окончаниям предложений
        parts = re.split(r"(?<=[.!?…])\s+", s.strip())
        return [p.strip() for p in parts if p.strip()]

    def _truncate_to_ratio(s: str, ratio: float = 0.5, min_chars: int = 120, max_chars: int = 400) -> str:
        s = s.strip()
        if not s:
            return s
        current_len = len(s)
        target = max(min_chars, int(current_len * ratio))
        target = min(target, max_chars)
        # Если и так коротко — просто вернём слегка нормализованный текст
        if current_len <= target:
            return s
        # Пробуем обрезать по предложениям: максимум 2
        sentences = _split_sentences(s)
        if sentences:
            out: list[str] = []
            total = 0
            for sent in sentences:
                if not sent:
                    continue
                if len(out) >= 2:
                    break
                if total + len(sent) + (1 if total > 0 else 0) > target:
                    # Уместим часть предложения, если совсем длинное
                    room = max(0, target - total - (1 if total > 0 else 0))
                    if room > 20:
                        out.append(sent[:room].rstrip(",;: ") + "…")
                    break
                out.append(sent)
                total += len(sent) + (1 if total > 0 else 0)
            if out:
                return " ".join(out)
        # Фолбэк — жёсткая обрезка по символам
        return s[:target].rstrip(",;: ") + "…"

    return _truncate_to_ratio(src).strip()

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
            # Обработка голосовых: пока не поддерживаем распознавание речи
            if getattr(message, "voice", None) is not None:
                info_text = "Пока не умею распознавать голосовые. Пришлите текст."
                await message.answer(info_text)
                await save_outgoing_message(session, message.chat.id if message.chat else 0, user_id, info_text)
                return
            if not await was_greeted(session, user_id):
                greet_text = await get_greeting_text(session)
                kb = ReplyKeyboardMarkup(keyboard=[[KeyboardButton(text="Сменить приветствие")]], resize_keyboard=True)
                await message.answer(greet_text, reply_markup=kb)
                await mark_greeted(session, user_id=user_id)
            else:
                # Сначала проверим intent-ответ из базы
                intent_answer = await find_intent_answer(session, message.text)
                if intent_answer:
                    await message.answer(intent_answer)
                    await save_outgoing_message(session, message.chat.id if message.chat else 0, user_id, intent_answer)
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
                        ai_reply_human = _humanize_reply_text(ai_reply)
                        await message.answer(ai_reply_human)
                        await save_outgoing_message(session, message.chat.id if message.chat else 0, user_id, ai_reply_human)
                    else:
                        # Резервный ответ при недоступности ИИ или ошибке
                        # Формулируем более «человечный» фолбэк в стиле менеджера
                        fallback_text = "Приняли ваш запрос — вернусь с ответом чуть позже. Если важно срочно, напишите, пожалуйста, что именно нужно закрыть сейчас."
                        await message.answer(fallback_text)
                        await save_outgoing_message(
                            session,
                            message.chat.id if message.chat else 0,
                            user_id,
                            fallback_text,
                        )
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
    await dp.start_polling(bot)


def run() -> None:
    asyncio.run(run_async())


if __name__ == "__main__":
    run()


