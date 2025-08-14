-- Инициализация базы данных для Kintaro Web
-- Этот файл выполняется при первом запуске контейнера MySQL

USE kintaro_web;

-- Создание таблиц (если нужно)
-- Здесь можно добавить SQL команды для создания таблиц

-- Установка прав для пользователя
GRANT ALL PRIVILEGES ON kintaro_web.* TO 'kintaro_user'@'%';
FLUSH PRIVILEGES;
