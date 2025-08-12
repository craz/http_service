# syntax=docker/dockerfile:1

FROM python:3.12-slim AS builder
WORKDIR /app

ENV PIP_DISABLE_PIP_VERSION_CHECK=1 \
    PIP_NO_CACHE_DIR=1 \
    PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1

RUN apt-get update && apt-get install -y --no-install-recommends build-essential && rm -rf /var/lib/apt/lists/*

COPY pyproject.toml README.md ./
COPY src ./src

RUN python -m pip install --upgrade pip && \
    python -m pip wheel --wheel-dir /wheels .

FROM python:3.12-slim AS runtime
WORKDIR /app

ENV PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1

# непривилегированный пользователь
RUN useradd -m appuser
USER appuser

COPY --from=builder /wheels /wheels
RUN python -m pip install --no-cache-dir /wheels/*

COPY --chown=appuser:appuser src ./src

EXPOSE 8000

CMD ["python", "-m", "uvicorn", "http_service.main:app", "--host", "0.0.0.0", "--port", "8000"]
