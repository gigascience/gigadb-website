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
        $this->createTable('filedrop_account', array(
            'id' => 'serial PRIMARY KEY',
            'doi' => 'character varying(100) NOT NULL',
            'upload_login' => 'character varying(100)',
            'upload_token' => 'character varying(128)',
            'download_login' => 'character varying(100)',
            'download_token' => 'character varying(128)',
            'status' => 'character varying(100)',
            'created_at' => 'timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL',
            'updated_at' => 'timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL',
            'retired_at' => 'timestamp',
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

       $this->execute("CREATE OR REPLACE FUNCTION update_retired_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.retired_at = now();
    RETURN NEW;
END;
$$ language 'plpgsql'");

        $this->execute("CREATE TRIGGER update_account_retiredtime BEFORE UPDATE ON filedrop_account
FOR EACH ROW
WHEN (OLD.status <> NEW.status AND NEW.status = 'retired')
EXECUTE PROCEDURE  update_retired_column()");

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
