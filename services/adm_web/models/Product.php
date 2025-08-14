<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string|null $summary_text
 * @property string|null $name
 * @property string|null $category
 * @property string|null $question
 * @property string|null $answer
 * @property string|null $group_name
 * @property string|null $segment
 * @property string|null $advantages
 * @property string|null $main_property
 * @property string|null $other_information
 * @property string|null $link
 * @property string|null $description
 * @property string|null $description_for_site
 * @property string|null $description_long
 * @property string|null $description_medium
 * @property string|null $description_short
 * @property string|null $ingredients
 * @property string|null $use_application
 * @property string|null $shades_name
 * @property string|null $brand
 * @property string|null $stamps
 * @property string|null $disclamer
 * @property string|null $fragrance
 * @property string|null $shade
 * @property string|null $profile_name
 * @property string|null $subbrand
 * @property int|null $weight
 * @property string|null $compound
 * @property string|null $shades_code
 * @property string|null $site_name_short
 * @property string|null $certificates
 * @property int|null $volume
 * @property string|null $aroma_notes
 * @property string|null $aroma_text
 * @property string|null $perfumer_name
 * @property string|null $perfumer_description
 * @property string|null $perfumer_text
 * @property string|null $cult_stamps_name
 * @property string|null $cult_stamps_text
 * @property string|null $cult_disclamer
 * @property int|null $cult_position
 * @property string|null $stamps_name
 * @property string|null $stamps_text
 * @property string|null $stamps_disclamer
 * @property string|null $ingredients_main
 * @property string|null $variants
 * @property string|null $set_components
 * @property string|null $search_tags
 * @property string|null $brochure_set_components
 * @property bool $disabled
 * @property string $created_at
 * @property string $edited_at
 * @property string|null $answer1
 * @property string|null $answer2
 * @property string|null $answer3
 * @property string|null $group_new
 *
 * @property ImageProducts[] $imageProducts
 */
class Product extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['summary_text', 'name', 'category', 'question', 'answer', 'group_name', 'segment', 'advantages', 'main_property', 'other_information', 'link', 'description', 'description_for_site', 'description_long', 'description_medium', 'description_short', 'ingredients', 'use_application', 'shades_name', 'brand', 'stamps', 'disclamer', 'fragrance', 'shade', 'profile_name', 'subbrand', 'weight', 'compound', 'shades_code', 'site_name_short', 'certificates', 'volume', 'aroma_notes', 'aroma_text', 'perfumer_name', 'perfumer_description', 'perfumer_text', 'cult_stamps_name', 'cult_stamps_text', 'cult_disclamer', 'cult_position', 'stamps_name', 'stamps_text', 'stamps_disclamer', 'ingredients_main', 'variants', 'set_components', 'search_tags', 'brochure_set_components', 'answer1', 'answer2', 'answer3', 'group_new'], 'default', 'value' => null],
            [['id', 'disabled'], 'required'],
            [['id', 'weight', 'volume', 'cult_position'], 'default', 'value' => null],
            [['id', 'weight', 'volume', 'cult_position'], 'integer'],
            [['summary_text', 'name', 'category', 'question', 'answer', 'group_name', 'segment', 'advantages', 'main_property', 'other_information', 'link', 'description', 'description_for_site', 'description_long', 'description_medium', 'description_short', 'ingredients', 'use_application', 'shades_name', 'brand', 'stamps', 'disclamer', 'fragrance', 'shade', 'profile_name', 'subbrand', 'compound', 'shades_code', 'site_name_short', 'certificates', 'aroma_notes', 'aroma_text', 'perfumer_name', 'perfumer_description', 'perfumer_text', 'cult_stamps_name', 'cult_stamps_text', 'cult_disclamer', 'stamps_name', 'stamps_text', 'stamps_disclamer', 'ingredients_main', 'variants', 'set_components', 'search_tags', 'brochure_set_components', 'answer1', 'answer2', 'answer3', 'group_new'], 'string'],
            [['disabled'], 'boolean'],
            [['created_at', 'edited_at'], 'safe'],
            [['id'], 'unique'],

            [['id', 'created_at', 'name', 'category', 'disabled', 'group_name', 'segment', 'link', 'answer1', 'answer2', 'answer3', 'group_new'], 'safe', 'on'=>'search'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'summary_text' => 'Summary Text',
            'name' => 'Название',
            'category' => 'Категория',
            'question' => 'Question',
            'answer' => 'Ответ',
            'group_name' => 'Группа',
            'segment' => 'Сегмент',
            'advantages' => 'Advantages',
            'main_property' => 'Main Property',
            'other_information' => 'Other Information',
            'link' => 'Ссылка',
            'description' => 'Description',
            'description_for_site' => 'Description For Site',
            'description_long' => 'Description Long',
            'description_medium' => 'Description Medium',
            'description_short' => 'Description Short',
            'ingredients' => 'Ingredients',
            'use_application' => 'Use Application',
            'shades_name' => 'Shades Name',
            'brand' => 'Brand',
            'stamps' => 'Stamps',
            'disclamer' => 'Disclamer',
            'fragrance' => 'Fragrance',
            'shade' => 'Shade',
            'profile_name' => 'Profile Name',
            'subbrand' => 'Subbrand',
            'weight' => 'Weight',
            'compound' => 'Compound',
            'shades_code' => 'Shades Code',
            'site_name_short' => 'Site Name Short',
            'certificates' => 'Certificates',
            'volume' => 'Volume',
            'aroma_notes' => 'Aroma Notes',
            'aroma_text' => 'Aroma Text',
            'perfumer_name' => 'Perfumer Name',
            'perfumer_description' => 'Perfumer Description',
            'perfumer_text' => 'Perfumer Text',
            'cult_stamps_name' => 'Cult Stamps Name',
            'cult_stamps_text' => 'Cult Stamps Text',
            'cult_disclamer' => 'Cult Disclamer',
            'cult_position' => 'Cult Position',
            'stamps_name' => 'Stamps Name',
            'stamps_text' => 'Stamps Text',
            'stamps_disclamer' => 'Stamps Disclamer',
            'ingredients_main' => 'Ingredients Main',
            'variants' => 'Variants',
            'set_components' => 'Set Components',
            'search_tags' => 'Search Tags',
            'brochure_set_components' => 'Brochure Set Components',
            'disabled' => 'Отключено',
            'created_at' => 'Дата создания',
            'edited_at' => 'Edited At',
            'answer1' => 'Ответ 1',
            'answer2' => 'Ответ 2',
            'answer3' => 'Ответ 3',
            'group_new' => 'Группа (new)',
        ];
    }

    /**
     * Gets query for [[ImageProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImageProducts()
    {
        return $this->hasMany(ImageProducts::class, ['id_product' => 'id']);
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
	        'disabled' => $this->disabled
		]);

        $query->andFilterWhere(['like', $this->tableName().'.name', $this->name]);
        $query->andFilterWhere(['like', $this->tableName().'.category', $this->category]);
        $query->andFilterWhere(['like', $this->tableName().'.group_name', $this->group_name]);
        $query->andFilterWhere(['like', $this->tableName().'.segment', $this->segment]);
        $query->andFilterWhere(['like', $this->tableName().'.link', $this->link]);
        $query->andFilterWhere(['like', $this->tableName().'.answer1', $this->answer1]);
        $query->andFilterWhere(['like', $this->tableName().'.answer2', $this->answer2]);
        $query->andFilterWhere(['like', $this->tableName().'.answer3', $this->answer3]);
        $query->andFilterWhere(['like', $this->tableName().'.group_new', $this->group_new]);

		if (!empty($this->created_at)) {
			$c = explode(' - ', $this->created_at);

			$query->andFilterWhere(['between', $this->tableName().'.created_at', date('Y-m-d', strtotime($c[0]))." 00:00:00", date('Y-m-d', strtotime($c[1]))." 23:59:59"]);
		}

        return $dataProvider;
	}
}
