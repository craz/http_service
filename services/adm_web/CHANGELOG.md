# Changelog

All notable changes to this project will be documented in this file.

The format is based on Keep a Changelog and this project adheres to Semantic Versioning.

## [Unreleased]

### Added

- Автоматическое AI‑ревью pull request'ов через GitHub Actions (`CodiumAI/pr-agent`)
- Пункт меню админки «Менеджеры» (`/admin/manager`) для управления менеджерами
- Новый раздел «Центр коммуникации» (`/admin/communication`): базовый каркас страницы (очередь слева, карточка клиента справа)

### Changed

- Переведен базовый Docker образ на `php:8.3-apache` для совместимости с зависимостями Symfony 7.x
- Добавлены настройки для PHP валидатора и Intelephense: путь к `php` теперь указывается через `/.vscode/settings.json`

### Fixed

- Исправлена ошибка запуска админки: таблица `admins` могла отсутствовать. Добавлена миграция `m250811_092500_ensure_admins_table`, которая:
  - переименовывает устаревшую таблицу `admin` в `admins` при наличии,
  - добавляет недостающие столбцы и уникальный индекс по `login`.
- Настроено подключение к БД для docker-compose: `config/db.php` теперь читает `DB_HOST/DB_PORT/DB_NAME/DB_USER/DB_PASSWORD` из `web.environment`; миграции запускаются внутри контейнера `web`

### DevOps

- Отключены подтверждения при запуске миграций по умолчанию: настройка `controllerMap.migrate.interactive=false` в `config/console.php`

## [0.1.1] - 2025-08-10

### Added

- Брендирование: логотип `web/uploads/logo.*` на экране логина и в шапке админки
- Оранжевая цветовая схема для шапки и кнопки логина
- Документация по стилю коммитов и changelog в `README.md`
- Начат `CHANGELOG.md`

### Changed

- Переименование приложения на «ИВАН ВЕЗЕТ»
- Верстка страницы логина под макет

### Fixed

- Импорты и типы в `DefaultController`

## [0.1.0] - 2025-08-10

### Added

- Базовая инфраструктура проекта на Yii2, модуль админки
- Docker конфигурация и скрипты запуска
- Миграции и модель `Admin`
