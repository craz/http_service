<?php

use yii\db\Migration;

class m250811_160000_create_context_tables extends Migration
{
    public function safeUp()
    {
        $schema = $this->db->schema;
        if ($schema->getTableSchema('{{%routing_rules}}', true) === null) {
            $this->createTable('{{%routing_rules}}', [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'model_number' => $this->integer()->notNull()->defaultValue(1), // 1|2|3
                'indata' => $this->text()->notNull(),
                'target_model_type' => $this->string(128)->notNull(), // 'контейнер', 'опт', ...
            ]);
            $this->createIndex('idx_routing_rules_model_number', '{{%routing_rules}}', 'model_number');
            $this->createIndex('idx_routing_rules_target', '{{%routing_rules}}', 'target_model_type');
        }
    }

    public function safeDown()
    {
        $schema = $this->db->schema;
        if ($schema->getTableSchema('{{%routing_rules}}', true) !== null) {
            $this->dropTable('{{%routing_rules}}');
        }
        return true;
    }
}


