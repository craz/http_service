# Руководство по вкладу

## Ветки
- Основная: `main`
- Именование рабочих веток: `type/short-kebab[-scope]`
  - Примеры: `feat/rules-autoload`, `fix/docker-build`, `docs/readme-rules`
  - Типы: `feat`, `fix`, `docs`, `chore`, `refactor`, `perf`, `test`, `build`, `ci`

## Коммиты (Conventional Commits)
```
<type>(<scope>): <краткое описание>

[подробности]
```
- Примеры: `feat(rules): add auto_apply metadata`, `fix(ci): correct workflow path`

## CHANGELOG
- Формат: Keep a Changelog, SemVer
- Перед PR обновляйте раздел `Unreleased` с категориями: Added/Changed/Fixed/Removed/Deprecated/Security

## Процесс работы
1. Отведите ветку от `main`
2. Внесите изменения и обновите `CHANGELOG.md`
3. Сделайте коммиты по правилам Conventional Commits
4. По завершении функциональности ветки — создайте PR в `main` (ассистент создаст автоматически, если не указано иное)
5. Дождитесь ревью и мерджа

## Шаблон коммита (необязательно)
- Используйте `.gitmessage.txt` как шаблон:
```
<type>(<scope>): <subject>

[body]

Refs: #issue
```
- Настроить: `git config commit.template .gitmessage.txt`
