<?php

declare(strict_types=1);

class m250506_041933_update_gigadb_user_token extends CDbMigration
{
    public function safeUp() {
        $this->addColumn('gigadb_user', 'activation_token', 'VARCHAR(250) UNIQUE');
    }

    public function safeDown() {
        $this->dropColumn('gigadb_user', 'activation_token');
    }
}
