SHELL := /usr/bin/bash

.PHONY: up down logs ngrok-up ngrok-url ping ping-remote

up:
	docker compose --env-file .env up -d

down:
	docker compose down

logs:
	docker compose logs -f --tail=200

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


