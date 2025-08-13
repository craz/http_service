from __future__ import annotations

import json
from fastapi import FastAPI, Query, HTTPException

from .config import Settings
from .logging_setup import configure_logging
from .middleware import RequestIdMiddleware, RequestDbLogMiddleware
from .outbound import OutboundClient
from .mocks import get_user_by_id
from .db import make_engine, make_session_factory, init_models
from .audit_service import AuditService
from .utils import Stopwatch


def create_app(settings: Settings | None = None) -> FastAPI:
    settings = settings or Settings()

    configure_logging(settings.log_level)

    app = FastAPI(title=settings.app_name)

    # DB
    engine = make_engine()
    session_factory = make_session_factory(engine)
    audit = AuditService(session_factory, max_size=settings.log_max_size)

    async def _get_session():
        return session_factory()

    app.add_middleware(RequestIdMiddleware)
    app.add_middleware(RequestDbLogMiddleware, session_getter=_get_session)

    client = OutboundClient(settings)

    @app.on_event("startup")
    async def _startup() -> None:
        await init_models(engine)

    @app.on_event("shutdown")
    async def _shutdown() -> None:
        await client.aclose()

    @app.get("/ping")
    async def ping() -> dict:
        return {"status": "ok"}

    @app.get("/proxy")
    async def proxy(url: str = Query(..., description="Target URL to fetch as JSON")) -> dict:
        with Stopwatch() as sw:
            try:
                result = await client.get_json(url)
                data = result.get("json") if isinstance(result, dict) else result
                meta = result.get("_meta", {}) if isinstance(result, dict) else {}
            except Exception as exc:  # noqa: BLE001
                await audit.log_proxy(
                    target_url=url,
                    response_body=str(exc),
                    response_headers=None,
                    status=502,
                    duration_ms=sw.duration_ms,
                )
                raise HTTPException(status_code=502, detail=str(exc)) from exc
        await audit.log_proxy(
            target_url=url,
            response_body=data,
            response_headers=meta.get("headers", {}),
            status=200,
            duration_ms=sw.duration_ms,
        )
        return {"ok": True, "data": data}

    @app.get("/users/{user_id}")
    async def get_user(user_id: int) -> dict:
        user = get_user_by_id(user_id)
        if not user:
            raise HTTPException(status_code=404, detail="User not found")
        return user.model_dump()

    return app


app = create_app()
