<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "unwanted_information".
 *
 * @property int $id
 * @property string $name
 * @property string|null $content
 * @property string $created_at
 * @property string $edited_at
 *
 * @property UnwantedInformationExample[] $unwantedInformationExamples
 */
class UnwantedInformation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'unwanted_information';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content'], 'default', 'value' => null],
            [['name'], 'required'],
            [['content'], 'string'],
            [['created_at', 'edited_at'], 'safe'],
            [['name'], 'string', 'max' => 256],

            [['id', 'created_at', 'name', 'content'], 'safe', 'on'=>'search'],
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
            'content' => 'Описание',
            'created_at' => 'Дата создания',
            'edited_at' => 'Edited At',
        ];
    }

    /**
     * Gets query for [[UnwantedInformationExamples]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnwantedInformationExamples()
    {
        return $this->hasMany(UnwantedInformationExample::class, ['id_unwanted_information' => 'id']);
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
		]);

        $query->andFilterWhere(['like', $this->tableName().'.name', $this->name]);
        $query->andFilterWhere(['like', $this->tableName().'.content', $this->content]);

		if (!empty($this->created_at)) {
			$c = explode(' - ', $this->created_at);

			$query->andFilterWhere(['between', $this->tableName().'.created_at', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

        return $dataProvider;
	}
}
