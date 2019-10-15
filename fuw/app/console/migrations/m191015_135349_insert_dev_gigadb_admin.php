<?php

use yii\db\Migration;
use common\models\User;

/**
 * Class m191015_135349_insert_dev_gigadb_admin
 */
class m191015_135349_insert_dev_gigadb_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%user}}', ["username" => "gigadb_admin", "email" => "admin@gigadb.org", "auth_key" => "dsfasdfasdfdsa", "password_hash" => "dsafadsgads","password_reset_token" => "dfdsfasgadfsa" , "status" => User::STATUS_ACTIVE, "created_at" => 1565353012, "updated_at" =>  1565353012]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%user}}',"username = 'gigadb_admin'");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191015_135349_insert_dev_gigadb_admin cannot be reverted.\n";

        return false;
    }
    */
}
