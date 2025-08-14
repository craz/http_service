<?php

namespace app\modules\admin\controllers;

use app\modules\admin\components\AdminController;
use app\models\RoutingRule;
use Yii;

class ContextController extends AdminController
{
    public function actionIndex(int $model = 1): string
    {
        $model = max(1, min(3, (int)$model));
        $search = new RoutingRule(['scenario' => 'search']);
        $dataProvider = $search->search(Yii::$app->request->queryParams, $model);
        return $this->render('index', [
            'activeModel' => $model,
            'searchModel' => $search,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate(int $model = 1)
    {
        $rule = new RoutingRule(['model_number' => $model]);
        if ($rule->load(Yii::$app->request->post()) && $rule->save()) {
            return $this->redirect(['index', 'model' => $model]);
        }
        return $this->render('form', ['model' => $rule, 'activeModel' => $model]);
    }

    public function actionUpdate(int $id)
    {
        $rule = RoutingRule::findOne($id);
        $model = $rule->model_number;
        if ($rule->load(Yii::$app->request->post()) && $rule->save()) {
            return $this->redirect(['index', 'model' => $model]);
        }
        return $this->render('form', ['model' => $rule, 'activeModel' => $model]);
    }

    public function actionDelete(int $id)
    {
        $rule = RoutingRule::findOne($id);
        if ($rule) {
            $m = $rule->model_number;
            $rule->delete();
            return $this->redirect(['index', 'model' => $m]);
        }
        return $this->redirect(['index']);
    }
}


