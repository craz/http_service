<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "images".
 *
 * @property int $id
 * @property string $name
 * @property string $created_at
 * @property string $edited_at
 * @property int|null $id_images_exemple
 *
 * @property ImageProducts[] $imageProducts
 * @property ImagesExample[] $imagesExamples
 */
class Holiday extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'images';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_images_exemple'], 'default', 'value' => null],
            [['name'], 'required'],
            [['created_at', 'edited_at'], 'safe'],
            [['id_images_exemple'], 'default', 'value' => null],
            [['id_images_exemple'], 'integer'],
            [['name'], 'string', 'max' => 256],

            [['id', 'created_at', 'name'], 'safe', 'on'=>'search'],
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

        $query->andFilterWhere(['like', $this->tableName().'.name', $this->name]);

		if (!empty($this->created_at)) {
			$c = explode(' - ', $this->created_at);

			$query->andFilterWhere(['between', $this->tableName().'.created_at', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

        return $dataProvider;
	}
}
