<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mockup_url}}`.
 */
class m200327_144910_create_mockup_url_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mockup_url}}', [
            'id' => $this->primaryKey(),
            'url_fragment' => $this->string(36),
            'jwt_token' => $this->string(512),
        ]);
        $this->createIndex('idx_unique_url_fragment', '{{%mockup_url}}', 'url_fragment', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mockup_url}}');
    }
}
