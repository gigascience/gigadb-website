<?php

class m200528_052407_create_yiisession_tab extends CDbMigration
{
public function up()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS \"YiiSession\" (
            id CHAR(32) PRIMARY KEY,
            expire integer,
            data bytea);");
    }

    public function down()
    {
        // YiiSession table is essential
        echo "m200528_052407_create_yiisession_tab does not support migration down.\n";
        return false;
    }
}
