from __future__ import annotations

import json
import time
from typing import Any

DEFAULT_MAX_LOG_SIZE = 64 * 1024  # 64 KB


def truncate(value: str | bytes | None, limit: int = DEFAULT_MAX_LOG_SIZE) -> str | None:
    if value is None:
        return None
    if isinstance(value, bytes):
        try:
            value = value.decode("utf-8", errors="replace")
        except Exception:
            value = value[:limit].decode("utf-8", errors="replace")
    if len(value) > limit:
        return value[:limit] + "…"
    return value


def to_json_safe(obj: Any, limit: int = DEFAULT_MAX_LOG_SIZE) -> str:
    try:
        text = json.dumps(obj, ensure_ascii=False, default=str)
    except Exception:
        text = str(obj)
    return truncate(text, limit) or ""


class Stopwatch:
    def __init__(self) -> None:
        self._start: float | None = None
        self.duration_ms: float | None = None

    def __enter__(self) -> "Stopwatch":
        self._start = time.perf_counter()
        return self

    def __exit__(self, exc_type, exc, tb) -> None:  # noqa: ANN001 - стандартная сигнатура
        if self._start is not None:
            self.duration_ms = (time.perf_counter() - self._start) * 1000.0


