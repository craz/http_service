<?php

use yii\db\Migration;

/**
 * Ensures that table `admins` exists with required columns and indexes.
 * If legacy table `admin` exists, it will be renamed to `admins`.
 */
class m250811_092500_ensure_admins_table extends Migration
{
    public function safeUp()
    {
        $schema = $this->db->schema;

        $admins = $schema->getTableSchema('{{%admins}}', true);
        $admin = $schema->getTableSchema('{{%admin}}', true);

        if ($admins === null && $admin !== null) {
            // Переименуем устаревшую таблицу
            $this->renameTable('{{%admin}}', '{{%admins}}');
            $admins = $schema->getTableSchema('{{%admins}}', true);
        }

        if ($admins === null) {
            // Создадим таблицу с нуля
            $this->createTable('{{%admins}}', [
                'id' => $this->primaryKey(),
                'login' => $this->string(256)->notNull(),
                'password' => $this->string(256)->notNull(),
                'role' => $this->string(64)->notNull()->defaultValue('admin'),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
        } else {
            // Добавляем отсутствующие столбцы на существующей таблице
            $columns = $admins->columns;
            if (!isset($columns['login'])) {
                $this->addColumn('{{%admins}}', 'login', $this->string(256)->notNull());
            }
            if (!isset($columns['password'])) {
                $this->addColumn('{{%admins}}', 'password', $this->string(256)->notNull());
            }
            if (!isset($columns['role'])) {
                $this->addColumn('{{%admins}}', 'role', $this->string(64)->notNull()->defaultValue('admin'));
            }
            if (!isset($columns['created_at'])) {
                $this->addColumn('{{%admins}}', 'created_at', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
            }
            if (!isset($columns['edited_at'])) {
                $this->addColumn('{{%admins}}', 'edited_at', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
            }
        }

        // Уникальный индекс на логин
        $indexes = $this->db->createCommand("SELECT indexname FROM pg_indexes WHERE schemaname = current_schema() AND tablename = 'admins'")->queryColumn();
        if (!in_array('idx_admins_login', $indexes, true)) {
            $this->createIndex('idx_admins_login', '{{%admins}}', 'login', true);
        }

        // Минимальные демо-данные администратора
        $hasAnyAdmin = (int)$this->db->createCommand("SELECT COUNT(1) FROM admins")->queryScalar();
        if ($hasAnyAdmin === 0) {
            $this->insert('{{%admins}}', [
                'login' => 'admin',
                'password' => 'admin',
                'role' => 'admin',
            ]);
        }
    }

    public function safeDown()
    {
        // Безопасный откат: оставим таблицу, но удалим уникальный индекс, если он есть
        $schema = $this->db->schema->getTableSchema('{{%admins}}', true);
        if ($schema !== null) {
            $indexes = $this->db->createCommand("SELECT indexname FROM pg_indexes WHERE schemaname = current_schema() AND tablename = 'admins'")->queryColumn();
            if (in_array('idx_admins_login', $indexes, true)) {
                $this->dropIndex('idx_admins_login', '{{%admins}}');
            }
        }
        return true;
    }
}


