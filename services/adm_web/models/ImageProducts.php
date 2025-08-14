<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "image_products".
 *
 * @property int $id
 * @property int $id_images
 * @property int $id_product
 * @property string $created_at
 * @property string $edited_at
 * @property string|null $description
 *
 * @property Images $images
 * @property Product $product
 */
class ImageProducts extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'image_products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'default', 'value' => null],
            [['id_images', 'id_product'], 'required'],
            [['id_images', 'id_product'], 'default', 'value' => null],
            [['id_images', 'id_product'], 'integer'],
            [['created_at', 'edited_at'], 'safe'],
            [['description'], 'string'],
            [['id_images'], 'exist', 'skipOnError' => true, 'targetClass' => Images::class, 'targetAttribute' => ['id_images' => 'id']],
            [['id_product'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['id_product' => 'id']],

            [['id', 'created_at', 'id_images', 'id_product', 'description'], 'safe', 'on'=>'search'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_images' => 'Образ',
            'id_product' => 'Продукт',
            'created_at' => 'Дата создания',
            'edited_at' => 'Edited At',
            'description' => 'Описание',
        ];
    }

    /**
     * Gets query for [[Images]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasOne(Image::class, ['id' => 'id_images']);
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'id_product']);
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
	        'id_images' => $this->id_images,
	        'id_product' => $this->id_product
		]);

        $query->andFilterWhere(['like', $this->tableName().'.description', $this->description]);

		if (!empty($this->created_at)) {
			$c = explode(' - ', $this->created_at);

			$query->andFilterWhere(['between', $this->tableName().'.created_at', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

        return $dataProvider;
	}
}
