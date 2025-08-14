SHELL := /usr/bin/bash

.PHONY: up down logs logs-once ngrok-up ngrok-url ping ping-remote db-shell ps restart bot-up bot-logs bot-restart bot-down alembic-init alembic-rev alembic-upgrade alembic-downgrade alembic-stamp-head migrate alembic-stamp-head-docker

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


# Тесты
.PHONY: test test-bot precommit-install

test:
	# гарантируем, что Postgres из compose поднят
	docker compose --env-file .env up -d postgres
	# прогон pytest в одноразовом контейнере Python, подключённом к сети compose
	docker run --rm \
		--network http_default \
		-v $(PWD):/work \
		-w /work \
		python:3.12-slim \
			bash -lc "python -m pip install --no-cache-dir -e services/tg_bot -e services/http_service pytest pytest-asyncio pytest-faker faker respx >/dev/null && PYTHONPATH=services/tg_bot/src:services/http_service/src TG_TEST_PGHOST=http_service_pg TG_TEST_PGPORT=5432 pytest -q"

test-bot:
	# только тесты tg_bot
	docker compose --env-file .env up -d postgres
	docker run --rm \
		--network http_default \
		-v $(PWD):/work \
		-w /work \
		python:3.12-slim \
			bash -lc "python -m pip install --no-cache-dir -e services/tg_bot -e services/http_service pytest pytest-asyncio pytest-faker faker respx >/dev/null && PYTHONPATH=services/tg_bot/src:services/http_service/src TG_TEST_PGHOST=http_service_pg TG_TEST_PGPORT=5432 pytest -q tests/test_tg_bot_db.py"

.PHONY: alembic-upgrade-docker
alembic-upgrade-docker:
	# запустить alembic upgrade head в одноразовом контейнере
	docker run --rm \
		--network http_default \
		-v $(PWD)/services/http_service:/work \
		-w /work \
		-e DATABASE_URL=postgresql+psycopg://postgres:postgres@http_service_pg:5432/http_service \
		python:3.12-slim \
		bash -lc "python -m pip install --no-cache-dir alembic sqlalchemy psycopg[binary] >/dev/null && PYTHONPATH=./src alembic -c alembic.ini upgrade head && echo 'Alembic upgraded to head'"

precommit-install:
	python -m pip install --upgrade pre-commit >/dev/null || true
	pre-commit install || true


# Alembic (миграции для services/http_service)
alembic-init:
	@cd services/http_service && [ -f alembic.ini ] || alembic init -t async alembic
	@echo "[alembic]\nscripts_location = alembic\nsqlalchemy.url = postgresql+psycopg://postgres:postgres@postgres:5432/http_service" >> services/http_service/alembic.ini
	@sed -i 's/^target_metadata = None/target_metadata = __import__("http_service.db", fromlist=["Base"]).Base.metadata/' services/http_service/alembic/env.py

alembic-rev:
	@cd services/http_service && ALEMBIC_CONFIG=alembic.ini alembic revision --autogenerate -m "$(MSG)"

alembic-upgrade:
	@cd services/http_service && ALEMBIC_CONFIG=alembic.ini alembic upgrade head

alembic-downgrade:
	@cd services/http_service && ALEMBIC_CONFIG=alembic.ini alembic downgrade -1

migrate: alembic-rev alembic-upgrade

alembic-stamp-head:
	@cd services/http_service && ALEMBIC_CONFIG=alembic.ini alembic stamp head

# Stamp head через одноразовый контейнер python, если alembic не установлен локально
alembic-stamp-head-docker:
	@docker run --rm \
		--network http_default \
		-v $(PWD)/services/http_service:/work \
		-w /work \
		python:3.12-slim \
		bash -lc "python -m pip install --no-cache-dir alembic sqlalchemy psycopg[binary] >/dev/null && PYTHONPATH=./src alembic -c alembic.ini stamp head && echo 'Stamped head'"

