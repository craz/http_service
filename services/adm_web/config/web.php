<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'ivan-vezet-app',
    'name' => 'ИВАН ВЕЗЕТ',
    'language' => 'ru',
    'sourceLanguage' => 'ru',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'admin/default/index',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
    	'assetManager' => [
			'bundles' => [
				'yii\bootstrap5\BootstrapAsset' => [
					'css' => [],
				],
				'yii\bootstrap5\BootstrapPluginAsset' => [
					'js'=>[]
				],
			],
		],
        'request' => [
        	//'baseUrl' => '/backend',
        	'enableCookieValidation' => false,
            //'cookieValidationKey' => 'mNp-HONNusnslwIcrl3cMmNkqSJFyByt',
            //'enableCsrfValidation'=>false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Admin',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'except' => ['yii\web\HttpException:404'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['aibot'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/info.log',
                ],
            ],
        ],
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            	'admin' => 'admin',

            	'api/<method:([a-zA-Z0-9\-]+)>' => 'api/query',

                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
            ],
        ],

        'reCaptcha' => [
        	'name' => 'reCaptcha',
	        'class' => 'himiklab\yii2\recaptcha\ReCaptchaConfig',
	        'siteKey' => '6LeNEsYqAAAAAC5zB4nD64j9wNRwAKKUbij4MESy',
	        'secretV2' => '6LeNEsYqAAAAAMhD45d66FPG7VzR3-6QqJMJrKkZ',
	    ],
    ],
    'modules' => [
   	 	'admin' => [
            'class' => 'app\modules\admin\AdminModule',
            'layout' => 'main',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV && class_exists('yii\debug\Module')) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

if (YII_ENV_DEV && class_exists('yii\gii\Module')) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        // 'allowedIPs' => ['127.0.0.1'],
    ];
}

return $config;
