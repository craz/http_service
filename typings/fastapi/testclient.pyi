from __future__ import annotations

from typing import Any, Dict


class Response:
  status_code: int
  def json(self) -> Dict[str, Any]: ...


class TestClient:
  def __init__(self, app: Any) -> None: ...
  def get(self, path: str, *args: Any, **kwargs: Any) -> Response: ...
  def post(self, path: str, *args: Any, **kwargs: Any) -> Response: ...


