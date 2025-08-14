# ИВАН ВЕЗЕТ — Админ‑панель (Yii2)

Административная панель и backend на Yii2.

## Описание

В составе проекта:

- Основное веб-приложение на Yii2
- Административная панель с модулем admin
- Система управления контентом
- Интеграция с Telegram ботом
- Система уведомлений и рассылок

## Технологии

- **PHP**: >=7.4.0
- **Framework**: Yii2
- **Frontend**: Bootstrap 5
- **Дополнительные компоненты**:
  - reCAPTCHA
  - ImageMagick
  - FancyBox
  - Select2
  - DateRangePicker

## Установка

### Локальная установка

1. Клонируйте репозиторий:

```bash
git clone <your-repo-url>
cd adm
```

2. Установите зависимости:

```bash
composer install
```

3. Настройте базу данных в `config/db.php`

4. Настройте веб-сервер для работы с папкой `web/`

5. Установите права на папки:

```bash
chmod 777 runtime
chmod 777 web/assets
chmod 755 yii
```

### Запуск в Docker

1. Убедитесь, что у вас установлен Docker и Docker Compose

2. Клонируйте репозиторий:

```bash
git clone <your-repo-url>
cd adm
```

3. Запустите проект:

```bash
docker-compose up -d
```

4. Откройте в браузере:

   - **Основное приложение**: http://localhost:8080
   - **Админ панель**: http://localhost:8080/admin
   - **phpMyAdmin**: http://localhost:8081

5. Для остановки:

```bash
docker-compose down
```

## Структура проекта

- `modules/admin/` - Административная панель
- `components/` - Компоненты (Telegram бот, AI бот)
- `models/` - Модели данных
- `controllers/` - Контроллеры
- `views/` - Представления
- `web/` - Публичная директория

## Docker файлы

- `Dockerfile` - Образ для веб-приложения
- `docker-compose.yml` - Конфигурация для запуска всех сервисов
- `config/db-docker.php` - Конфигурация БД для Docker
- `.dockerignore` - Файлы, исключаемые из Docker образа

## Коммиты и Changelog

- Используем Conventional Commits: `feat: ...`, `fix: ...`, `docs: ...`, `refactor: ...`, `chore: ...` и т.д.
- Все заметные изменения фиксируем в `CHANGELOG.md` по формату Keep a Changelog. Раздел `Unreleased` — для текущих изменений.
- Для релизов используем теги Git вида `vMAJOR.MINOR.PATCH` (SemVer). После тега — переносим записи из `Unreleased` в новый релиз в `CHANGELOG.md`.

См. актуальный файл [`CHANGELOG.md`](CHANGELOG.md).

## Лицензия

BSD-3-Clause
