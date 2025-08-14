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
 * @property int $holiday_id
 * @property bool $disabled
 *
 * @property Holidays $holiday
 * @property ImageProducts[] $imageProducts
 */
class Image extends \yii\db\ActiveRecord
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
            [['name', 'holiday_id', 'disabled'], 'required'],
            [['created_at', 'edited_at'], 'safe'],
            [['holiday_id'], 'default', 'value' => null],
            [['holiday_id'], 'integer'],
            [['disabled'], 'boolean'],
            [['name'], 'string', 'max' => 256],
            [['holiday_id'], 'exist', 'skipOnError' => true, 'targetClass' => Holidays::class, 'targetAttribute' => ['holiday_id' => 'id']],

            [['id', 'created_at', 'holiday_id', 'name', 'disabled'], 'safe', 'on'=>'search'],
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
            'holiday_id' => 'Праздник',
            'disabled' => 'Отключено',
        ];
    }

    /**
     * Gets query for [[Holiday]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHoliday()
    {
        return $this->hasOne(Holiday::class, ['id' => 'holiday_id']);
    }

    /**
     * Gets query for [[ImageProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImageProducts()
    {
        return $this->hasMany(ImageProducts::class, ['id_images' => 'id']);
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
	        'holiday_id' => $this->holiday_id,
	        'disabled' => $this->disabled
		]);

        $query->andFilterWhere(['like', $this->tableName().'.name', $this->name]);

		if (!empty($this->created_at)) {
			$c = explode(' - ', $this->created_at);

			$query->andFilterWhere(['between', $this->tableName().'.created_at', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

        return $dataProvider;
	}

}
