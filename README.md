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

