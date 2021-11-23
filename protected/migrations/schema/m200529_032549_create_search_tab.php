<?php

class m200529_032549_create_search_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS search (
            id integer NOT NULL,
            user_id integer NOT NULL,
            name character varying(128) NOT NULL,
            query text NOT NULL,
            result text);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS search_id_seq 
            START WITH 1 
            INCREMENT BY 1 
            NO MINVALUE 
            NO MAXVALUE 
            CACHE 1;");

        $this->execute("ALTER SEQUENCE search_id_seq 
            OWNED BY search.id;");

        $this->execute("ALTER TABLE ONLY search 
            ALTER COLUMN id SET DEFAULT nextval('search_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY search
            ADD CONSTRAINT search_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY search
            ADD CONSTRAINT search_user_id_fkey FOREIGN KEY (user_id) REFERENCES gigadb_user(id) ON DELETE RESTRICT;");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE search_id_seq CASCADE;");
        $this->dropTable('search');
    }
}
