# Changelog

Все значимые изменения этого проекта будут документироваться в этом файле.

Формат основан на "Keep a Changelog" и проект следует Semantic Versioning.

## [Unreleased]
- Added: AI‑сервис (`services/ai_service`) с `/generate` и `/health`, интеграция с Ollama (`mistral`), БД `ai_service`, поддержка `system` промпта
- Added: Telegram‑бот (`services/tg_bot`) со своей БД `tg_bot`: приветствия, история сообщений, профили, интенты, меню команд
- Changed: `http_service` стал универсальным роутером/логгером без Telegram‑доменных таблиц; поле `created_at` в `request_log`
- Added: Мониторинг Loki/Promtail/Grafana с автопровиженингом дашборда Monorepo Logs Overview
- Added: Тесты (`tests/test_tg_bot_db.py`), Faker/pytest‑faker/respx; Makefile цели `test`/`test-bot`
- Added: CI (GitHub Actions) для автозапуска тестов на push/PR
- Added: Документация обновлена: `README.md`, `PROJECT_GUIDE.md`, `TECHNICAL.md`, `CONTRIBUTING.md`

## [0.1.1] - 2025-08-13
- Added: `TECHNICAL.md` — техническая документация (архитектура, эндпоинты, аудит)
- Changed: аудит переведён на jsonb-колонки и расширен по полям/метрикам
- Changed: совместимость `docker-compose.yml` с Compose v2 (удалён `version`)
