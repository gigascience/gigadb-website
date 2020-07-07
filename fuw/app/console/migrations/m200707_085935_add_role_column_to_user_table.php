<?php

use yii\db\Migration;
use common\models\User;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m200707_085935_add_role_column_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'role', $this->string(10));
        $this->update('{{%user}}', ["role" => "admin"], "username='gigadb_admin'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'role');
    }
}
