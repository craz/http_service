from __future__ import annotations

from .models import User


USERS: dict[int, User] = {
    1: User(id=1, fio="Павлов Алексей Васильевич"),
}


def get_user_by_id(user_id: int) -> User | None:
    return USERS.get(user_id)
