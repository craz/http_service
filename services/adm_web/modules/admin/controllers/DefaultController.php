<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\bootstrap5\ActiveForm;
use app\models\Admin;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends \app\modules\admin\components\AdminController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        $this->layout = 'login';

        $model = new Admin();

        $r = Yii::$app->request;

        if ($r->post('ajax')=='login-form' && $model->load($r->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load($r->post())) {

        	$admin = Admin::find()->where(['login' => $model->login, 'password' => $model->password])->one();

        	if ($admin instanceof Admin) {
        		Yii::$app->user->login($admin, 3600 * 24 * 30);
    	            return $this->redirect(['index']);
    		}

			$model->addError('login', 'Неверные логин или пароль');
			$model->addError('password', 'Неверные логин или пароль');
        }

        return $this->render('login', [
            'model'=>$model,
        ]);
    }
}
