import respx
import httpx
from fastapi.testclient import TestClient

from http_service.main import app


def test_ping():
    client = TestClient(app)
    r = client.get("/ping")
    assert r.status_code == 200
    assert r.json() == {"status": "ok"}


@respx.mock
def test_proxy_success():
    client = TestClient(app)

    route = respx.get("https://example.com/api").mock(
        return_value=httpx.Response(200, json={"hello": "world"})
    )

    r = client.get("/proxy", params={"url": "https://example.com/api"})
    assert r.status_code == 200
    data = r.json()
    assert data["ok"] is True
    assert data["data"] == {"hello": "world"}
    assert route.called
