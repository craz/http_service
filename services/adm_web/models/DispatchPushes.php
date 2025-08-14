<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "dispatch_pushes".
 *
 * @property int $id
 * @property string $body
 * @property int $daytype
 */
class DispatchPushes extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dispatch_pushes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['body', 'daytype'], 'required'],
            [['body'], 'string'],
            [['daytype'], 'default', 'value' => null],
            [['daytype'], 'integer'],

            [['id', 'daytype', 'body'], 'safe', 'on'=>'search'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'body' => 'Текст',
            'daytype' => 'День запуска',
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
	        'daytype' => $this->daytype,
		]);

        $query->andFilterWhere(['like', $this->tableName().'.body', $this->body]);

        return $dataProvider;
	}
}
