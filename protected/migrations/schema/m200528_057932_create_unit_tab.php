<?php

class m200528_057932_create_unit_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS unit (
            id character varying(30) NOT NULL,
            name character varying(200),
            definition character varying(500));");

        $this->execute("ALTER TABLE ONLY unit
            ADD CONSTRAINT unit_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->dropTable('unit');
    }
}
