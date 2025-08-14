<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "users_quiz".
 *
 * @property int $id
 * @property string $created_at
 * @property string $edited_at
 * @property int $user_id
 * @property string|null $answer1
 * @property string|null $answer2
 * @property string|null $answer3
 * @property string|null $answer4
 * @property string|null $result1
 */
class UserQuiz extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_quiz';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['answer1', 'answer2', 'answer3', 'answer4', 'answer5', 'result1'], 'default', 'value' => null],
            [['created_at', 'edited_at'], 'safe'],
            [['user_id'], 'required'],
            [['user_id'], 'default', 'value' => null],
            [['user_id'], 'integer'],
            [['answer1', 'answer2', 'answer3', 'answer4', 'answer5', 'result1'], 'string'],

            [['id', 'created_at', 'answer1', 'answer2', 'answer3', 'answer4', 'answer5', 'result1'], 'safe', 'on'=>'search'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Дата прохождения',
            'edited_at' => 'Edited At',
            'user_id' => 'Пользователь',
            'answer1' => 'Ответ 1',
            'answer2' => 'Ответ 2',
            'answer3' => 'Ответ 3',
            'answer4' => 'Ответ 4',
            'answer5' => 'Ответ 5',
            'result1' => 'Результат',
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
	        'user_id' => $this->user_id
		]);

        $query->andFilterWhere(['like', $this->tableName().'.answer1', $this->answer1]);
        $query->andFilterWhere(['like', $this->tableName().'.answer2', $this->answer2]);
        $query->andFilterWhere(['like', $this->tableName().'.answer3', $this->answer3]);
        $query->andFilterWhere(['like', $this->tableName().'.answer4', $this->answer4]);
        $query->andFilterWhere(['like', $this->tableName().'.answer5', $this->answer5]);
        $query->andFilterWhere(['like', $this->tableName().'.result1', $this->result1]);

		if (!empty($this->created_at)) {
			$c = explode(' - ', $this->created_at);

			$query->andFilterWhere(['between', $this->tableName().'.created_at', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

        return $dataProvider;
	}
}
