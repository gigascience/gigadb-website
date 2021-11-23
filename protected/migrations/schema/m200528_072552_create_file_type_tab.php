<?php

class m200528_072552_create_file_type_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS file_type (
            id integer NOT NULL,
            name character varying(100) NOT NULL,
            description text DEFAULT ''::text NOT NULL,
            edam_ontology_id character varying(100));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS file_type_id_seq
            START WITH 200
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE file_type_id_seq 
            OWNED BY file_type.id;");

        $this->execute("ALTER TABLE ONLY file_type 
            ALTER COLUMN id SET DEFAULT nextval('file_type_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY file_type
            ADD CONSTRAINT file_type_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE file_type_id_seq CASCADE;");
        $this->dropTable('file_type');
    }
}
