<?php

namespace app\models;

use yii\db\ActiveRecord;

class BotUser extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'bot_users';
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['bot_user_id' => 'id']);
    }
}


