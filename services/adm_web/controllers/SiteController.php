<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

use app\models\Func;

use app\components\TelegramBot;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function beforeAction($action) {

    	if ($action->id == 'tgbot') {
	        $this->enableCsrfValidation = false;
		}

	    return parent::beforeAction($action);
	}

    public function actionIndex()
    {
        return $this->render('index', [
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        setcookie("_identity-admin", "", -1, "/");
        return $this->goHome();
    }

    public function actionTgbot()
    {
    	$bot = new TelegramBot();
		$bot->init();
    }

    public function actionTest()
    {
    	die;
    	echo date('Y-m-d H:i:s');
    	die;
    }
}
