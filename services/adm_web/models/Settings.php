<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property string $title
 * @property string $alias
 * @property string $val
 * @property string $created_at
 * @property string $edited_at
 */
class Settings extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'alias', 'val'], 'required'],
            [['val'], 'string'],
            [['created_at', 'edited_at'], 'safe'],
            [['title', 'alias'], 'string', 'max' => 256],

            [['id', 'title', 'alias', 'val'], 'safe', 'on'=>'search'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'alias' => 'Алиас (slug)',
            'val' => 'Значение',
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
		]);

        $query->andFilterWhere(['like', $this->tableName().'.title', $this->title]);
        $query->andFilterWhere(['like', $this->tableName().'.alias', $this->alias]);
        $query->andFilterWhere(['like', $this->tableName().'.val', $this->val]);

		if (!empty($this->created_at)) {
			$c = explode(' - ', $this->created_at);

			$query->andFilterWhere(['between', $this->tableName().'.created_at', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

        return $dataProvider;
	}
}
