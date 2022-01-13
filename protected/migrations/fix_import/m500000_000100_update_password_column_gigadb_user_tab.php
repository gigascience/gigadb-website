<?php

class m500000_000100_update_password_column_gigadb_user_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("ALTER TABLE gigadb_user
            ALTER COLUMN password TYPE character varying(128);");
    }

    public function safeDown()
    {
        // This function is empty because reverting safeUp() will mean passwords
        // after this migration will be too big for the old column size.
    }
}
