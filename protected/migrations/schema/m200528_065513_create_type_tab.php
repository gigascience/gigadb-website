<?php

class m200528_065513_create_type_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS type (
            id integer NOT NULL,
            name character varying(32) NOT NULL,
            description text DEFAULT ''::text NOT NULL);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS type_id_seq 
            START WITH 30 
            INCREMENT BY 1 
            NO MINVALUE 
            NO MAXVALUE 
            CACHE 1;");

        $this->execute("ALTER SEQUENCE type_id_seq 
            OWNED BY type.id;");

        $this->execute("ALTER TABLE ONLY type 
            ALTER COLUMN id SET DEFAULT nextval('type_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY type
            ADD CONSTRAINT type_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE type_id_seq CASCADE;");
        $this->dropTable('type');
    }
}
