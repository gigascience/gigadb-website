<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%filedrop_account}}`.
 */
class m191030_110616_add_instructions_column_to_filedrop_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%filedrop_account}}', 'instructions', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%filedrop_account}}', 'instructions');
    }
}
