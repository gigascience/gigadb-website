<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%upload}}`.
 */
class m200115_105203_add_datatype_column_to_upload_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%upload}}', 'datatype', $this->string(32));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%upload}}', 'datatype');
    }
}
