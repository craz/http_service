---
auto_apply: true
apply_to:
  ["Dockerfile", "docker-compose.yml", "docker/**/*", "**/.dockerignore"]
---

# Правила для Docker и DevOps

## Dockerfile

- Использовать многоэтапную сборку для оптимизации размера образа
- Кэшировать зависимости для ускорения сборки
- Использовать официальные базовые образы
- Не запускать контейнеры от root пользователя

## Docker Compose

- Использовать переменные окружения для конфигурации
- Создавать отдельные сети для разных сервисов
- Использовать volumes для персистентных данных
- Настраивать health checks для сервисов

## Структура окружения

```yaml
# docker-compose.yml
version: "3.8"

services:
  web:
    build: .
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
      - ./uploads:/var/www/html/web/uploads
    environment:
      - DB_HOST=mysql
      - DB_NAME=adm_db
    depends_on:
      - mysql
    networks:
      - app-network

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: adm_db
    volumes:
      - mysql_data:/var/lib/mysql
      - ./docker/mysql/init:/docker-entrypoint-initdb.d
    ports:
      - "3306:3306"
    networks:
      - app-network

volumes:
  mysql_data:

networks:
  app-network:
    driver: bridge
```

## Безопасность

- Использовать secrets для паролей и ключей
- Регулярно обновлять базовые образы
- Сканировать образы на уязвимости
- Ограничивать ресурсы контейнеров

## Лучшие практики

- Создавать .dockerignore файл
- Использовать здоровые проверки
- Логировать в stdout/stderr
- Использовать init система в контейнерах
