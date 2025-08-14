<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "unwanted_information_example".
 *
 * @property int $id
 * @property int $id_unwanted_information
 * @property string $text
 * @property string $created_at
 * @property string $edited_at
 * @property string|null $edit_at
 *
 * @property UnwantedInformation $unwantedInformation
 */
class UnwantedInformationExample extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'unwanted_information_example';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['edit_at'], 'default', 'value' => null],
            [['id_unwanted_information', 'text'], 'required'],
            [['id_unwanted_information'], 'default', 'value' => null],
            [['id_unwanted_information'], 'integer'],
            [['text'], 'string'],
            [['created_at', 'edited_at'], 'safe'],
            [['edit_at'], 'string', 'max' => 50],
            [['id_unwanted_information'], 'exist', 'skipOnError' => true, 'targetClass' => UnwantedInformation::class, 'targetAttribute' => ['id_unwanted_information' => 'id']],

            [['id', 'created_at', 'id_unwanted_information', 'text'], 'safe', 'on'=>'search'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_unwanted_information' => 'Категория',
            'text' => 'Текст',
            'created_at' => 'Дата создания',
            'edited_at' => 'Edited At',
            'edit_at' => 'Edit At',
        ];
    }

    /**
     * Gets query for [[UnwantedInformation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnwantedInformation()
    {
        return $this->hasOne(UnwantedInformation::class, ['id' => 'id_unwanted_information']);
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
	        'id_unwanted_information' => $this->id_unwanted_information
		]);

        $query->andFilterWhere(['like', $this->tableName().'.text', $this->text]);

		if (!empty($this->created_at)) {
			$c = explode(' - ', $this->created_at);

			$query->andFilterWhere(['between', $this->tableName().'.created_at', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

        return $dataProvider;
	}
}
