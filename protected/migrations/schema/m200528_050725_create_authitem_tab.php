<?php

class m200528_050725_create_authitem_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS AuthItem (
            name character varying(64) NOT NULL,
            type integer NOT NULL,
            description text,
            bizrule text,
            data text);");

        $this->execute("ALTER TABLE ONLY AuthItem
            ADD CONSTRAINT AuthItem_pkey PRIMARY KEY (name);");
    }

    public function safeDown()
    {
        $this->dropTable('AuthItem');
    }
}
