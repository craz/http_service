"""Tests for the HTTP service."""

from fastapi.testclient import TestClient

from http_service.main import app


client = TestClient(app)


def test_read_root() -> None:
    """The root endpoint returns a welcome message."""
    response = client.get("/")
    assert response.status_code == 200
    assert response.json() == {"message": "Hello, World!"}
