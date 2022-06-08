<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ingest}}`.
 */
class m220608_101045_create_ingest_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ingest}}', [
            'id' => $this->primaryKey(),
            'file_name' => $this->string(),
            'report_type' => $this->integer(),
            'fetch_status' => $this->integer(),
            'parse_status' => $this->integer(),
            'store_status' => $this->integer(),
            'remote_file_status' => $this->integer(),
            'created_at' => $this->datetime(),
            'updated_at' => $this->datetime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%ingest}}');
    }
}
