# Монорепозиторий сервисов

Запуск локально (Docker Compose):

```bash
docker compose up -d
curl http://localhost:8000/ping
```

Сервисы:
- `services/http_service` — HTTP API (FastAPI, SQLAlchemy, Alembic)
- `services/tg_bot` — Telegram-бот (aiogram)

Makefile (быстрые команды):

```bash
make up          # поднять весь стек (подхватит .env)
make down        # остановить стек
make logs        # поток логов всех сервисов
make logs-once   # единовременный снимок логов (без -f)
make ngrok-up    # поднять только ngrok
make ngrok-url   # показать публичный URL из 4040/api
make ping        # локальная проверка /ping
make ping-remote # проверка /ping на https://$NGROK_DOMAIN
make db-shell    # интерактивная оболочка psql внутри контейнера Postgres
make ps          # статус контейнеров docker compose
make restart     # перезапуск всех сервисов
make bot-up      # поднять только tg_bot
make bot-logs    # логи tg_bot
make bot-restart # рестарт tg_bot
make bot-down    # остановить tg_bot

# Миграции Alembic для http_service
make alembic-init
make alembic-rev MSG="init"
make alembic-upgrade
```

