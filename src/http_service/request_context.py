from __future__ import annotations

import uuid
from contextvars import ContextVar
from typing import Optional


_request_id_var: ContextVar[str | None] = ContextVar("request_id", default=None)


def get_request_id() -> Optional[str]:
    return _request_id_var.get()


def set_request_id(value: Optional[str]) -> None:
    _request_id_var.set(value)


def new_request_id() -> str:
    return uuid.uuid4().hex
