<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Роль для менеджеров.
 * @property int $id
 * @property string $name
 * @property string $slug
 */
class Role extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'roles';
    }

    public function rules(): array
    {
        return [
            [['name', 'slug'], 'required'],
            [['name', 'slug'], 'string', 'max' => 128],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'slug' => 'Код',
        ];
    }
}


