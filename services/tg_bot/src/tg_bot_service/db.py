from __future__ import annotations

import os
from typing import AsyncIterator

from sqlalchemy import BigInteger, DateTime, Text, func, text
from sqlalchemy.dialects.postgresql import JSONB
from sqlalchemy.ext.asyncio import (
    AsyncEngine,
    AsyncSession,
    async_sessionmaker,
    create_async_engine,
)
from sqlalchemy.orm import DeclarativeBase, Mapped, mapped_column
from sqlalchemy.engine.url import make_url, URL

import psycopg
from psycopg.errors import InvalidCatalogName


class Base(DeclarativeBase):
    pass


class UserState(Base):
    __tablename__ = "user_state"

    user_id: Mapped[int] = mapped_column(BigInteger, primary_key=True)
    greeted_at: Mapped[DateTime | None] = mapped_column(DateTime(timezone=True), default=None)


class BotSettings(Base):
    __tablename__ = "bot_settings"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    greeting_text: Mapped[str] = mapped_column(Text, default="Привет! Рад(а) познакомиться.")


class UserProfile(Base):
    __tablename__ = "user_profile"

    user_id: Mapped[int] = mapped_column(BigInteger, primary_key=True)
    username: Mapped[str | None] = mapped_column(default=None)
    first_name: Mapped[str | None] = mapped_column(default=None)
    last_name: Mapped[str | None] = mapped_column(default=None)
    updated_at: Mapped[DateTime] = mapped_column(DateTime(timezone=True), server_default=func.now())


class IncomingMessage(Base):
    __tablename__ = "incoming_message"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    chat_id: Mapped[int] = mapped_column(BigInteger)
    user_id: Mapped[int | None] = mapped_column(BigInteger, default=None, index=True)
    text: Mapped[str | None] = mapped_column(Text, default=None)
    date_ts: Mapped[int | None] = mapped_column(BigInteger, default=None)
    raw_json: Mapped[dict | None] = mapped_column(JSONB, default=None)
    created_at: Mapped[DateTime] = mapped_column(DateTime(timezone=True), server_default=func.now())


class OutgoingMessage(Base):
    __tablename__ = "outgoing_message"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    chat_id: Mapped[int] = mapped_column(BigInteger)
    user_id: Mapped[int | None] = mapped_column(BigInteger, default=None, index=True)
    text: Mapped[str | None] = mapped_column(Text, default=None)
    created_at: Mapped[DateTime] = mapped_column(DateTime(timezone=True), server_default=func.now())


def _ensure_database_exists(target_url: URL) -> None:
    admin_url = target_url.set(database="postgres")
    # psycopg ожидает драйвер postgresql, удалим +psycopg из схемы
    admin_dsn = admin_url.set(drivername="postgresql").render_as_string(hide_password=False)
    dbname = target_url.database or "tg_bot"
    try:
        with psycopg.connect(admin_dsn, autocommit=True) as conn:
            with conn.cursor() as cur:
                cur.execute("SELECT 1 FROM pg_database WHERE datname=%s", (dbname,))
                exists = cur.fetchone() is not None
                if not exists:
                    cur.execute(f"CREATE DATABASE {dbname}")
    except Exception:
        # Тихо игнорируем, если нет прав или другая ошибка — далее упадём на подключении
        pass


async def init_db() -> async_sessionmaker[AsyncSession]:
    url_str = os.getenv(
        "TG_BOT_DATABASE_URL",
        "postgresql+psycopg://postgres:postgres@postgres:5432/tg_bot",
    )
    url = make_url(url_str)
    _ensure_database_exists(url)

    engine: AsyncEngine = create_async_engine(url_str, pool_pre_ping=True)
    try:
        async with engine.begin() as conn:
            await conn.run_sync(Base.metadata.create_all)
            # гарантируем, что есть строка с настройками
            await conn.execute(text(
                """
                INSERT INTO bot_settings (id, greeting_text)
                VALUES (1, 'Привет! Рад(а) познакомиться.')
                ON CONFLICT (id) DO NOTHING;
                """
            ))
            # индексы для входящих сообщений
            await conn.execute(text(
                """
                CREATE INDEX IF NOT EXISTS idx_incoming_message_created_at ON incoming_message (created_at);
                CREATE INDEX IF NOT EXISTS idx_incoming_message_chat_id ON incoming_message (chat_id);
                CREATE INDEX IF NOT EXISTS idx_incoming_message_user_id ON incoming_message (user_id);
                """
            ))
            await conn.execute(text(
                """
                CREATE INDEX IF NOT EXISTS idx_outgoing_message_created_at ON outgoing_message (created_at);
                CREATE INDEX IF NOT EXISTS idx_outgoing_message_chat_id ON outgoing_message (chat_id);
                CREATE INDEX IF NOT EXISTS idx_outgoing_message_user_id ON outgoing_message (user_id);
                """
            ))
    except InvalidCatalogName:
        _ensure_database_exists(url)
        async with engine.begin() as conn:
            await conn.run_sync(Base.metadata.create_all)
    return async_sessionmaker(engine, expire_on_commit=False)


async def was_greeted(session: AsyncSession, user_id: int) -> bool:
    result = await session.get(UserState, user_id)
    return result is not None and result.greeted_at is not None


async def mark_greeted(session: AsyncSession, user_id: int) -> None:
    state = await session.get(UserState, user_id)
    if state is None:
        state = UserState(user_id=user_id)
        session.add(state)
    state.greeted_at = func.now()
    await session.commit()


async def get_greeting_text(session: AsyncSession) -> str:
    from sqlalchemy import select
    result = await session.execute(select(BotSettings.greeting_text).where(BotSettings.id == 1))
    row = result.first()
    return row[0] if row else "Привет! Рад(а) познакомиться."


async def upsert_user_profile(session: AsyncSession, message: "Message") -> None:
    from .db import UserProfile  # local import to avoid circular
    user = message.from_user
    if not user:
        return
    profile = await session.get(UserProfile, user.id)
    if profile is None:
        profile = UserProfile(user_id=user.id)
        session.add(profile)
    profile.username = user.username
    profile.first_name = user.first_name
    profile.last_name = user.last_name
    profile.updated_at = func.now()
    await session.commit()


async def save_incoming_message(session: AsyncSession, message: "Message") -> None:
    from .db import IncomingMessage  # local import to avoid circular
    chat_id = message.chat.id if message.chat else 0
    user_id = message.from_user.id if message.from_user else None
    # aiogram Message.date — datetime; приведём к unix time
    try:
        date_ts = int(message.date.timestamp()) if getattr(message, "date", None) else None
    except Exception:
        date_ts = None
    msg = IncomingMessage(
        chat_id=chat_id,
        user_id=user_id,
        text=message.text,
        date_ts=date_ts,
        raw_json=message.model_dump(),
    )
    session.add(msg)
    await session.commit()


async def save_outgoing_message(session: AsyncSession, chat_id: int, user_id: int | None, text: str) -> None:
    from .db import OutgoingMessage
    msg = OutgoingMessage(chat_id=chat_id, user_id=user_id, text=text)
    session.add(msg)
    await session.commit()


