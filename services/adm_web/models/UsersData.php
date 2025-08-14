<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users_data".
 *
 * @property int $id
 * @property int $user_id
 * @property int $push_registered_cnt
 * @property int $push_phone_cnt
 * @property int $push_registered_and_phone_cnt
 */
class UsersData extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['push_registered_and_phone_cnt'], 'default', 'value' => 0],
            [['user_id'], 'required'],
            [['user_id', 'push_registered_cnt', 'push_phone_cnt', 'push_registered_and_phone_cnt'], 'default', 'value' => null],
            [['user_id', 'push_registered_cnt', 'push_phone_cnt', 'push_registered_and_phone_cnt'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'push_registered_cnt' => 'Push Registered Cnt',
            'push_phone_cnt' => 'Push Phone Cnt',
            'push_registered_and_phone_cnt' => 'Push Registered And Phone Cnt',
        ];
    }

}
