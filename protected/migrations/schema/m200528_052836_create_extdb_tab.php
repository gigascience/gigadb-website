<?php

class m200528_052836_create_extdb_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS extdb (
            id integer NOT NULL,
            database_name character varying(100),
            definition character varying(1000),
            database_homepage character varying(100),
            database_search_url character varying(100));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS extdb_id_seq
            START WITH 10
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE extdb_id_seq
            OWNED BY extdb.id;");

        $this->execute("ALTER TABLE ONLY extdb 
            ALTER COLUMN id SET DEFAULT nextval('extdb_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY extdb
            ADD CONSTRAINT extdb_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE extdb_id_seq CASCADE;");
        $this->dropTable('extdb');
    }
}
