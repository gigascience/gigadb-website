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
            'fetch_status' => $this->smallInteger(),
            'parse_status' => $this->smallInteger(),
            'store_status' => $this->smallInteger(),
            'remote_file_status' => $this->smallInteger(),
            'created_at' => $this->bigInteger(),
            'updated_at' => $this->bigInteger(),
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
