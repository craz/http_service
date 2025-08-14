<?php

namespace app\modules\admin\controllers;

use app\models\UserQuiz;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\Func;

/**
 * UserQuizController implements the CRUD actions for UserQuiz model.
 */
class UserQuizController extends \app\modules\admin\components\AdminController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }



    public function beforeAction($action) {

    	if ($action->id == 'export') {
	        $this->enableCsrfValidation = false;
		}

	    return parent::beforeAction($action);
	}

	public function actionExport()
    {
    	//print_r($_POST);
    	//die;

    	$request = \Yii::$app->request;

    	$params = $request->post('params', '');

    	$parts = parse_url($params);

    	$query_params = [];
    	parse_str($parts['path'], $query_params);

    	$model = new UserQuiz();
    	$model->load($query_params);

    	$path = Func::processExportAdminData($model, $query_params, function($items){
			$ret = [
				'header' => [
					'id', 'Дата прохождения', 'Пользователь', 'Ответ 1', 'Ответ 2', 'Ответ 3', 'Ответ 4', 'Ответ 5', 'Результат'
				],
				'data' => [],
			];

			foreach ($items as $i) {

				$ret['data'][] = [
					$i->id,
					$i->created_at,
					$i->user_id,
					$i->answer1,
					$i->answer2,
					$i->answer3,
					$i->answer4,
					$i->answer5,
					$i->result1
				];
			}

			return $ret;
		});

		//return $this->redirect($path);
    }


    public function actionIndex()
    {
    	$model = new UserQuiz(['scenario' => 'search']);
    	$dataProvider = $model->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
        	'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single UserQuiz model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserQuiz model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new UserQuiz();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserQuiz model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserQuiz model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserQuiz model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return UserQuiz the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserQuiz::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
