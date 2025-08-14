<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class BotHistory extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'bot_history';
    }

    public function rules(): array
    {
        return [
            [['id', 'bot_user_id'], 'safe', 'on' => 'search'],
        ];
    }

    public function search(array $params, int $botUserId): ActiveDataProvider
    {
        $query = self::find()->where(['bot_user_id' => $botUserId])->orderBy(['id' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        $this->load($params);
        return $dataProvider;
    }
}


