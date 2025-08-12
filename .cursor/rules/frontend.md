---
auto_apply: true
apply_to:
  - "**/*.js"
  - "**/*.jsx"
  - "**/*.ts"
  - "**/*.tsx"
  - "**/*.css"
  - "**/*.scss"
  - "**/*.sass"
  - "**/*.less"
  - "package.json"
  - "vite.config.*"
  - "webpack.config.*"
priority: 30
description: "Правила для фронтенда"
---

# Фронтенд

- Менеджер пакетов: pnpm/npm/yarn — выбрать и зафиксировать.
- Линтеры: ESLint + Prettier, конфигурации в репозитории.
- Сборка: Vite/Webpack, `.env` для конфигурации.
- CI: проверка типов (tsc) и линтов на PR.
