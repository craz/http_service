from __future__ import annotations

from fastapi import APIRouter, Query
import httpx


router = APIRouter()


@router.get("/proxy")
async def proxy(url: str = Query(..., min_length=1)) -> dict:
    async with httpx.AsyncClient() as client:
        response = await client.get(url, timeout=10.0)
    try:
        data = response.json()
    except ValueError:
        data = response.text
    return {"ok": response.status_code < 400, "data": data}



