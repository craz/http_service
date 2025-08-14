<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tgbot_history".
 *
 * @property int $id
 * @property string $created
 * @property int $user_id
 * @property int $inbox
 * @property string $text
 * @property string|null $out_data
 * @property string $action_id
 * @property string|null $images
 */
class TgbotHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'images', 'params', 'model_type', 'action_url', 'buttons', 'tg_api_id', 'tg_out_data', 'answer_time', 'system_flag', 'reply_to_history_id'], 'safe'],
            [['bot_user_id', 'is_bot', 'in_data'], 'required'],
            [['bot_user_id', 'is_bot'], 'integer'],
            [['in_data', 'out_data', 'images'], 'string'],
            [['action_url'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created' => 'Created',
            'user_id' => 'User ID',
            'inbox' => 'Inbox',
            'text' => 'Text',
            'out_data' => 'Out Data',
            'action_id' => 'Action ID',
            'images' => 'Images',
        ];
    }
}
