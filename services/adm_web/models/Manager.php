<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * Модель менеджера.
 *
 * @property int $id
 * @property string $created_at
 * @property string $edited_at
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $tg_username
 * @property int $tg_id
 * @property string $status
 * @property string $schedule
 */
class Manager extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'managers';
    }

    public function rules(): array
    {
        return [
            [['first_name', 'last_name', 'email', 'tg_username', 'status', 'schedule'], 'safe'],
            [['tg_id'], 'integer'],
            [['email'], 'email'],
            [['status'], 'default', 'value' => 'active'],
            [['schedule'], 'default', 'value' => 'day'],
            [['created_at', 'edited_at'], 'safe'],
            [['id', 'first_name', 'last_name', 'status', 'schedule'], 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'created_at' => 'Создан',
            'edited_at' => 'Изменен',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'email' => 'Почта',
            'tg_username' => 'ID Telegram',
            'tg_id' => 'ID tG',
            'status' => 'Статус',
            'schedule' => 'График',
        ];
    }

    public function getManagerRoles()
    {
        return $this->hasMany(ManagerRole::class, ['manager_id' => 'id']);
    }

    public function getRoles()
    {
        return $this->hasMany(Role::class, ['id' => 'role_id'])->via('managerRoles');
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'schedule' => $this->schedule,
        ]);

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
              ->andFilterWhere(['like', 'last_name', $this->last_name]);

        return $dataProvider;
    }
}


