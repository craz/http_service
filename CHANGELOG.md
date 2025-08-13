# Changelog

Все значимые изменения этого проекта будут документироваться в этом файле.

Формат основан на "Keep a Changelog" и проект следует Semantic Versioning.

## [Unreleased]
- Added: структура правил в `.cursor/rules/` и документация для применения правил
 - Added: первая версия технической документации `TECHNICAL.md` (архитектура, эндпоинты, аудит, примеры кода)
 - Changed: удалён устаревший ключ `version` из `docker-compose.yml` для совместимости с Docker Compose v2
 - Added: Makefile цели (`up/down/logs/ngrok-up/ngrok-url/ping/ping-remote`), healthcheck для `ngrok`

## [0.1.1] - 2025-08-13
- Added: `TECHNICAL.md` — техническая документация (архитектура, эндпоинты, аудит)
- Changed: аудит переведён на jsonb-колонки и расширен по полям/метрикам
- Changed: совместимость `docker-compose.yml` с Compose v2 (удалён `version`)
