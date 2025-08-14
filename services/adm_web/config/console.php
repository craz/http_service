<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'avon-console-app',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
    'controllerMap' => [
        // Отключаем интерактивные подтверждения для миграций по умолчанию
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'interactive' => false,
        ],
        /*
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
        ],
        */
    ],
];

// Определяем dev-окружение без обращения к несуществующим константам для линтера
$isDevEnv = (defined('YII_ENV') ? (constant('YII_ENV') === 'dev') : (getenv('YII_ENV') === 'dev'));

if ($isDevEnv && class_exists('yii\gii\Module')) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

if ($isDevEnv && class_exists('yii\debug\Module')) {
    // configuration adjustments for 'dev' environment
    // requires version `2.1.21` of yii2-debug module
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
