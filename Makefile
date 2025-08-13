SHELL := /usr/bin/bash

.PHONY: up down logs logs-once ngrok-up ngrok-url ping ping-remote db-shell ps restart bot-up bot-logs bot-restart bot-down

up:
	docker compose --env-file .env up -d

down:
	docker compose down

logs:
	docker compose logs -f --tail=200

logs-once:
	docker compose logs --tail=200

ngrok-up:
	docker compose --env-file .env up -d ngrok

ngrok-url:
	curl -s http://localhost:4040/api/tunnels | jq -r '.tunnels[]?.public_url'

ping:
	curl -s http://localhost:8000/ping | jq .

ping-remote:
	@NGROK_DOMAIN=$$(grep -E '^NGROK_DOMAIN=' .env | cut -d= -f2); \
	if [ -z "$$NGROK_DOMAIN" ]; then \
		echo "NGROK_DOMAIN не задан в .env"; exit 1; \
	fi; \
	curl -sS https://$$NGROK_DOMAIN/ping | jq .

db-shell:
	@echo "Подключение к Postgres контейнера http_service_pg..."; \
	docker exec -it http_service_pg psql -U postgres -d http_service

ps:
	docker compose ps

restart:
	docker compose restart

bot-up:
	docker compose --env-file .env up -d tg_bot

bot-logs:
	docker compose logs -f tg_bot

bot-restart:
	docker compose restart tg_bot

bot-down:
	docker compose stop tg_bot || true
	docker compose rm -f tg_bot || true


