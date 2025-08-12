from __future__ import annotations

from fastapi import FastAPI, Query, HTTPException

from .config import Settings
from .logging_setup import configure_logging
from .middleware import RequestIdMiddleware
from .outbound import OutboundClient


def create_app(settings: Settings | None = None) -> FastAPI:
    settings = settings or Settings()

    configure_logging(settings.log_level)

    app = FastAPI(title=settings.app_name)
    app.add_middleware(RequestIdMiddleware)

    client = OutboundClient(settings)

    @app.on_event("shutdown")
    async def _shutdown() -> None:
        await client.aclose()

    @app.get("/ping")
    async def ping() -> dict:
        return {"status": "ok"}

    @app.get("/proxy")
    async def proxy(url: str = Query(..., description="Target URL to fetch as JSON")) -> dict:
        try:
            data = await client.get_json(url)
            return {"ok": True, "data": data}
        except Exception as exc:  # noqa: BLE001 - возвращаем 502 с текстом
            raise HTTPException(status_code=502, detail=str(exc)) from exc

    return app


app = create_app()
