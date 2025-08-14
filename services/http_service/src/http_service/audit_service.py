from __future__ import annotations

from typing import Any

from sqlalchemy.ext.asyncio import AsyncSession

from .db import RequestLog, RequestAudit, ProxyAudit
from .utils import to_json_safe


class AuditService:
    def __init__(self, session_factory, *, max_size: int = 64 * 1024) -> None:
        self._session_factory = session_factory
        self._max_size = max_size

    async def log_request(
        self,
        *,
        method: str,
        path: str,
        query: str | None,
        body: str | None,
        status: int,
        headers: dict[str, Any] | None,
        duration_ms: float | None,
        response_body: str | None = None,
    ) -> None:
        session: AsyncSession
        async with self._session_factory() as session:
            req_log = RequestLog(
                method=method,
                path=path if not query else f"{path}?{query}",
                status=status,
            )
            session.add(req_log)
            await session.flush()  # получим id без commit
            session.add(
                RequestAudit(
                    method=method,
                    path=path,
                    query=query,
                    body=body,
                    status=status,
                    request_headers_json=headers or {},
                    duration_ms=duration_ms,
                    response_body=response_body,
                    request_log_id=req_log.id,
                )
            )
            await session.commit()

    async def log_proxy(
        self,
        *,
        target_url: str,
        response_body: Any | None,
        response_headers: dict[str, Any] | None,
        status: int,
        duration_ms: float | None,
    ) -> None:
        session: AsyncSession
        async with self._session_factory() as session:
            session.add(
                ProxyAudit(
                    target_url=target_url,
                    response_body=to_json_safe(response_body, limit=self._max_size) if response_body is not None else None,
                    status=status,
                    response_headers_json=response_headers or {},
                    response_body_json=response_body,
                    duration_ms=duration_ms,
                )
            )
            await session.commit()


