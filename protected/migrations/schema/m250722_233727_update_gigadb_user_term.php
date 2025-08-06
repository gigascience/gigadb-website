<?php

declare(strict_types=1);

class m250722_233727_update_gigadb_user_term extends CDbMigration
{
    public function safeUp()
    {
        $this->addColumn('gigadb_user', 'terms', 'BOOLEAN NOT NULL DEFAULT FALSE');
    }

    public function safeDown()
    {
        $this->dropColumn('gigadb_user', 'terms');
    }
}
