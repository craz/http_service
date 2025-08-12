---
auto_apply: true
apply_to:
  - "**/*.py"
  - "pyproject.toml"
  - "requirements*.txt"
priority: 30
description: "Правила для Python кода"
---

# Правила для Python

- Версии и зависимости фиксировать в `pyproject.toml`/`requirements.txt`.
- Виртуальные окружения — хранить вне репозитория (`.venv/`, `venv/` в `.gitignore`).
- Стиль: PEP 8, понятные имена, ранние возвраты, без глубокой вложенности.
- Типизация: использовать `typing` и аннотации в публичных API.
- Тесты: `pytest` по умолчанию, структура `tests/`.
- Инструменты качества: `ruff`/`flake8`, `black`/`ruff format`, `mypy` при необходимости.
