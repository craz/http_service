<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "dispatches".
 *
 * @property int $id
 * @property string $name
 * @property string $text
 * @property int $gender
 * @property int $age_min
 * @property int $age_max
 * @property int $is_registered
 * @property int $using_frequency
 * @property int $favorite_products
 * @property int $refusal_reason
 * @property string $date_start
 * @property int $is_sended
 * @property string|null $users_ids
 * @property string $created_at
 * @property string $edited_at
 *
 * @property DispatchesTrigger[] $dispatchesTriggers
 */
class Dispatch extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dispatches';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'text', 'date_start'], 'required'],
            [['text', 'users_ids'], 'string'],
            [['gender', 'age_min', 'age_max', 'is_registered', 'using_frequency', 'favorite_products', 'refusal_reason', 'is_sended'], 'default', 'value' => null],
            [['gender', 'age_min', 'age_max', 'is_registered', 'using_frequency', 'favorite_products', 'refusal_reason', 'is_sended'], 'integer'],
            [['date_start', 'created_at', 'edited_at', 'is_send_text_and_media_separately', 'text_type', 'is_ads_dispatch'], 'safe'],
            [['name'], 'string', 'max' => 256],

            [['id', 'created_at', 'name', 'text', 'gender', 'age_min', 'age_max', 'is_registered', 'using_frequency', 'favorite_products', 'refusal_reason', 'date_start', 'is_sended', 'users_ids', 'is_send_text_and_media_separately', 'text_type', 'is_ads_dispatch'], 'safe', 'on'=>'search'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'text' => 'Текст рассылки',
            'gender' => 'Пол',
            'age_min' => 'Возраст от',
            'age_max' => 'Возраст до',
            'is_registered' => 'Зарегистрирован',
            'using_frequency' => 'Частота использования',
            'favorite_products' => 'Любимый тип продукта',
            'refusal_reason' => 'Причина отказа',
            'date_start' => 'Время рассылки',
            'is_sended' => 'Отправлена',
            'users_ids' => 'Пользователи',
            'created_at' => 'Дата создания',
            'edited_at' => 'Edited At',
            'is_send_text_and_media_separately' => 'Отправлять медиа и текст в 2 сообщениях (раздельно)',
            'text_type' => 'Тип контента',
            'is_ads_dispatch' => 'Подписка на рекламную рассылку',
        ];
    }

    /**
     * Gets query for [[DispatchesTriggers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDispatchesTriggers()
    {
        return $this->hasMany(DispatchesTrigger::class, ['dispatch_id' => 'id']);
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
	        'is_sended' => $this->is_sended,
	        'age_min' => $this->age_min,
	        'age_max' => $this->age_max,
	        'is_send_text_and_media_separately' => $this->is_send_text_and_media_separately,
	        'text_type' => $this->text_type,
	        'is_ads_dispatch' => $this->is_ads_dispatch,
		]);

        $query->andFilterWhere(['like', $this->tableName().'.name', $this->name]);

		if (!empty($this->created_at)) {
			$c = explode(' - ', $this->created_at);

			$query->andFilterWhere(['between', $this->tableName().'.created_at', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

		if (!empty($this->date_start)) {
			$c = explode(' - ', $this->date_start);

			$query->andFilterWhere(['between', $this->tableName().'.date_start', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

        return $dataProvider;
	}
}
