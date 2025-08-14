<?php

namespace app\modules\admin\components;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\ForbiddenHttpException;

class AdminController extends \yii\web\Controller
{
	public $layout = 'main';
    public $user = null;
    
    public $breadcrumbs = [];
    public $pageTitle = '';
    
    public function init()
    {
    	parent::init();

        Yii::$app->set('user', [
        	'class' => 'yii\web\User',
			'identityClass' => 'app\models\Admin',
			'enableAutoLogin' => true,
			'loginUrl' => '/admin/default/login',
			'identityCookie' => [
				'name' => '_identity-admin',
 			],
	    ]);

        Yii::$app->language = 'ru';

        if (!Yii::$app->user->isGuest) {

            $this->user = Yii::$app->user->identity;

            /*if (!$this->user->active || $this->user->deleted) {
                Yii::$app->user->logout();
                return $this->redirect(['/site/logout']);
            }*/
        }
    }

    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest && $action->id != 'login') {
        	$this->redirect('/admin/default/login');
        }

    	return parent::beforeAction($action);
    }
}
