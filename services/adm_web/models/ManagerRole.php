<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Связь многие-ко-многим между менеджерами и ролями.
 * @property int $manager_id
 * @property int $role_id
 */
class ManagerRole extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'manager_role';
    }

    public static function primaryKey(): array
    {
        return ['manager_id', 'role_id'];
    }
}


