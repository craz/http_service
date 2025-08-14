from __future__ import annotations

from fastapi import FastAPI

from .config import Settings
from .logging_setup import configure_logging
from .db import make_engine, make_session_factory, init_models
from .middleware import RequestIdMiddleware, RequestDbLogMiddleware


def create_app(settings: Settings | None = None) -> FastAPI:
    settings = settings or Settings()
    configure_logging(settings.log_level)
    app = FastAPI(title=settings.app_name)

    engine = make_engine()
    session_factory = make_session_factory(engine)
    app.state.session_factory = session_factory  # type: ignore[attr-defined]

    app.add_middleware(RequestIdMiddleware)
    app.add_middleware(RequestDbLogMiddleware, session_getter=lambda: session_factory())

    @app.on_event("startup")
    async def _startup() -> None:
        await init_models(engine)

    from .routers.health import router as health_router
    from .routers.tg import router as tg_router
    from .routers.proxy import router as proxy_router
    from .routers.users import router as users_router
    app.include_router(health_router)
    app.include_router(tg_router)
    app.include_router(proxy_router)
    app.include_router(users_router)

    return app


app = create_app()


