<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%upload}}`.
 */
class m200305_155409_add_sample_id_column_to_upload_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%upload}}', 'sample_ids', $this->string(512)->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%upload}}', 'sample_ids');
    }
}
