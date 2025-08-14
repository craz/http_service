<?php

// Подключение по TCP — единообразно работает из контейнера и с хоста.
// Все параметры берём из ENV (см. docker-compose.yml → service web.environment)
return [
    'class' => 'yii\\db\\Connection',
    'dsn' => 'pgsql:host=' . (getenv('DB_HOST') ?: 'pg') . ';port=' . (getenv('DB_PORT') ?: '5432') . ';dbname=' . (getenv('DB_NAME') ?: 'avon_stage'),
    'username' => getenv('DB_USER') ?: 'avon_stage',
    'password' => getenv('DB_PASSWORD') ?: 'pass',
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
