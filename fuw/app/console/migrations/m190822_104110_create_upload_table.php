<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%upload}}`.
 */
class m190822_104110_create_upload_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%upload}}', [
            'id' => $this->primaryKey(),
            'doi' => $this->string(100),
            'name' => $this->string(128)->notNull(),
            'size' => $this->bigInteger()->notNull(),
            'status' => $this->integer(),
            'location' => $this->string(200)->notNull(),
            'description' => $this->text(),
            'initial_md5' => $this->text(),
            'extension' => $this->string(32),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'filedrop_account_id' => $this->integer()->notNull(),
        ]);

        $this->execute("CREATE OR REPLACE FUNCTION update_modified_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$$ language 'plpgsql'");

        $this->execute("CREATE TRIGGER update_upload_modtime BEFORE UPDATE ON upload
FOR EACH ROW EXECUTE PROCEDURE  update_modified_column()");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%upload}}');
    }
}
