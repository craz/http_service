---
auto_apply: true
apply_to:
  - "**/Dockerfile"
  - "**/*.dockerfile"
  - "**/docker-compose*.yml"
  - "**/docker-compose*.yaml"
priority: 50
description: "Правила для Docker и Compose"
---

# Docker

- Использовать минимальные базовые образы (slim/alpine, если совместимо).
- Многошаговые сборки для сокращения размера.
- Не хранить секреты в образе; использовать переменные окружения/секреты.
- `docker-compose` для локальной разработки и интеграции сервисов.
