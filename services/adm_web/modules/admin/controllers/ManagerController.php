<?php

namespace app\modules\admin\controllers;

use app\models\Manager;
use app\models\Role;
use app\models\ManagerRole;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class ManagerController extends \app\modules\admin\components\AdminController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    public function actionIndex()
    {
        $model = new Manager(['scenario' => 'search']);
        $dataProvider = $model->search(\Yii::$app->request->queryParams);
        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Manager();

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            $this->syncRoles($model);
            return $this->redirect(['index']);
        }

        return $this->render('form', [
            'model' => $model,
            'roles' => Role::find()->orderBy(['id' => SORT_ASC])->all(),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            $this->syncRoles($model);
            return $this->redirect(['index']);
        }

        return $this->render('form', [
            'model' => $model,
            'roles' => Role::find()->orderBy(['id' => SORT_ASC])->all(),
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id): Manager
    {
        if (($model = Manager::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Запись не найдена');
    }

    private function syncRoles(Manager $manager): void
    {
        $post = \Yii::$app->request->post('roles', []);
        // роли приходят как массив id
        ManagerRole::deleteAll(['manager_id' => $manager->id]);
        if (is_array($post)) {
            $rows = [];
            foreach ($post as $roleId) {
                $rows[] = [$manager->id, (int)$roleId];
            }
            if ($rows) {
                \Yii::$app->db->createCommand()->batchInsert('manager_role', ['manager_id', 'role_id'], $rows)->execute();
            }
        }
    }
}


