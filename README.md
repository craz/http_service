# Монорепозиторий сервисов

Запуск локально (Docker Compose):

```bash
docker compose up -d
curl http://localhost:8000/ping
```

Сервисы:
- `services/http_service` — HTTP API (FastAPI, SQLAlchemy, Alembic)
- `services/tg_bot` — Telegram‑бот (aiogram, отдельная БД `tg_bot`)
- `services/ai_service` — AI‑сервис (FastAPI) + Ollama (`mistral`), своя БД `ai_service`
- `adm_web` — админка (Yii2, PHP) как отдельный контейнер; подключена к `adm_pg`

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

# Админка (Yii2)
# Подключена как build‑контекст ../adm и доступна на :8080; БД — контейнер `adm_pg` (5544->5432)
Запуск только админки:

```bash
docker compose --env-file .env up -d adm_pg adm_web
```

# Миграции Alembic для http_service
make alembic-init
make alembic-rev MSG="init"
make alembic-upgrade
```

Тесты (dockerized):

```bash
make test     # все тесты (http_service + tg_bot)
make test-bot # только тесты tg_bot
```

CI: GitHub Actions в `.github/workflows/ci.yml` запускает тесты на каждый push/PR.

