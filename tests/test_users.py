from fastapi.testclient import TestClient

from http_service.main import app


def test_get_user_ok():
    client = TestClient(app)
    r = client.get("/users/1")
    assert r.status_code == 200
    assert r.json() == {"id": 1, "fio": "Павлов Алексей Васильевич"}


def test_get_user_not_found():
    client = TestClient(app)
    r = client.get("/users/999")
    assert r.status_code == 404
    assert r.json()["detail"] == "User not found"
