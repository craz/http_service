<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class RoutingRule extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'routing_rules';
    }

    public function rules(): array
    {
        return [
            [['indata', 'target_model_type', 'model_number'], 'required'],
            [['indata'], 'string'],
            [['model_number'], 'integer'],
            [['target_model_type'], 'string', 'max' => 128],
            [['created_at', 'edited_at'], 'safe'],
            [['id', 'model_number', 'target_model_type', 'indata'], 'safe', 'on' => 'search'],
        ];
    }

    public function search(array $params, int $modelNumber): ActiveDataProvider
    {
        $query = self::find()->where(['model_number' => $modelNumber])->orderBy(['id' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);
        $this->load($params);
        $query->andFilterWhere(['target_model_type' => $this->target_model_type]);
        $query->andFilterWhere(['like', 'indata', $this->indata]);
        return $dataProvider;
    }
}


