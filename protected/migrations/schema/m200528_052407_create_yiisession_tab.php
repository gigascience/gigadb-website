<?php

class m200528_052407_create_yiisession_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE YiiSession (
            id character varying(32) NOT NULL,
            expire integer,
            data bytea);");
    }

    public function safeDown()
    {
        $this->dropTable('YiiSession');
    }
}
