# http_service

Запуск локально (Docker Compose):

```bash
docker compose up -d
curl http://localhost:8000/ping
```

Release flow:
- Тегируйте версию `vX.Y.Z` → GitHub Actions соберёт и опубликует образ в GHCR:
  - `ghcr.io/<owner>/http_service:X.Y.Z`
  - `ghcr.io/<owner>/http_service:latest`

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
```

