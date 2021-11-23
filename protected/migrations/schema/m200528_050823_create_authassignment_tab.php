<?php

class m200528_050823_create_authassignment_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS AuthAssignment (
            itemname character varying(64) NOT NULL,
            userid character varying(64) NOT NULL,
            bizrule text,
            data text);");

        $this->execute("ALTER TABLE ONLY AuthAssignment
            ADD CONSTRAINT AuthAssignment_pkey PRIMARY KEY (itemname, userid);");

        $this->execute("ALTER TABLE ONLY AuthAssignment
            ADD CONSTRAINT AuthAssignment_itemname_fkey FOREIGN KEY (itemname) REFERENCES AuthItem(name) ON UPDATE CASCADE ON DELETE CASCADE;");
    }

    public function safeDown()
    {
        $this->dropTable('AuthAssignment');
    }
}
