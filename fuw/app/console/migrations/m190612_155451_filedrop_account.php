<?php

use yii\db\Migration;

/**
 * Class m190612_155451_filedrop_account
 */
class m190612_155451_filedrop_account extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%filedrop_account}}', array(
            'id' => $this->primaryKey(),
            'doi' => $this->string(100)->notNull(),
            'upload_login' => $this->string(100),
            'upload_token' => $this->string(128),
            'download_login' => $this->string(100),
            'download_token' => $this->string(128),
            'status' => $this->integer(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'terminated_at' => $this->timestamp(),
        ));

        $this->execute("CREATE OR REPLACE FUNCTION update_modified_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$$ language 'plpgsql'");

       $this->execute("CREATE TRIGGER update_account_modtime BEFORE UPDATE ON filedrop_account
FOR EACH ROW EXECUTE PROCEDURE  update_modified_column()");

       $this->execute("CREATE OR REPLACE FUNCTION update_terminated_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.terminated_at = now();
    RETURN NEW;
END;
$$ language 'plpgsql'");

        $this->execute("CREATE TRIGGER update_account_terminatedtime BEFORE UPDATE ON filedrop_account
FOR EACH ROW
WHEN (OLD.status <> NEW.status AND NEW.status = 0)
EXECUTE PROCEDURE  update_terminated_column()");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190612_155451_filedrop_account cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190612_155451_filedrop_account cannot be reverted.\n";

        return false;
    }
    */
}
