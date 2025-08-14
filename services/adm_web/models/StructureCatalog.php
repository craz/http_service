<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "structure_catalog".
 *
 * @property int $id
 * @property string $category
 * @property string $question1
 * @property string $question2
 * @property string $created_at
 * @property string $edited_at
 * @property string|null $question3
 */
class StructureCatalog extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'structure_catalog';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question3'], 'default', 'value' => null],
            [['category', 'question1', 'question2'], 'required'],
            [['created_at', 'edited_at'], 'safe'],
            [['question3'], 'string'],
            [['category', 'question1', 'question2'], 'string', 'max' => 256],

            [['id', 'created_at', 'category', 'question1', 'question2', 'question3'], 'safe', 'on'=>'search'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category' => 'Название',
            'question1' => 'Вопрос 1',
            'question2' => 'Вопрос 2',
            'created_at' => 'Дата создания',
            'edited_at' => 'Edited At',
            'question3' => 'Вопрос 3',
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
		]);

        $query->andFilterWhere(['like', $this->tableName().'.question1', $this->question1]);
        $query->andFilterWhere(['like', $this->tableName().'.question2', $this->question2]);
        $query->andFilterWhere(['like', $this->tableName().'.question3', $this->question3]);
        $query->andFilterWhere(['like', $this->tableName().'.category', $this->category]);

		if (!empty($this->created_at)) {
			$c = explode(' - ', $this->created_at);

			$query->andFilterWhere(['between', $this->tableName().'.created_at', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

        return $dataProvider;
	}
}
