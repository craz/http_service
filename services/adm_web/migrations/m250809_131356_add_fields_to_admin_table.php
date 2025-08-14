<?php

use yii\db\Migration;

class m250809_131356_add_fields_to_admin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // На случай, если таблица уже была создана без нужных полей в ранних версиях
        $schema = $this->db->schema->getTableSchema('{{%admins}}', true);

        if ($schema === null) {
            // Таблицы нет — создадим минимально необходимую структуру
            $this->createTable('{{%admins}}', [
                'id' => $this->primaryKey(),
                'login' => $this->string(256)->notNull(),
                'password' => $this->string(256)->notNull(),
                'role' => $this->string(64)->notNull()->defaultValue('admin'),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
            $this->createIndex('idx_admins_login', '{{%admins}}', 'login', true);
            return;
        }

        // Добавляем отсутствующие столбцы
        $columns = $schema->columns;
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

        // Уникальный индекс на логин
        $db = $this->db;
        $indexes = $db->createCommand("SELECT indexname FROM pg_indexes WHERE schemaname = current_schema() AND tablename = 'admins'")->queryColumn();
        if (!in_array('idx_admins_login', $indexes, true)) {
            $this->createIndex('idx_admins_login', '{{%admins}}', 'login', true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Аккуратно удаляем то, что могли добавить в safeUp
        $schema = $this->db->schema->getTableSchema('{{%admins}}', true);
        if ($schema !== null) {
            // Индекс
            $db = $this->db;
            $indexes = $db->createCommand("SELECT indexname FROM pg_indexes WHERE schemaname = current_schema() AND tablename = 'admins'")->queryColumn();
            if (in_array('idx_admins_login', $indexes, true)) {
                $this->dropIndex('idx_admins_login', '{{%admins}}');
            }
            // Столбцы — удалять необязательно, оставим таблицу целой
        }
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250809_131356_add_fields_to_admin_table cannot be reverted.\n";

        return false;
    }
    */
}
