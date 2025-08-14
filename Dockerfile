# syntax=docker/dockerfile:1

FROM python:3.12-slim AS builder
WORKDIR /app

ENV PIP_DISABLE_PIP_VERSION_CHECK=1 \
    PIP_NO_CACHE_DIR=1 \
    PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1

RUN apt-get update && apt-get install -y --no-install-recommends build-essential && rm -rf /var/lib/apt/lists/*

COPY services ./services

# Позволяем собирать колёса изолированно для конкретного сервиса,
# чтобы сборка tg_bot не тянула зависимости ai_service и наоборот
ARG BUILD_SERVICE=multi
RUN python -m pip install --upgrade pip && \
    if [ "$BUILD_SERVICE" = "tg_bot" ]; then \
        python -m pip wheel --wheel-dir /wheels services/tg_bot ; \
    elif [ "$BUILD_SERVICE" = "http_service" ]; then \
        python -m pip wheel --wheel-dir /wheels services/http_service ; \
    elif [ "$BUILD_SERVICE" = "ai_service" ]; then \
        python -m pip wheel --wheel-dir /wheels services/ai_service ; \
    else \
        python -m pip wheel --wheel-dir /wheels services/tg_bot services/http_service services/ai_service ; \
    fi

FROM python:3.12-slim AS runtime
WORKDIR /app

ENV PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1

# непривилегированный пользователь
RUN useradd -m appuser
USER appuser

COPY --from=builder /wheels /wheels
RUN python -m pip install --no-cache-dir /wheels/*

# только пакетные колёса, исходники не копируем — пакет ставится из wheels

EXPOSE 8000

CMD ["python", "-m", "uvicorn", "http_service.main:app", "--host", "0.0.0.0", "--port", "8000"]
