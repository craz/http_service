<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%admins}}`.
 */
class m250809_131102_create_admin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Основная таблица администраторов
        $this->createTable('{{%admins}}', [
            'id' => $this->primaryKey(),
            'login' => $this->string(256)->notNull(),
            'password' => $this->string(256)->notNull(),
            'role' => $this->string(64)->notNull()->defaultValue('admin'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Индекс для быстрого поиска по логину
        $this->createIndex('idx_admins_login', '{{%admins}}', 'login', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%admins}}');
    }
}
