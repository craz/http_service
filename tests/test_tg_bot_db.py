import os
import time
import uuid
import asyncio

import psycopg
import pytest
import pytest_asyncio
from aiogram.types import Message


PGHOST = os.getenv("TG_TEST_PGHOST", "127.0.0.1")
PGPORT = int(os.getenv("TG_TEST_PGPORT", "5432"))


def _make_db_url(db_name: str) -> str:
    return f"postgresql+psycopg://postgres:postgres@{PGHOST}:{PGPORT}/{db_name}"


@pytest_asyncio.fixture(scope="function")
async def tg_bot_session_factory():
    # создаём уникальную БД под тест
    test_db = f"tg_bot_test_{uuid.uuid4().hex[:8]}"
    admin_dsn = f"postgresql://postgres:postgres@{PGHOST}:{PGPORT}/postgres"
    with psycopg.connect(admin_dsn, autocommit=True) as conn:
        with conn.cursor() as cur:
            cur.execute(f"CREATE DATABASE {test_db}")

    os.environ["TG_BOT_DATABASE_URL"] = _make_db_url(test_db)

    # импорт после установки переменной окружения
    from tg_bot_service.db import init_db

    session_factory = await init_db()
    yield session_factory

    # teardown: дроп БД
    with psycopg.connect(admin_dsn, autocommit=True) as conn:
        with conn.cursor() as cur:
            # завершить соединения с БД
            cur.execute(
                f"""
                SELECT pg_terminate_backend(pid)
                FROM pg_stat_activity
                WHERE datname = %s AND pid <> pg_backend_pid()
                """,
                (test_db,),
            )
            cur.execute(f"DROP DATABASE IF EXISTS {test_db}")


@pytest.mark.asyncio
async def test_save_message_and_profile(tg_bot_session_factory, faker):
    from tg_bot_service.db import (
        upsert_user_profile,
        save_incoming_message,
        was_greeted,
        mark_greeted,
        get_greeting_text,
    )

    # Подготовим фейковое сообщение
    user_id = faker.pyint(min_value=10_000, max_value=9_999_999)
    chat_id = faker.pyint(min_value=10_000, max_value=9_999_999)
    payload = {
        "message_id": faker.pyint(min_value=1, max_value=1_000_000),
        "date": int(time.time()),
        "chat": {"id": chat_id, "type": "private"},
        "from": {"id": user_id, "is_bot": False, "first_name": faker.first_name(), "username": faker.user_name()},
        "text": faker.sentence(nb_words=3),
    }
    msg = Message.model_validate(payload)

    async with tg_bot_session_factory() as session:
        # профиль и сообщение сохраняются
        await upsert_user_profile(session, msg)
        await save_incoming_message(session, msg)

        # приветствие доступно и помечается
        greeted_before = await was_greeted(session, user_id)
        assert greeted_before is False
        text = await get_greeting_text(session)
        assert isinstance(text, str) and len(text) > 0
        await mark_greeted(session, user_id)

    # Повторная сессия: проверим, что запись осталась
    from sqlalchemy import text as sql_text
    async with tg_bot_session_factory() as session:
        res = await session.execute(sql_text("SELECT count(*) FROM incoming_message"))
        cnt = res.scalar_one()
        assert cnt >= 1


