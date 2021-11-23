<?php

class m200529_022516_create_prefix_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS prefix (
            id integer DEFAULT nextval('link_prefix_id_seq'::regclass) NOT NULL,
            prefix character(20) NOT NULL,
            url text NOT NULL,
            source character varying(128) DEFAULT ''::character varying,
            icon character varying(100));");

        $this->execute("ALTER TABLE ONLY prefix
            ADD CONSTRAINT link_prefix_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->dropTable('news');
    }
}
