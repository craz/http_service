from __future__ import annotations

import httpx
from tenacity import Retrying, stop_after_attempt, wait_exponential, retry_if_exception_type

from .config import Settings
from .request_context import get_request_id
from .utils import truncate


class OutboundClient:
    def __init__(self, settings: Settings) -> None:
        self.settings = settings
        self._client = httpx.AsyncClient(
            timeout=self.settings.timeout_seconds,
            headers={"User-Agent": self.settings.user_agent},
        )

    async def get_json(self, url: str) -> dict:
        headers = {}
        rid = get_request_id()
        if rid:
            headers["X-Request-ID"] = rid

        retry = Retrying(
            stop=stop_after_attempt(self.settings.max_retries + 1),
            wait=wait_exponential(multiplier=0.2, min=0.2, max=2.0),
            retry=retry_if_exception_type((httpx.RequestError, httpx.HTTPStatusError)),
            reraise=True,
        )

        last_exc: Exception | None = None
        for attempt in retry:
            with attempt:
                resp = await self._client.get(url, headers=headers)
                resp.raise_for_status()
                return {
                    "_meta": {
                        "headers": dict(resp.headers.items()),
                        "text": truncate(resp.text, self.settings.log_max_size),
                        "status": resp.status_code,
                    },
                    "json": resp.json(),
                }
        if last_exc:
            raise last_exc
        raise RuntimeError("Failed to fetch URL without specific exception")

    async def aclose(self) -> None:
        await self._client.aclose()
