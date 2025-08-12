from __future__ import annotations

from starlette.middleware.base import BaseHTTPMiddleware
from starlette.types import ASGIApp
from starlette.requests import Request
from starlette.responses import Response

from .request_context import get_request_id, set_request_id, new_request_id


class RequestIdMiddleware(BaseHTTPMiddleware):
    def __init__(self, app: ASGIApp, header_name: str = "X-Request-ID") -> None:
        super().__init__(app)
        self.header_name = header_name

    async def dispatch(self, request: Request, call_next):  # type: ignore[override]
        incoming_id = request.headers.get(self.header_name)
        rid = incoming_id or new_request_id()
        set_request_id(rid)
        try:
            response: Response = await call_next(request)
        finally:
            # очищаем контекст
            set_request_id(None)
        response.headers.setdefault(self.header_name, rid)
        return response
