<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . (getenv('DB_HOST') ?: 'db') . ';dbname=' . (getenv('DB_NAME') ?: 'kintaro_web'),
    'username' => getenv('DB_USER') ?: 'kintaro_user',
    'password' => getenv('DB_PASSWORD') ?: 'kintaro_password',
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
