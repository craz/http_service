<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "promoted_topics".
 *
 * @property int $id
 * @property string $name
 * @property string $topic
 * @property bool $disabled
 * @property string $created_at
 * @property string $edited_at
 */
class PromotedTopic extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promoted_topics';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'topic', 'disabled'], 'required'],
            [['disabled'], 'boolean'],
            [['created_at', 'edited_at'], 'safe'],
            [['name', 'topic'], 'string', 'max' => 256],

            [['id', 'created_at', 'name', 'topic', 'disabled'], 'safe', 'on'=>'search'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Заголовок',
            'topic' => 'Тема',
            'disabled' => 'Отключено',
            'created_at' => 'Дата создания',
            'edited_at' => 'Edited At',
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
	        'disabled' => $this->disabled,
		]);

        $query->andFilterWhere(['like', $this->tableName().'.name', $this->name]);
        $query->andFilterWhere(['like', $this->tableName().'.topic', $this->topic]);

		if (!empty($this->created_at)) {
			$c = explode(' - ', $this->created_at);

			$query->andFilterWhere(['between', $this->tableName().'.created_at', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

        return $dataProvider;
	}
}
