<?php

namespace app\modules\admin\controllers;

use app\modules\admin\components\AdminController;
use app\models\User;
use app\models\BotUser;
use app\models\BotHistory;
use Yii;

class CommunicationController extends AdminController
{
    public function actionIndex(): string
    {
        $this->view->title = 'Центр коммуникации';
        $this->view->params['breadcrumbs'][] = 'Центр коммуникации';

        $users = User::find()->orderBy(['id' => SORT_DESC])->limit(20)->all();
        $selectedUserId = (int)Yii::$app->request->get('user_id', $users[0]->id ?? 0);
        $selectedUser = $selectedUserId ? User::findOne($selectedUserId) : null;
        $chatProvider = null;
        if ($selectedUser && $selectedUser->bot_user_id) {
            $chatModel = new BotHistory(['scenario' => 'search']);
            $chatProvider = $chatModel->search(Yii::$app->request->queryParams, (int)$selectedUser->bot_user_id);
        }

        return $this->render('index', [
            'users' => $users,
            'selectedUser' => $selectedUser,
            'chatProvider' => $chatProvider,
        ]);
    }
}


