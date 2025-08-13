## Техническая документация: http_service

### Обзор

Сервис на базе FastAPI для обработки HTTP-запросов, проксирования внешних запросов и ведения расширенного аудита в PostgreSQL. Основные задачи:

- Поддержка корреляции запросов через X-Request-ID
- Надежные внешние вызовы (httpx + tenacity)
- Аудит входящих запросов и ответов внешних сервисов (JSONB-поля, метрики времени)

Стек: FastAPI, httpx (AsyncClient), SQLAlchemy Async, psycopg, Pydantic Settings, Tenacity, Structlog.

### Архитектура и жизненный цикл

Монорепозиторий с несколькими сервисами:
- `src/http_service` — HTTP API (FastAPI)
- `services/tg_bot` — Telegram-бот (aiogram, long polling)

Инициализация приложения и инфраструктуры выполняется в `create_app`:

```python
from fastapi import FastAPI

def create_app(settings: Settings | None = None) -> FastAPI:
    settings = settings or Settings()
    configure_logging(settings.log_level)
    app = FastAPI(title=settings.app_name)

    engine = make_engine()
    session_factory = make_session_factory(engine)
    audit = AuditService(session_factory, max_size=settings.log_max_size)

    app.add_middleware(RequestIdMiddleware)
    app.add_middleware(RequestDbLogMiddleware, session_getter=lambda: session_factory())

    client = OutboundClient(settings)

    @app.on_event("startup")
    async def _startup() -> None:
        await init_models(engine)

    @app.on_event("shutdown")
    async def _shutdown() -> None:
        await client.aclose()

    ...
    return app
```

Точки расширения: логирование, middlewares, настройки ретраев и лимиты аудита.

### Конфигурация

Конфигурация через Pydantic Settings с префиксом переменных окружения `HTTP_SERVICE_`:

```python
class Settings(BaseSettings):
    app_name: str = "http_service"
    log_level: str = "INFO"
    timeout_seconds: float = Field(10.0, ge=0.1)
    max_retries: int = Field(2, ge=0)
    user_agent: str = "http_service/0.1.0"
    log_max_size: int = 64 * 1024
    webhook_token: str | None = None
    class Config:
        env_prefix = "HTTP_SERVICE_"
        env_file = ".env"
```

Ключевые параметры: таймауты и ретраи для httpx, размер среза больших полей, опциональный токен вебхуков.

### Middleware

1) RequestIdMiddleware — устанавливает и возвращает `X-Request-ID`:

```python
class RequestIdMiddleware(BaseHTTPMiddleware):
    async def dispatch(self, request: Request, call_next):
        rid = request.headers.get("X-Request-ID") or new_request_id()
        set_request_id(rid)
        try:
            response = await call_next(request)
        finally:
            set_request_id(None)
        response.headers.setdefault("X-Request-ID", rid)
        return response
```

2) RequestDbLogMiddleware — перехватывает тело запроса/ответа, логирует в БД:

```python
class RequestDbLogMiddleware(BaseHTTPMiddleware):
    async def dispatch(self, request: Request, call_next):
        raw_body = await request.body()
        async def receive():
            return {"type": "http.request", "body": raw_body, "more_body": False}
        request._receive = receive  # перезапуск чтения тела

        started_at = time.perf_counter()
        response_body_text: str | None = None
        try:
            response: Response = await call_next(request)
            body_chunks = [chunk async for chunk in response.body_iterator]
            body_bytes = b"".join(body_chunks)
            response_body_text = body_bytes.decode("utf-8", errors="replace") if body_bytes else None
            return Response(content=body_bytes, status_code=response.status_code, headers=dict(response.headers), media_type=response.media_type)
        finally:
            duration_ms = (time.perf_counter() - started_at) * 1000.0
            audit = AuditService(self.session_getter)
            await audit.log_request(
                method=request.method,
                path=request.url.path,
                query=request.url.query or None,
                body=raw_body.decode("utf-8", errors="replace") if raw_body else None,
                status=getattr(locals().get("response", None), "status_code", 0) or 0,
                headers=dict(request.headers),
                duration_ms=duration_ms,
                response_body=response_body_text,
            )
```

### Внешние вызовы: OutboundClient

Асинхронный клиент с ретраями и пробросом `X-Request-ID` во внешние запросы.

```python
class OutboundClient:
    async def get_json(self, url: str) -> dict:
        headers = {}
        rid = get_request_id()
        if rid:
            headers["X-Request-ID"] = rid
        # tenacity с экспоненциальным ожиданием
        resp = await self._client.get(url, headers=headers)
        resp.raise_for_status()
        return {
            "_meta": {"headers": dict(resp.headers.items()), "text": truncate(resp.text, self.settings.log_max_size), "status": resp.status_code},
            "json": resp.json(),
        }
```

### Аудит: модели и сервис

Схема БД (основное):

```python
class RequestLog(Base):
    __tablename__ = "request_log"
    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    method: Mapped[str]
    path: Mapped[str]
    status: Mapped[int]

class RequestAudit(Base):
    __tablename__ = "request_audit"
    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    method: Mapped[str]
    path: Mapped[str]
    query: Mapped[str | None]
    body: Mapped[str | None]
    status: Mapped[int]
    request_headers_json: Mapped[dict[str, Any] | None] = mapped_column(JSONB, default=None)
    duration_ms: Mapped[float | None] = mapped_column(default=None)
    response_body: Mapped[str | None] = mapped_column(default=None)
    request_log_id: Mapped[int | None] = mapped_column(ForeignKey("request_log.id"), default=None)

class ProxyAudit(Base):
    __tablename__ = "proxy_audit"
    id: Mapped[int] = mapped_column(primary_key=True, autoincrement=True)
    target_url: Mapped[str]
    response_body: Mapped[str | None]
    status: Mapped[int]
    response_headers_json: Mapped[dict[str, Any] | None] = mapped_column(JSONB, default=None)
    response_body_json: Mapped[Any | None] = mapped_column(JSONB, default=None)
    duration_ms: Mapped[float | None] = mapped_column(default=None)
```

Сервис аудита:

```python
class AuditService:
    async def log_request(...):
        req_log = RequestLog(method=method, path=path if not query else f"{path}?{query}", status=status)
        session.add(req_log)
        await session.flush()
        session.add(RequestAudit(..., request_log_id=req_log.id))
        await session.commit()

    async def log_proxy(...):
        session.add(ProxyAudit(
            target_url=target_url,
            response_body=to_json_safe(response_body, limit=self._max_size) if response_body is not None else None,
            status=status,
            response_headers_json=response_headers or {},
            response_body_json=response_body,
            duration_ms=duration_ms,
        ))
        await session.commit()
```

### Эндпоинты

- GET `/ping` — healthcheck.
- POST `/webhook/bitrix` — валидация входящего запроса по токену (заголовок `X-Webhook-Token`, query `?token=...`, либо тело для `application/x-www-form-urlencoded` и `application/json`). Возвращает `401` при несовпадении с `Settings.webhook_token`.
- GET `/proxy?url=...` — запрашивает внешний URL, возвращает JSON-данные и логирует в `ProxyAudit` (успех/ошибка, заголовки, время).
- GET `/users/{user_id}` — демо-эндпоинт, возвращает пользователя из `mocks`.

Пример проверки токена в вебхуке:

```python
provided_token = x_webhook_token or token
if provided_token is None and "application/json" in ctype:
    data = json.loads(await request.body())
    provided_token = data.get("token") if isinstance(data, dict) else None
if settings.webhook_token is not None and provided_token != settings.webhook_token:
    raise HTTPException(status_code=401, detail="Invalid token")
```

### Контекст запроса и утилиты

- `request_context`: хранение `request_id` в `ContextVar`, генерация UUIDv4.
- `utils.Stopwatch`: измерение времени секций кода (мс).
- `utils.to_json_safe` и `utils.truncate`: безопасная сериализация и ограничение размера текста для аудита.

### Инициализация БД и миграции

При старте выполняется `init_models(engine)`: создание таблиц, добавление недостающих колонок/индексов (`IF NOT EXISTS`), мягкое заполнение JSONB из старых текстовых колонок.

Подключение: `DATABASE_URL` (формат `postgresql+psycopg://`), авто-конвертация `postgresql://` в async-URL.

### Локальный запуск

- Через Uvicorn: `uvicorn http_service.main:app --reload`
- Через Docker Compose: см. `docker-compose.yml` (Postgres + pgAdmin).

Makefile (developer UX):

```bash
make up          # поднять стек
make down        # остановить стек
make logs        # поток логов
make logs-once   # разовый снимок логов (без -f)
make ngrok-up    # только ngrok
make ngrok-url   # показать публичный URL
make ping        # локальный /ping
make ping-remote # /ping на https://$NGROK_DOMAIN
make db-shell    # psql в контейнере Postgres
make ps          # статус docker compose
make restart     # рестарт всех сервисов
```

### Тесты

Запуск: `pytest -q`. Для интеграционных тестов потребуется доступная БД Postgres, либо моки.

### Расширение

- Добавляйте новые middlewares для сквозных concerns (трейсинг, аутентификация)
- Расширяйте `AuditService` дополнительными полями/таблицами и метриками
- В `OutboundClient` можно параметризовать политику ретраев и idempotency-ключи


