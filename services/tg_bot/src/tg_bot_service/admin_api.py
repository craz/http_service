from __future__ import annotations

import os
from typing import Optional

from fastapi import FastAPI, Depends, HTTPException, Header, status
from pydantic import BaseModel, Field
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from .db import init_db, Intent, BotSettings


_session_factory = None


async def _get_session_factory():
    global _session_factory
    if _session_factory is None:
        _session_factory = await init_db()
    return _session_factory


async def get_db_session() -> AsyncSession:
    factory = await _get_session_factory()
    async with factory() as session:
        yield session


async def admin_auth(x_admin_token: Optional[str] = Header(default=None)) -> None:
    expected = os.getenv("ADMIN_API_TOKEN")
    if not expected:
        return  # если токен не задан — не блокируем (локальная разработка)
    if x_admin_token != expected:
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Unauthorized")


class IntentIn(BaseModel):
    name: str = Field(..., max_length=100)
    match_type: str = Field("substring", pattern="^(substring|equals|startswith|regex)$")
    pattern: str
    answer_text: str
    enabled: bool = True
    priority: int = 0


class IntentOut(IntentIn):
    id: int

    class Config:
        from_attributes = True


class SettingsIn(BaseModel):
    greeting_text: Optional[str] = None
    ai_system_prompt: Optional[str] = None


class SettingsOut(SettingsIn):
    id: int

    class Config:
        from_attributes = True


def create_app() -> FastAPI:
    app = FastAPI(title="tg-bot admin API", version="1.0.0")

    @app.get("/admin/intent", response_model=list[IntentOut], dependencies=[Depends(admin_auth)])
    async def list_intents(db: AsyncSession = Depends(get_db_session)):
        res = await db.execute(select(Intent).order_by(Intent.priority.desc(), Intent.id.asc()))
        return list(res.scalars())

    @app.post("/admin/intent", response_model=IntentOut, status_code=201, dependencies=[Depends(admin_auth)])
    async def create_intent(payload: IntentIn, db: AsyncSession = Depends(get_db_session)):
        item = Intent(
            name=payload.name,
            match_type=payload.match_type,
            pattern=payload.pattern,
            answer_text=payload.answer_text,
            enabled=payload.enabled,
            priority=payload.priority,
        )
        db.add(item)
        await db.commit()
        await db.refresh(item)
        return item

    @app.get("/admin/intent/{intent_id}", response_model=IntentOut, dependencies=[Depends(admin_auth)])
    async def get_intent(intent_id: int, db: AsyncSession = Depends(get_db_session)):
        item = await db.get(Intent, intent_id)
        if not item:
            raise HTTPException(status_code=404, detail="Not found")
        return item

    @app.put("/admin/intent/{intent_id}", response_model=IntentOut, dependencies=[Depends(admin_auth)])
    async def update_intent(intent_id: int, payload: IntentIn, db: AsyncSession = Depends(get_db_session)):
        item = await db.get(Intent, intent_id)
        if not item:
            raise HTTPException(status_code=404, detail="Not found")
        item.name = payload.name
        item.match_type = payload.match_type
        item.pattern = payload.pattern
        item.answer_text = payload.answer_text
        item.enabled = payload.enabled
        item.priority = payload.priority
        await db.commit()
        await db.refresh(item)
        return item

    @app.delete("/admin/intent/{intent_id}", status_code=204, dependencies=[Depends(admin_auth)])
    async def delete_intent(intent_id: int, db: AsyncSession = Depends(get_db_session)):
        item = await db.get(Intent, intent_id)
        if not item:
            return  # идемпотентность
        await db.delete(item)
        await db.commit()

    @app.get("/admin/settings", response_model=SettingsOut, dependencies=[Depends(admin_auth)])
    async def get_settings(db: AsyncSession = Depends(get_db_session)):
        res = await db.execute(select(BotSettings).where(BotSettings.id == 1))
        row = res.scalar_one_or_none()
        if row is None:
            # Инициализируем по умолчанию
            row = BotSettings(id=1)
            db.add(row)
            await db.commit()
            await db.refresh(row)
        return row

    @app.put("/admin/settings", response_model=SettingsOut, dependencies=[Depends(admin_auth)])
    async def update_settings(payload: SettingsIn, db: AsyncSession = Depends(get_db_session)):
        res = await db.execute(select(BotSettings).where(BotSettings.id == 1))
        row = res.scalar_one_or_none()
        if row is None:
            row = BotSettings(id=1)
            db.add(row)
        if payload.greeting_text is not None:
            row.greeting_text = payload.greeting_text
        if payload.ai_system_prompt is not None:
            row.ai_system_prompt = payload.ai_system_prompt
        await db.commit()
        await db.refresh(row)
        return row

    return app


