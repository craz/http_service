<?php

use yii\db\Migration;

/**
 * Создает таблицы для управления менеджерами и их ролями.
 */
class m250811_120000_create_manager_tables extends Migration
{
    public function safeUp()
    {
        $schema = $this->db->schema;

        // managers
        if ($schema->getTableSchema('{{%managers}}', true) === null) {
            $this->createTable('{{%managers}}', [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'first_name' => $this->string(128),
                'last_name' => $this->string(128),
                'email' => $this->string(256),
                'tg_username' => $this->string(128),
                'tg_id' => $this->bigInteger(),
                'status' => $this->string(32)->notNull()->defaultValue('active'), // active|vacation|sick
                'schedule' => $this->string(32)->notNull()->defaultValue('day'),   // day|night|flex
            ]);
            $this->createIndex('idx_managers_status', '{{%managers}}', 'status');
            $this->createIndex('idx_managers_schedule', '{{%managers}}', 'schedule');
        }

        // roles
        if ($schema->getTableSchema('{{%roles}}', true) === null) {
            $this->createTable('{{%roles}}', [
                'id' => $this->primaryKey(),
                'name' => $this->string(128)->notNull(),
                'slug' => $this->string(128)->notNull(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
            $this->createIndex('idx_roles_slug', '{{%roles}}', 'slug', true);
        }

        // pivot manager_role
        if ($schema->getTableSchema('{{%manager_role}}', true) === null) {
            $this->createTable('{{%manager_role}}', [
                'manager_id' => $this->integer()->notNull(),
                'role_id' => $this->integer()->notNull(),
            ]);
            $this->addPrimaryKey('pk_manager_role', '{{%manager_role}}', ['manager_id', 'role_id']);
            $this->addForeignKey('fk_manager_role_manager', '{{%manager_role}}', 'manager_id', '{{%managers}}', 'id', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_manager_role_role', '{{%manager_role}}', 'role_id', '{{%roles}}', 'id', 'CASCADE', 'CASCADE');
        }

        // Предзаполним несколько типовых ролей, если пусто
        $hasRoles = (int)$this->db->createCommand('SELECT COUNT(1) FROM {{%roles}}')->queryScalar();
        if ($hasRoles === 0) {
            $this->batchInsert('{{%roles}}', ['name', 'slug'], [
                ['Розница', 'retail'],
                ['Контейнер', 'container'],
                ['Белая логистика', 'white_logistics'],
                ['Оплаты', 'payments'],
            ]);
        }
    }

    public function safeDown()
    {
        $schema = $this->db->schema;

        if ($schema->getTableSchema('{{%manager_role}}', true) !== null) {
            $this->dropForeignKey('fk_manager_role_manager', '{{%manager_role}}');
            $this->dropForeignKey('fk_manager_role_role', '{{%manager_role}}');
            $this->dropTable('{{%manager_role}}');
        }
        if ($schema->getTableSchema('{{%roles}}', true) !== null) {
            $this->dropTable('{{%roles}}');
        }
        if ($schema->getTableSchema('{{%managers}}', true) !== null) {
            $this->dropTable('{{%managers}}');
        }
        return true;
    }
}


