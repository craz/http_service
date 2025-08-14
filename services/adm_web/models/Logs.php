<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "logs".
 *
 * @property int $id
 * @property string $point
 * @property float $answer_time
 * @property int $bot_user_id
 * @property string $in_data
 * @property int $model_type
 * @property string $created_at
 * @property string $edited_at
 *
 * @property BotUsers $botUser
 */
class Logs extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['point', 'answer_time', 'bot_user_id', 'in_data', 'model_type'], 'required'],
            [['point', 'in_data'], 'string'],
            [['answer_time'], 'number'],
            [['bot_user_id', 'model_type'], 'default', 'value' => null],
            [['bot_user_id', 'model_type'], 'integer'],
            [['created_at', 'edited_at'], 'safe'],
            [['bot_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => BotUsers::class, 'targetAttribute' => ['bot_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'point' => 'Point',
            'answer_time' => 'Answer Time',
            'bot_user_id' => 'Bot User ID',
            'in_data' => 'In Data',
            'model_type' => 'Model Type',
            'created_at' => 'Created At',
            'edited_at' => 'Edited At',
        ];
    }

    /**
     * Gets query for [[BotUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBotUser()
    {
        return $this->hasOne(BotUsers::class, ['id' => 'bot_user_id']);
    }

}
