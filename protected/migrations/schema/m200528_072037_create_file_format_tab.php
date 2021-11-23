<?php

class m200528_072037_create_file_format_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS file_format (
            id integer NOT NULL,
            name character varying(20) NOT NULL,
            description text DEFAULT ''::text NOT NULL,
            edam_ontology_id character varying(100));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS file_format_id_seq
            START WITH 100
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE file_format_id_seq 
            OWNED BY file_format.id;");

        $this->execute("ALTER TABLE ONLY file_format 
            ALTER COLUMN id SET DEFAULT nextval('file_format_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY file_format
            ADD CONSTRAINT file_format_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE file_format_id_seq CASCADE;");
        $this->dropTable('file_format');
    }
}
