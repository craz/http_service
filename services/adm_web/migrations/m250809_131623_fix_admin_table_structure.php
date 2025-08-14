<?php

use yii\db\Migration;

class m250809_131623_fix_admin_table_structure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250809_131623_fix_admin_table_structure cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250809_131623_fix_admin_table_structure cannot be reverted.\n";

        return false;
    }
    */
}
