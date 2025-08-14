from __future__ import annotations

from fastapi import APIRouter, HTTPException


router = APIRouter(prefix="/users")


# Простейший in-memory источник данных для тестов
_USERS = {
    1: {"id": 1, "fio": "Павлов Алексей Васильевич"},
}


@router.get("/{user_id}")
async def get_user(user_id: int) -> dict:
    user = _USERS.get(user_id)
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
    return user



