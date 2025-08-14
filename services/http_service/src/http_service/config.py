from pydantic_settings import BaseSettings
from pydantic import Field


class Settings(BaseSettings):
    app_name: str = "http_service"
    log_level: str = "INFO"

    timeout_seconds: float = Field(10.0, ge=0.1)
    max_retries: int = Field(2, ge=0)
    user_agent: str = "http_service/0.1.0"

    # Зарезервировано под туннель на следующих шагах
    ngrok_authtoken: str | None = None

    # Лимит сохранения больших полей аудита (в байтах)
    log_max_size: int = 64 * 1024

    # Опциональный токен валидации входящих вебхуков (заголовок X-Webhook-Token или query ?token=...)
    webhook_token: str | None = None

    class Config:
        env_prefix = "HTTP_SERVICE_"
        env_file = ".env"
        case_sensitive = False


