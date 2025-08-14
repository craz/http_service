<?php

use yii\db\Migration;

/**
 * Creates minimal required domain tables used by admin UI: users, users_data, users_quiz,
 * bot_users, logs, bot_history, settings, structure_catalog, product, images, image_products,
 * promoted_topics, unwanted_information, unwanted_information_example, dispatches, dispatch_pushes.
 *
 * This mirrors the minimal schema used in DbMockController->actionDemo().
 */
class m250811_092600_create_minimal_domain_tables extends Migration
{
    public function safeUp()
    {
        $schema = $this->db->schema;

        // Helper to create table if it does not exist
        $ensure = function (string $name, callable $creator) use ($schema) {
            if ($schema->getTableSchema("{{%$name}}", true) === null) {
                $creator();
            }
        };

        $ensure('settings', function () {
            $this->createTable('{{%settings}}', [
                'id' => $this->primaryKey(),
                'title' => $this->string(256)->notNull(),
                'alias' => $this->string(256)->notNull(),
                'val' => $this->text()->notNull(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
        });

        $ensure('structure_catalog', function () {
            $this->createTable('{{%structure_catalog}}', [
                'id' => $this->primaryKey(),
                'category' => $this->string(256)->notNull(),
                'question1' => $this->string(256)->notNull(),
                'question2' => $this->string(256)->notNull(),
                'question3' => $this->text(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
        });

        $ensure('product', function () {
            $this->createTable('{{%product}}', [
                'id' => $this->integer()->notNull(),
                'name' => $this->text(),
                'category' => $this->text(),
                'disabled' => $this->boolean()->notNull()->defaultValue(false),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
            $this->addPrimaryKey('pk_product', '{{%product}}', 'id');
        });

        $ensure('images', function () {
            $this->createTable('{{%images}}', [
                'id' => $this->primaryKey(),
                'name' => $this->string(256)->notNull(),
                'holiday_id' => $this->integer(),
                'disabled' => $this->boolean()->notNull()->defaultValue(false),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
        });

        $ensure('image_products', function () {
            $this->createTable('{{%image_products}}', [
                'id' => $this->primaryKey(),
                'id_images' => $this->integer()->notNull(),
                'id_product' => $this->integer()->notNull(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'description' => $this->text(),
            ]);
            $this->addForeignKey('fk_image_products_images', '{{%image_products}}', 'id_images', '{{%images}}', 'id', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_image_products_product', '{{%image_products}}', 'id_product', '{{%product}}', 'id', 'CASCADE', 'CASCADE');
        });

        $ensure('groups_example', function () {
            $this->createTable('{{%groups_example}}', [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edit_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'id_class' => $this->integer()->notNull(),
                'name_class' => $this->text()->notNull(),
                'product' => $this->text(),
                'assignment' => $this->text(),
                'description' => $this->text(),
            ]);
            $this->addForeignKey('fk_groups_example_structure_catalog', '{{%groups_example}}', 'id_class', '{{%structure_catalog}}', 'id', 'CASCADE', 'CASCADE');
        });

        $ensure('promoted_topics', function () {
            $this->createTable('{{%promoted_topics}}', [
                'id' => $this->primaryKey(),
                'name' => $this->string(256)->notNull(),
                'topic' => $this->string(256)->notNull(),
                'disabled' => $this->boolean()->notNull()->defaultValue(false),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
        });

        $ensure('unwanted_information', function () {
            $this->createTable('{{%unwanted_information}}', [
                'id' => $this->primaryKey(),
                'name' => $this->string(256)->notNull(),
                'content' => $this->text(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
        });

        $ensure('unwanted_information_example', function () {
            $this->createTable('{{%unwanted_information_example}}', [
                'id' => $this->primaryKey(),
                'id_unwanted_information' => $this->integer()->notNull(),
                'text' => $this->text()->notNull(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edit_at' => $this->string(50),
            ]);
            $this->addForeignKey('fk_unw_info_example_unw_info', '{{%unwanted_information_example}}', 'id_unwanted_information', '{{%unwanted_information}}', 'id', 'CASCADE', 'CASCADE');
        });

        $ensure('bot_users', function () {
            $this->createTable('{{%bot_users}}', [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'tg_id' => $this->bigInteger()->notNull(),
                'tg_login' => $this->string(128),
                'user_id' => $this->integer(),
            ]);
        });

        $ensure('users', function () {
            $this->createTable('{{%users}}', [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'bot_user_id' => $this->integer()->notNull(),
                'name' => $this->string(255),
                'gender' => $this->integer(),
                'phone' => $this->string(64),
                'age' => $this->integer(),
                'birth_date' => $this->timestamp(),
                'is_registered' => $this->integer(),
                'using_frequency' => $this->integer(),
                'favorite_products' => $this->integer(),
                'refusal_reason' => $this->integer(),
                'personal_data' => $this->boolean()->defaultValue(false),
                'edited_at' => $this->timestamp(),
                'skin_type' => $this->text(),
                'another_flag' => $this->boolean()->defaultValue(false),
                'referent_id' => $this->integer(),
                'is_dispatch_subscribed' => $this->integer()->defaultValue(0),
                'ads_dispatch' => $this->integer()->defaultValue(0),
                'register_tries_cnt' => $this->integer()->defaultValue(0),
                'is_intro_processed' => $this->integer()->defaultValue(0),
                'utm_medium' => $this->text(),
                'utm_source' => $this->text(),
                'utm_campaign' => $this->text(),
                'utm_term' => $this->text(),
                'utm_content' => $this->text(),
                'is_register_push_sended_1' => $this->integer()->defaultValue(0),
                'quiz_1_started' => $this->integer()->defaultValue(0),
            ]);
            $this->addForeignKey('fk_users_bot_users', '{{%users}}', 'bot_user_id', '{{%bot_users}}', 'id', 'CASCADE', 'CASCADE');
        });

        $ensure('users_quiz', function () {
            $this->createTable('{{%users_quiz}}', [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'user_id' => $this->integer()->notNull(),
                'answer1' => $this->text(),
                'answer2' => $this->text(),
                'answer3' => $this->text(),
                'answer4' => $this->text(),
                'answer5' => $this->text(),
                'result1' => $this->text(),
            ]);
            $this->addForeignKey('fk_users_quiz_users', '{{%users_quiz}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        });

        $ensure('users_data', function () {
            $this->createTable('{{%users_data}}', [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer()->notNull(),
                'push_registered_cnt' => $this->integer()->defaultValue(0),
                'push_phone_cnt' => $this->integer()->defaultValue(0),
                'push_registered_and_phone_cnt' => $this->integer()->defaultValue(0),
            ]);
            $this->addForeignKey('fk_users_data_users', '{{%users_data}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
        });

        $ensure('logs', function () {
            $this->createTable('{{%logs}}', [
                'id' => $this->primaryKey(),
                'point' => $this->text()->notNull(),
                'answer_time' => $this->decimal(10, 2)->notNull(),
                'bot_user_id' => $this->integer()->notNull(),
                'in_data' => $this->text()->notNull(),
                'model_type' => $this->integer()->notNull(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
            $this->addForeignKey('fk_logs_bot_users', '{{%logs}}', 'bot_user_id', '{{%bot_users}}', 'id', 'CASCADE', 'CASCADE');
        });

        $ensure('bot_history', function () {
            $this->createTable('{{%bot_history}}', [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'bot_user_id' => $this->integer()->notNull(),
                'is_bot' => $this->integer()->notNull(),
                'in_data' => $this->text()->notNull(),
                'out_data' => $this->text(),
                'images' => $this->text(),
                'action_url' => $this->string(256),
                'params' => $this->text(),
                'model_type' => $this->integer(),
                'tg_api_id' => $this->string(64),
                'tg_out_data' => $this->text(),
                'answer_time' => $this->decimal(10, 2),
                'system_flag' => $this->integer(),
                'reply_to_history_id' => $this->integer(),
            ]);
        });

        $ensure('dispatch_pushes', function () {
            $this->createTable('{{%dispatch_pushes}}', [
                'id' => $this->primaryKey(),
                'body' => $this->text()->notNull(),
                'daytype' => $this->integer()->notNull(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
        });

        $ensure('dispatches', function () {
            $this->createTable('{{%dispatches}}', [
                'id' => $this->primaryKey(),
                'name' => $this->string(256)->notNull(),
                'text' => $this->text()->notNull(),
                'gender' => $this->integer(),
                'age_min' => $this->integer(),
                'age_max' => $this->integer(),
                'is_registered' => $this->integer(),
                'using_frequency' => $this->integer(),
                'favorite_products' => $this->integer(),
                'refusal_reason' => $this->integer(),
                'date_start' => $this->timestamp()->notNull(),
                'is_sended' => $this->integer()->defaultValue(0),
                'users_ids' => $this->text(),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'edited_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'is_send_text_and_media_separately' => $this->integer(),
                'text_type' => $this->integer(),
                'is_ads_dispatch' => $this->integer(),
            ]);
        });
    }

    public function safeDown()
    {
        // Ничего не удаляем, эти таблицы являются доменными и могут содержать данные
        return true;
    }
}


