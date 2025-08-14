from __future__ import annotations

import os
from contextlib import asynccontextmanager
from typing import AsyncIterator, Any

from sqlalchemy.ext.asyncio import AsyncEngine, create_async_engine, async_sessionmaker, AsyncSession
from sqlalchemy.orm import DeclarativeBase, Mapped, mapped_column
from sqlalchemy import text, DateTime, func, ForeignKey, BigInteger
from sqlalchemy.dialects.postgresql import JSONB


class Base(DeclarativeBase):
    pass


class RequestLog(Base):
    __tablename__ = "request_log"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    method: Mapped[str]
    path: Mapped[str]
    status: Mapped[int]


class RequestAudit(Base):
    __tablename__ = "request_audit"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    method: Mapped[str]
    path: Mapped[str]
    query: Mapped[str | None]
    body: Mapped[str | None]
    status: Mapped[int]
    # новые jsonb-колонки
    request_headers_json: Mapped[dict[str, Any] | None] = mapped_column(JSONB, default=None)
    duration_ms: Mapped[float | None] = mapped_column(default=None)
    # тело ответа (например, деталь ошибки для 4xx/5xx)
    response_body: Mapped[str | None] = mapped_column(default=None)
    # связь с компактным логом
    request_log_id: Mapped[int | None] = mapped_column(ForeignKey("request_log.id"), default=None)
    created_at: Mapped[DateTime] = mapped_column(DateTime(timezone=True), server_default=func.now())


class ProxyAudit(Base):
    __tablename__ = "proxy_audit"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    target_url: Mapped[str]
    response_body: Mapped[str | None]
    status: Mapped[int]
    # новые jsonb-колонки
    response_headers_json: Mapped[dict[str, Any] | None] = mapped_column(JSONB, default=None)
    response_body_json: Mapped[Any | None] = mapped_column(JSONB, default=None)
    duration_ms: Mapped[float | None] = mapped_column(default=None)
    created_at: Mapped[DateTime] = mapped_column(DateTime(timezone=True), server_default=func.now())


class TgMessage(Base):
    __tablename__ = "tg_message"

    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    chat_id: Mapped[int] = mapped_column(BigInteger)
    user_id: Mapped[int | None] = mapped_column(BigInteger, default=None)
    text: Mapped[str | None]
    date_ts: Mapped[int | None] = mapped_column(BigInteger, default=None)
    raw_json: Mapped[dict[str, Any] | None] = mapped_column(JSONB, default=None)
    created_at: Mapped[DateTime] = mapped_column(DateTime(timezone=True), server_default=func.now())


def make_engine() -> AsyncEngine:
    url = os.getenv("DATABASE_URL", "postgresql+psycopg://postgres:postgres@postgres:5432/postgres")
    if url.startswith("postgresql://"):
        url = url.replace("postgresql://", "postgresql+psycopg://", 1)
    engine = create_async_engine(url, pool_pre_ping=True)
    return engine


async def init_models(engine: AsyncEngine) -> None:
    async with engine.begin() as conn:
        # создаст таблицы, если их ещё нет
        await conn.run_sync(Base.metadata.create_all)
        # безопасные ALTER/ADD для новых колонок и индексов
        await conn.execute(text(
            """
            ALTER TABLE request_audit
                ADD COLUMN IF NOT EXISTS request_headers_json jsonb,
                ADD COLUMN IF NOT EXISTS duration_ms double precision,
                ADD COLUMN IF NOT EXISTS response_body text,
                ADD COLUMN IF NOT EXISTS request_log_id integer;
            CREATE INDEX IF NOT EXISTS idx_request_audit_created_at ON request_audit (created_at);
            CREATE INDEX IF NOT EXISTS idx_request_audit_request_log_id ON request_audit (request_log_id);
            """
        ))
        # добавляем FK, если его ещё нет
        await conn.execute(text(
            """
            DO $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_constraint
                    WHERE conname = 'fk_request_audit_request_log_id'
                ) THEN
                    ALTER TABLE request_audit
                        ADD CONSTRAINT fk_request_audit_request_log_id
                        FOREIGN KEY (request_log_id)
                        REFERENCES request_log(id)
                        ON DELETE SET NULL;
                END IF;
            END$$;
            """
        ))
        await conn.execute(text(
            """
            ALTER TABLE proxy_audit
                ADD COLUMN IF NOT EXISTS response_headers_json jsonb,
                ADD COLUMN IF NOT EXISTS response_body_json jsonb,
                ADD COLUMN IF NOT EXISTS duration_ms double precision;
            CREATE INDEX IF NOT EXISTS idx_proxy_audit_created_at ON proxy_audit (created_at);
            """
        ))
        # мягкая миграция: заполняем jsonb из текстовых колонок без строгого парсинга
        await conn.execute(text(
            """
            UPDATE request_audit SET request_headers_json = COALESCE(request_headers_json, to_jsonb(request_headers))
            WHERE request_headers IS NOT NULL AND request_headers_json IS NULL;
            """
        ))
        await conn.execute(text(
            """
            UPDATE proxy_audit SET response_headers_json = COALESCE(response_headers_json, to_jsonb(response_headers))
            WHERE response_headers IS NOT NULL AND response_headers_json IS NULL;
            """
        ))
        await conn.execute(text(
            """
            UPDATE proxy_audit SET response_body_json = COALESCE(response_body_json, to_jsonb(response_body))
            WHERE response_body IS NOT NULL AND response_body_json IS NULL;
            """
        ))
        await conn.execute(text("select 1"))

        # индексы для tg_message
        await conn.execute(text(
            """
            CREATE INDEX IF NOT EXISTS idx_tg_message_created_at ON tg_message (created_at);
            CREATE INDEX IF NOT EXISTS idx_tg_message_chat_id ON tg_message (chat_id);
            CREATE INDEX IF NOT EXISTS idx_tg_message_user_id ON tg_message (user_id);
            """
        ))


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


