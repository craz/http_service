<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "holiday_example".
 *
 * @property int $id
 * @property int $id_holiday
 * @property string $content
 * @property string $created_at
 * @property string $edited_at
 * @property string|null $column4
 * @property string|null $column5
 * @property string|null $column6
 * @property string|null $column7
 *
 * @property Holidays $holiday
 */
class HolidayExample extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'holiday_example';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['column4', 'column5', 'column6', 'column7'], 'default', 'value' => null],
            [['id_holiday', 'content'], 'required'],
            [['id_holiday'], 'default', 'value' => null],
            [['id_holiday'], 'integer'],
            [['content'], 'string'],
            [['created_at', 'edited_at'], 'safe'],
            [['column4', 'column5', 'column6', 'column7'], 'string', 'max' => 50],
            [['id_holiday'], 'exist', 'skipOnError' => true, 'targetClass' => Holidays::class, 'targetAttribute' => ['id_holiday' => 'id']],

            [['id', 'created_at', 'id_holiday', 'content'], 'safe', 'on'=>'search'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_holiday' => 'Праздник',
            'content' => 'Контент для подбора',
            'created_at' => 'Дата создания',
            'edited_at' => 'Edited At',
            'column4' => 'Column4',
            'column5' => 'Column5',
            'column6' => 'Column6',
            'column7' => 'Column7',
        ];
    }

    /**
     * Gets query for [[Holiday]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHoliday()
    {
        return $this->hasOne(Holiday::class, ['id' => 'id_holiday']);
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
	        'id_holiday' => $this->id_holiday,
		]);

        $query->andFilterWhere(['like', $this->tableName().'.content', $this->content]);

		if (!empty($this->created_at)) {
			$c = explode(' - ', $this->created_at);

			$query->andFilterWhere(['between', $this->tableName().'.created_at', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

        return $dataProvider;
	}
}
