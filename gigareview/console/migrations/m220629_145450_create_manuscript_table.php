<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%manuscript}}`.
 */
class m220629_145450_create_manuscript_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%manuscript}}', [
            'id' => $this->primaryKey(),
            'doi' => $this->integer(),
            'manuscript_number' => $this->string(),
            'article_title' => $this->string(),
            'publication_date' => $this->date(),
            'editorial_status' => $this->string(),
            'editorial_status_date' => $this->date(),
            'editors_note' => $this->text(),
            'created_at' => $this->biginteger(),
            'updated_at' => $this->biginteger(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%manuscript}}');
    }
}
