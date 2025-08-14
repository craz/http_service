<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Exception;

class DbMockController extends Controller
{
    /**
     * Полный сброс БД и наполнение минимальными демо-данными.
     * ВНИМАНИЕ: уничтожает все пользовательские данные в текущей схеме.
     */
    public function actionDemo(): int
    {
        $db = Yii::$app->db;

        $transaction = $db->beginTransaction();

        try {
            // Отключаем проверки внешних ключей (для PostgreSQL)
            $db->createCommand("SET session_replication_role = 'replica';")->execute();

            // Список таблиц в порядке безопасного удаления
            $tables = [
                'image_products',
                'dispatches',
                'dispatch_pushes',
                'users_quiz',
                'users_data',
                'users',
                'bot_history',
                'logs',
                'bot_users',
                'groups_example',
                'images',
                'product',
                'structure_catalog',
                'unwanted_information_example',
                'unwanted_information',
                'promoted_topics',
                'settings',
                'admins',
                'test',
                'admin',
            ];

            foreach ($tables as $table) {
                $db->createCommand("DROP TABLE IF EXISTS \"$table\" CASCADE")->execute();
            }

            // Создание таблиц (минимальная схема для админки)
            $sql = [];

            $sql[] = <<<SQL
CREATE TABLE admins (
    id SERIAL PRIMARY KEY,
    login VARCHAR(256) NOT NULL,
    password VARCHAR(256) NOT NULL,
    role VARCHAR(64) NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE settings (
    id SERIAL PRIMARY KEY,
    title VARCHAR(256) NOT NULL,
    alias VARCHAR(256) NOT NULL,
    val TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE structure_catalog (
    id SERIAL PRIMARY KEY,
    category VARCHAR(256) NOT NULL,
    question1 VARCHAR(256) NOT NULL,
    question2 VARCHAR(256) NOT NULL,
    question3 TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE product (
    id INTEGER PRIMARY KEY,
    name TEXT,
    category TEXT,
    disabled BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE images (
    id SERIAL PRIMARY KEY,
    name VARCHAR(256) NOT NULL,
    holiday_id INTEGER,
    disabled BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE image_products (
    id SERIAL PRIMARY KEY,
    id_images INTEGER NOT NULL REFERENCES images(id) ON DELETE CASCADE,
    id_product INTEGER NOT NULL REFERENCES product(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE groups_example (
    id SERIAL PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    edit_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_class INTEGER NOT NULL REFERENCES structure_catalog(id) ON DELETE CASCADE,
    name_class TEXT NOT NULL,
    product TEXT,
    assignment TEXT,
    description TEXT
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE promoted_topics (
    id SERIAL PRIMARY KEY,
    name VARCHAR(256) NOT NULL,
    topic VARCHAR(256) NOT NULL,
    disabled BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE unwanted_information (
    id SERIAL PRIMARY KEY,
    name VARCHAR(256) NOT NULL,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE unwanted_information_example (
    id SERIAL PRIMARY KEY,
    id_unwanted_information INTEGER NOT NULL REFERENCES unwanted_information(id) ON DELETE CASCADE,
    text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    edit_at VARCHAR(50)
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE bot_users (
    id SERIAL PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tg_id BIGINT NOT NULL,
    tg_login VARCHAR(128),
    user_id INTEGER
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    bot_user_id INTEGER NOT NULL,
    name VARCHAR(255),
    gender INTEGER,
    phone VARCHAR(64),
    age INTEGER,
    birth_date TIMESTAMP,
    is_registered INTEGER,
    using_frequency INTEGER,
    favorite_products INTEGER,
    refusal_reason INTEGER,
    personal_data BOOLEAN DEFAULT FALSE,
    edited_at TIMESTAMP,
    skin_type TEXT,
    another_flag BOOLEAN DEFAULT FALSE,
    referent_id INTEGER,
    is_dispatch_subscribed INTEGER DEFAULT 0,
    ads_dispatch INTEGER DEFAULT 0,
    register_tries_cnt INTEGER DEFAULT 0,
    is_intro_processed INTEGER DEFAULT 0,
    utm_medium TEXT,
    utm_source TEXT,
    utm_campaign TEXT,
    utm_term TEXT,
    utm_content TEXT,
    is_register_push_sended_1 INTEGER DEFAULT 0,
    quiz_1_started INTEGER DEFAULT 0
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE users_quiz (
    id SERIAL PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    answer1 TEXT,
    answer2 TEXT,
    answer3 TEXT,
    answer4 TEXT,
    answer5 TEXT,
    result1 TEXT
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE users_data (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    push_registered_cnt INTEGER DEFAULT 0,
    push_phone_cnt INTEGER DEFAULT 0,
    push_registered_and_phone_cnt INTEGER DEFAULT 0
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE bot_history (
    id SERIAL PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    bot_user_id INTEGER NOT NULL,
    is_bot INTEGER NOT NULL,
    in_data TEXT NOT NULL,
    out_data TEXT,
    images TEXT,
    action_url VARCHAR(256),
    params TEXT,
    model_type INTEGER,
    tg_api_id VARCHAR(64),
    tg_out_data TEXT,
    answer_time NUMERIC,
    system_flag INTEGER,
    reply_to_history_id INTEGER
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE logs (
    id SERIAL PRIMARY KEY,
    point TEXT NOT NULL,
    answer_time NUMERIC NOT NULL,
    bot_user_id INTEGER NOT NULL REFERENCES bot_users(id) ON DELETE CASCADE,
    in_data TEXT NOT NULL,
    model_type INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE dispatch_pushes (
    id SERIAL PRIMARY KEY,
    body TEXT NOT NULL,
    daytype INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SQL;

            $sql[] = <<<SQL
CREATE TABLE dispatches (
    id SERIAL PRIMARY KEY,
    name VARCHAR(256) NOT NULL,
    text TEXT NOT NULL,
    gender INTEGER,
    age_min INTEGER,
    age_max INTEGER,
    is_registered INTEGER,
    using_frequency INTEGER,
    favorite_products INTEGER,
    refusal_reason INTEGER,
    date_start TIMESTAMP NOT NULL,
    is_sended INTEGER DEFAULT 0,
    users_ids TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_send_text_and_media_separately INTEGER,
    text_type INTEGER,
    is_ads_dispatch INTEGER
);
SQL;

            foreach ($sql as $stmt) {
                $db->createCommand($stmt)->execute();
            }

            // Демо-данные
            $db->createCommand()->batchInsert('admins', ['login', 'password', 'role'], [
                ['admin', 'admin', 'admin'],
            ])->execute();

            $db->createCommand()->batchInsert('settings', ['title', 'alias', 'val'], [
                ['О проекте', 'about', 'Демо стенд админки'],
                ['Контакты', 'contacts', 'demo@example.com'],
            ])->execute();

            $db->createCommand()->batchInsert('structure_catalog', ['category', 'question1', 'question2', 'question3'], [
                ['Уход за кожей', 'Тип кожи?', 'Основная задача?', 'Есть ли аллергии?'],
            ])->execute();

            // Продукты
            $db->createCommand()->batchInsert('product', ['id', 'name', 'category', 'disabled'], [
                [1001, 'Крем увлажняющий', 'Уход за кожей', false],
                [1002, 'Сыворотка витамин C', 'Уход за кожей', false],
            ])->execute();

            // Изображения и связь
            $db->createCommand()->insert('images', ['name' => 'demo_image_1.jpg', 'disabled' => false])->execute();
            $imageId = (int)$db->getLastInsertID();
            $db->createCommand()->insert('image_products', ['id_images' => $imageId, 'id_product' => 1001, 'description' => 'Образ к увлажняющему крему'])->execute();

            // Пример группы
            $db->createCommand()->insert('groups_example', [
                'id_class' => 1,
                'name_class' => 'Уход за кожей',
                'product' => 'Крем',
                'assignment' => 'Увлажнение',
                'description' => 'Базовый набор',
            ])->execute();

            // Промо темы
            $db->createCommand()->insert('promoted_topics', [
                'name' => 'Летний уход',
                'topic' => 'summer_care',
                'disabled' => false,
            ])->execute();

            // Нежелательная информация + примеры
            $db->createCommand()->insert('unwanted_information', [
                'name' => 'Нецензурная лексика',
                'content' => 'Фильтрация нежелательных выражений',
            ])->execute();
            $unwId = (int)$db->getLastInsertID();
            $db->createCommand()->insert('unwanted_information_example', [
                'id_unwanted_information' => $unwId,
                'text' => 'Пример нежелательной фразы',
            ])->execute();

            // Пользователи/бот
            $db->createCommand()->insert('bot_users', [
                'tg_id' => 123456789,
                'tg_login' => 'demo_user',
            ])->execute();
            $botUserId = (int)$db->getLastInsertID();

            $db->createCommand()->insert('users', [
                'bot_user_id' => $botUserId,
                'name' => 'Иван',
                'gender' => 1,
                'phone' => '79991234567',
                'age' => 28,
                'is_registered' => 1,
                'is_intro_processed' => 1,
                'is_dispatch_subscribed' => 1,
                'ads_dispatch' => 1,
            ])->execute();
            $userId = (int)$db->getLastInsertID();

            $db->createCommand()->insert('users_quiz', [
                'user_id' => $userId,
                'answer1' => 'Нормальная кожа',
                'result1' => 'Рекомендован крем 1001',
            ])->execute();

            $db->createCommand()->insert('users_data', [
                'user_id' => $userId,
                'push_registered_cnt' => 1,
                'push_phone_cnt' => 0,
                'push_registered_and_phone_cnt' => 0,
            ])->execute();

            // Логи и история (минимум)
            $db->createCommand()->insert('logs', [
                'point' => 'demo',
                'answer_time' => 0.01,
                'bot_user_id' => $botUserId,
                'in_data' => '{}',
                'model_type' => 0,
            ])->execute();

            $db->createCommand()->insert('bot_history', [
                'bot_user_id' => $botUserId,
                'is_bot' => 1,
                'in_data' => '{"start":true}',
            ])->execute();

            // Информационные рассылки (для daytype 1/4/5)
            $db->createCommand()->batchInsert('dispatch_pushes', ['body', 'daytype'], [
                ['Демо сообщение дня 1', 1],
                ['Демо сообщение дня 4', 4],
                ['Демо сообщение дня 5', 5],
            ])->execute();

            // Рассылка
            $db->createCommand()->insert('dispatches', [
                'name' => 'Демо рассылка',
                'text' => 'Текст демо рассылки',
                'date_start' => date('Y-m-d H:i:s'),
                'is_sended' => 0,
            ])->execute();

            // Включаем проверки внешних ключей обратно
            $db->createCommand("SET session_replication_role = 'origin';")->execute();

            $transaction->commit();

            $this->stdout("Готово: база очищена и заполнена демо-данными.\n");
            return ExitCode::OK;
        } catch (Exception $e) {
            $transaction->rollBack();
            // Возвращаем роль на случай ошибки
            try { $db->createCommand("SET session_replication_role = 'origin';")->execute(); } catch (\Throwable $t) {}
            $this->stderr("Ошибка: " . $e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}


