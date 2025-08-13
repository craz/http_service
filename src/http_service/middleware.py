from __future__ import annotations

import time
import json
from starlette.middleware.base import BaseHTTPMiddleware
from starlette.types import ASGIApp
from starlette.requests import Request
from starlette.responses import Response
from starlette.exceptions import HTTPException as StarletteHTTPException

from .request_context import get_request_id, set_request_id, new_request_id
from .audit_service import AuditService


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
            set_request_id(None)
        response.headers.setdefault(self.header_name, rid)
        return response


class RequestDbLogMiddleware(BaseHTTPMiddleware):
    def __init__(self, app: ASGIApp, session_getter) -> None:
        super().__init__(app)
        self.session_getter = session_getter  # async callable -> session factory

    async def dispatch(self, request: Request, call_next):  # type: ignore[override]
        raw_body_bytes = await request.body()
        async def receive():
            return {"type": "http.request", "body": raw_body_bytes, "more_body": False}
        request._receive = receive  # type: ignore[attr-defined]

        started_at = time.perf_counter()
        status_code: int | None = None
        response_body_text: str | None = None
        try:
            response: Response = await call_next(request)
            status_code = response.status_code
            # Перехват тела ответа
            body_chunks: list[bytes] = []
            async for chunk in response.body_iterator:  # type: ignore[attr-defined]
                body_chunks.append(chunk)
            body_bytes = b"".join(body_chunks)
            response_body_text = body_bytes.decode("utf-8", errors="replace") if body_bytes else None
            new_response = Response(
                content=body_bytes,
                status_code=response.status_code,
                headers=dict(response.headers),
                media_type=response.media_type,
            )
            return new_response
        except StarletteHTTPException as exc:
            status_code = exc.status_code
            try:
                # detail может быть dict/str
                response_body_text = json.dumps(exc.detail) if not isinstance(exc.detail, str) else exc.detail
            except Exception:
                response_body_text = str(exc.detail)
            raise
        except Exception:
            status_code = 500
            raise
        finally:
            try:
                duration_ms = (time.perf_counter() - started_at) * 1000.0
                path_value = request.url.path
                query_value = request.url.query or None
                body_value = raw_body_bytes.decode("utf-8", errors="replace") if raw_body_bytes else None
                headers_dict = dict(request.headers)
                audit = AuditService(self.session_getter)
                await audit.log_request(
                    method=request.method,
                    path=path_value,
                    query=query_value,
                    body=body_value,
                    status=status_code or 0,
                    headers=headers_dict,
                    duration_ms=duration_ms,
                    response_body=response_body_text,
                )
            except Exception:
                pass
