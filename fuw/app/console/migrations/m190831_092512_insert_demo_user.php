<?php

use yii\db\Migration;

/**
 * Class m190831_092512_insert_demo_user
 */
class m190831_092512_insert_demo_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%user}}', ["username" => "prototype", "email" => "sfriesen@jenkins.info", "auth_key" => "dsfasdfasdfdsa", "password_hash" => "dsafadsgads","password_reset_token" => "oqwetad" , "status" => 10, "created_at" => 1565353012, "updated_at" =>  1565353012]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%user}}',"username = 'prototype'");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190831_092512_insert_demo_user cannot be reverted.\n";

        return false;
    }
    */
}
