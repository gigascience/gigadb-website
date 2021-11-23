<?php

class m200528_061005_create_funder_name_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS funder_name (
            id integer NOT NULL,
            uri character varying(100) NOT NULL,
            primary_name_display character varying(1000),
            country character varying(128) DEFAULT ''::character varying);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS funder_name_id_seq
            START WITH 6200
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE funder_name_id_seq 
            OWNED BY funder_name.id;");

        $this->execute("ALTER TABLE ONLY funder_name 
            ALTER COLUMN id SET DEFAULT nextval('funder_name_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY funder_name
            ADD CONSTRAINT funder_name_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE funder_name_id_seq CASCADE;");
        $this->dropTable('funder_name');
    }
}
