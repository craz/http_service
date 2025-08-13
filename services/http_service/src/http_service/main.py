from __future__ import annotations

from fastapi import FastAPI


def create_app() -> FastAPI:
    app = FastAPI(title="http_service")

    from .routers.health import router as health_router
    app.include_router(health_router)

    return app


app = create_app()


