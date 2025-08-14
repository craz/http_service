<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $created_at
 * @property int $bot_user_id
 * @property string $name
 * @property int $gender
 * @property string $phone
 * @property int $age
 * @property int $is_registered
 * @property int $using_frequency
 * @property int $favorite_products
 * @property int $refusal_reason
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            [['created_at', 'name', 'phone', 'gender', 'birth_date', 'is_registered', 'using_frequency', 'favorite_products', 'refusal_reason', 'personal_data', 'edited_at', 'skin_type', 'another_flag', 'referent_id',
            	'is_dispatch_subscribed', 'ads_dispatch', 'register_tries_cnt', 'is_intro_processed', 'utm_medium', 'utm_source', 'utm_campaign', 'utm_term', 'utm_content',
            	'is_register_push_sended_1', 'quiz_1_started'], 'safe'],

            [['bot_user_id'], 'required'],

            [['id', 'created_at', 'name', 'gender', 'birth_date', 'is_registered', 'using_frequency', 'favorite_products', 'refusal_reason'], 'safe', 'on'=>'search'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Дата регистрации',
            'bot_user_id' => 'Bot User ID',
            'name' => 'Имя',
            'gender' => 'Пол',
            'phone' => 'Телефон',
            'age' => 'Возраст',
            'birth_date' => 'Дата рождения',
            'is_registered' => 'Зарегистрирован',
            'using_frequency' => 'Частота использования',
            'favorite_products' => 'Любимый тип продукта',
            'refusal_reason' => 'Причина отказа',
        ];
    }

    public function search($params)
    {
    	$query = self::find();


	    $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        $query->andFilterWhere([
	        'id' => $this->id,
	        'is_registered' => $this->is_registered,
	        'using_frequency' => $this->using_frequency,
	        'favorite_products' => $this->favorite_products,
	        'refusal_reason' => $this->refusal_reason,
	        'gender' => $this->gender,
		]);

        $query->andFilterWhere(['like', $this->tableName().'.name', $this->name]);

		if (!empty($this->created_at)) {
			$c = explode(' - ', $this->created_at);

			$query->andFilterWhere(['between', $this->tableName().'.created_at', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

		if (!empty($this->birth_date)) {
			$c = explode(' - ', $this->birth_date);

			$query->andFilterWhere(['between', $this->tableName().'.birth_date', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

        return $dataProvider;
	}
}
