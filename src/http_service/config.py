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

    class Config:
        env_prefix = "HTTP_SERVICE_"
        env_file = ".env"
        case_sensitive = False
