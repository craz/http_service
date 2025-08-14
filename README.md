# Монорепозиторий сервисов

## Быстрый старт (Docker Compose)

1) Подготовьте переменные окружения `.env` (минимум токен бота):
```
TG_BOT_TOKEN=123:abc
NGROK_DOMAIN= # опционально
```
2) Запуск:
```bash
docker compose up -d
curl http://localhost:8000/ping
```

Дефолтные адреса:
- HTTP: `http://localhost:8000`
- AI‑service: `http://localhost:8010`
- PgAdmin: `http://localhost:8082` (email `admin@example.com`, pass `admin`)
- Grafana: `http://localhost:3000` (admin/admin)
- Loki API: `http://localhost:3100`
- Ollama: `http://localhost:11434`

## Сервисы
- `services/http_service` — HTTP API (FastAPI, SQLAlchemy, Alembic). Универсальный роутер/логгер, не хранит доменные данные TG.
- `services/tg_bot` — Telegram‑бот (aiogram) с собственной БД `tg_bot`:
  - приветствие из БД, без эхо по умолчанию;
  - меню команд: `/start`, `/about`, `/cost`;
  - система интентов (детерминированные ответы по шаблонам);
  - история входящих/исходящих сообщений и профили пользователей;
  - передача запросов в `ai_service` и сохранение ответа;
  - голосовые пока не поддерживаются (отвечает подсказкой прислать текст).
- `services/ai_service` — AI‑сервис (FastAPI) + Ollama (`mistral`), своя БД `ai_service`:
  - эндпоинт `/generate` принимает `{text, chat_id, user_id, system}`;
  - сохраняет system/user/assistant сообщения;
  - вызывает Ollama с возможностью передать системный промпт.
- `adm_web` — существующая админка (Yii2, PHP) как отдельный контейнер; подключена к `adm_pg` (внешний том).
- Мониторинг: `loki`, `promtail`, `grafana` с автопровиженингом дашборда.

## Makefile (быстрые команды)

```bash
make up          # поднять весь стек (подхватит .env)
make down        # остановить стек
make logs        # поток логов всех сервисов
make logs-once   # снимок логов без -f
make ngrok-up    # поднять только ngrok
make ngrok-url   # показать публичный URL
make ping        # локальная проверка /ping
make ping-remote # /ping через https://$NGROK_DOMAIN
make db-shell    # psql в контейнере Postgres
make ps          # статус docker compose
make restart     # рестарт всех сервисов
make bot-up      # поднять только tg_bot
make bot-logs    # логи tg_bot
make bot-restart # рестарт tg_bot
make bot-down    # остановить tg_bot
```

## Telegram‑бот: контекст и меню
- Меню выставляется автоматически (`/start`, `/about`, `/cost`).
- Контекст управляется через БД интентов и системный промпт:
  - системный промпт: таблица `bot_settings.ai_system_prompt` (id=1);
  - интенты: таблица `intent` с полями `match_type` (substring|equals|startswith|regex), `pattern`, `answer_text`, `priority`.

Примеры SQL:
```sql
-- Системный промпт
UPDATE bot_settings SET ai_system_prompt = 'Ты — дружелюбный ассистент проекта. Отвечай кратко по-русски.' WHERE id = 1;

-- Интент “кто ты”
INSERT INTO intent (name, match_type, pattern, answer_text, enabled, priority)
VALUES ('who_are_you', 'substring', 'кто ты', 'Я тестовый бот для отладки сервисов (HTTP, AI, TG).', true, 100)
ON CONFLICT DO NOTHING;
```

## Тесты и CI
Запуск тестов в контейнерах:
```bash
make test     # все тесты (http_service + tg_bot)
make test-bot # только tg_bot
```
CI: GitHub Actions (`.github/workflows/ci.yml`) запускает тесты на каждый push/PR.

## Мониторинг и логи (Grafana/Loki)
- Grafana: `http://localhost:3000`, datasource Loki уже настроен.
- Дашборд “Monorepo Logs Overview” провижинен автоматически.
- Полезные запросы в Explore:
  - бота: `{service="tg_bot"}`
  - ошибки бота: `{service="tg_bot"} |~ "(?i)error|exception|traceback"`
  - сеть TG: `{service="tg_bot"} |= "TelegramNetworkError"`
  - AI‑сервис: `{service="ai_service"}`
  - ошибки Ollama: `{service="ai_service"} |~ "ollama|request failed|HTTP 5|json parse error"`

## Админка (Yii2)
Подключена как build‑контекст `../adm`, доступна на `:8080`. БД — контейнер `adm_pg` (порт 5544 наружу). Запуск только админки:
```bash
docker compose --env-file .env up -d adm_pg adm_web
```

## Миграции Alembic (http_service)
```bash
make alembic-init
make alembic-rev MSG="init"
make alembic-upgrade
```

## Переменные окружения (ключевые)
- `TG_BOT_TOKEN` — токен Telegram‑бота
- `TG_BOT_DATABASE_URL` — URL БД бота (по умолчанию postgres в compose)
- `AI_DATABASE_URL` — БД AI‑сервиса
- `AI_MODEL` — модель для Ollama (по умолчанию `mistral`)
- `OLLAMA_BASE_URL` — адрес Ollama (`http://ollama:11434`)
- `HTTP_SERVICE_BASE_URL` — URL http_service для бота

## Траблшутинг
- Нет ответа ИИ — увеличены таймауты: бот 60s, AI‑сервис 120s; проверьте дашборд и логи.
- Проблемы сети TG внутри контейнера — обычно решается без IPv6; используем стандартную сессию aiogram.

