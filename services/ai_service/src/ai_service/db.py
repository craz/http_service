from __future__ import annotations

import os
from typing import AsyncIterator

from sqlalchemy import DateTime, Text
from sqlalchemy.orm import DeclarativeBase, Mapped, mapped_column
from sqlalchemy.ext.asyncio import AsyncEngine, AsyncSession, async_sessionmaker, create_async_engine
from sqlalchemy import func, BigInteger
from contextlib import asynccontextmanager
from sqlalchemy.engine.url import make_url
import psycopg


class Base(DeclarativeBase):
    pass


class AiSession(Base):
    __tablename__ = "ai_session"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    chat_id: Mapped[int] = mapped_column(BigInteger)
    user_id: Mapped[int | None] = mapped_column(BigInteger, default=None, index=True)
    created_at: Mapped[DateTime] = mapped_column(DateTime(timezone=True), server_default=func.now())


class AiMessage(Base):
    __tablename__ = "ai_message"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    session_id: Mapped[int] = mapped_column(index=True)
    role: Mapped[str] = mapped_column(default="user")  # user|assistant|system
    text: Mapped[str] = mapped_column(Text)
    created_at: Mapped[DateTime] = mapped_column(DateTime(timezone=True), server_default=func.now())


class AiSettings(Base):
    __tablename__ = "ai_settings"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    model: Mapped[str] = mapped_column(Text, default="mistral")
    ollama_base_url: Mapped[str] = mapped_column(Text, default="http://ollama:11434")
    created_at: Mapped[DateTime] = mapped_column(DateTime(timezone=True), server_default=func.now())
    updated_at: Mapped[DateTime] = mapped_column(DateTime(timezone=True), server_default=func.now())


def make_engine() -> AsyncEngine:
    url = os.getenv("AI_DATABASE_URL", "postgresql+psycopg://postgres:postgres@postgres:5432/ai_service")
    if url.startswith("postgresql://"):
        url = url.replace("postgresql://", "postgresql+psycopg://", 1)
    _ensure_database_exists(url)
    return create_async_engine(url, pool_pre_ping=True)


async def init_models(engine: AsyncEngine) -> None:
    async with engine.begin() as conn:
        await conn.run_sync(Base.metadata.create_all)
        # гарантируем наличие строки настроек (id=1)
        await conn.exec_driver_sql(
            """
            INSERT INTO ai_settings (id, model, ollama_base_url)
            VALUES (1, 'mistral', 'http://ollama:11434')
            ON CONFLICT (id) DO NOTHING
            """
        )


def make_session_factory(engine: AsyncEngine) -> async_sessionmaker[AsyncSession]:
    return async_sessionmaker(engine, expire_on_commit=False)


@asynccontextmanager
async def session_scope(session_factory: async_sessionmaker[AsyncSession]) -> AsyncIterator[AsyncSession]:
    async with session_factory() as session:
        try:
            yield session
            await session.commit()
        except Exception:
            await session.rollback()
            raise


def _ensure_database_exists(url: str) -> None:
    parsed = make_url(url)
    target_db = parsed.database or "ai_service"
    # Подключаемся к системной БД postgres
    admin_dsn = (
        f"host={parsed.host or 'postgres'} "
        f"port={parsed.port or 5432} "
        f"user={parsed.username or 'postgres'} "
        f"password={parsed.password or 'postgres'} "
        f"dbname=postgres"
    )
    try:
        with psycopg.connect(admin_dsn, autocommit=True) as conn:
            with conn.cursor() as cur:
                cur.execute("SELECT 1 FROM pg_database WHERE datname=%s", (target_db,))
                exists = cur.fetchone() is not None
                if not exists:
                    cur.execute(f'CREATE DATABASE "{target_db}"')
    except Exception:
        # В контейнере без прав — просто продолжаем; создание таблиц упадёт явно
        pass


async def get_settings(session: AsyncSession) -> tuple[str, str]:
    """Возвращает (ollama_base_url, model) из БД (таблица ai_settings, id=1)."""
    from sqlalchemy import select
    result = await session.execute(select(AiSettings.ollama_base_url, AiSettings.model).where(AiSettings.id == 1))
    row = result.first()
    if row is None:
        # На случай гонки до init_models — создадим с дефолтами
        settings = AiSettings(id=1)
        session.add(settings)
        await session.flush()
        return (settings.ollama_base_url, settings.model)
    return (row[0], row[1])


