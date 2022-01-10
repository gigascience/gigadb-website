<?php

class m300000_000100_update_password_column_gigadb_user_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("ALTER TABLE gigadb_user
            ALTER COLUMN password TYPE character varying(128);");
    }

    public function safeDown()
    {
        $this->execute("ALTER TABLE gigadb_user
            ALTER COLUMN password TYPE character varying(64);");
    }
}
