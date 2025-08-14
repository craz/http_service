<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

use app\models\StructureCatalog;


/**
 * This is the model class for table "groups_example".
 *
 * @property int $id
 * @property string $created_at
 * @property string $edit_at
 * @property int $id_class
 * @property string $name_class
 * @property string|null $product
 * @property string|null $assignment
 * @property string|null $description
 */
class GroupsExample extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'groups_example';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product', 'assignment', 'description'], 'default', 'value' => null],
            [['created_at', 'edit_at'], 'safe'],
            [['id_class', 'name_class'], 'required'],
            [['id_class'], 'default', 'value' => null],
            [['id_class'], 'integer'],
            [['name_class', 'product', 'assignment', 'description'], 'string'],

            [['id', 'created_at', 'product', 'assignment', 'description', 'id_class', 'name_class'], 'safe', 'on'=>'search'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Дата создания',
            'edit_at' => 'Edit At',
            'id_class' => 'Группа продуктов',
            'name_class' => 'Название группы продуктов',
            'product' => 'Тип продукта',
            'assignment' => 'Назначение',
            'description' => 'Описание',
        ];
    }

    public function getStructureCatalog()
    {
        return $this->hasOne(StructureCatalog::class, ['id' => 'id_class']);
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
	        'id_class' => $this->id_class,
		]);

        $query->andFilterWhere(['like', $this->tableName().'.product', $this->product]);
        $query->andFilterWhere(['like', $this->tableName().'.assignment', $this->assignment]);
        $query->andFilterWhere(['like', $this->tableName().'.description', $this->description]);
        $query->andFilterWhere(['like', $this->tableName().'.name_class', $this->name_class]);

		if (!empty($this->created_at)) {
			$c = explode(' - ', $this->created_at);

			$query->andFilterWhere(['between', $this->tableName().'.created_at', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

        return $dataProvider;
	}
}
