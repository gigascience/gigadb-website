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
            'manuscript_number' => $this->string(),
            'article_title' => $this->string(),
            'revision_number' => $this->smallinteger(),
            'created_at' => $this->bigInteger(),
            'updated_at' => $this->bigInteger(),
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
