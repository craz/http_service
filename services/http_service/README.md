# http_service

Сервис HTTP API (FastAPI). Выделен в `services/http_service`.

Запуск локально (Docker Compose):

```bash
make up
make ping
```

Миграции (Alembic):

```bash
# генерация alembic.ini и структуры (однократно)
make alembic-init

# автогенерация миграции из текущих моделей
make alembic-rev MSG="init tables"

# применение миграций
make alembic-upgrade
```

Структура:
- `src/http_service` — пакет приложения (роутеры, middleware, db, конфиг)
- `alembic/` — миграции

