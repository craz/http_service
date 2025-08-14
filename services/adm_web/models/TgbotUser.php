<?php

namespace app\models;

use Yii;

class TgbotUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'queued_messages_ids', 'bdate_iteration'], 'safe'],
            [['tg_id'], 'required'],
            [['user_id'], 'integer'],
            [['tg_login'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created',
            'tg_id' => 'Tg ID',
            'tg_login' => 'Tg Login',
            'user_id' => 'Crm ID',
        ];
    }
}
