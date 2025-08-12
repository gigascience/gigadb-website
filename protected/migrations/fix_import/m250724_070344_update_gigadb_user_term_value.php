<?php

declare(strict_types=1);

class m250724_070344_update_gigadb_user_term_value extends CDbMigration
{
    public function safeUp()
    {
        $this->update('gigadb_user', ['terms' => true]);
    }

    public function safeDown()
    {
    }
}
