<?php

class m200528_075520_create_file_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS file (
            id integer NOT NULL,
            dataset_id integer NOT NULL,
            name character varying(200) NOT NULL,
            location character varying(500) NOT NULL,
            extension character varying(100) NOT NULL,
            size bigint NOT NULL,
            description text DEFAULT ''::text NOT NULL,
            date_stamp date,
            format_id integer,
            type_id integer,
            code character varying(200) DEFAULT 'FILE_CODE'::character varying,
            index4blast character varying(50),
            download_count integer DEFAULT 0 NOT NULL,
            alternative_location character varying(200));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS file_id_seq
            START WITH 6300
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE file_id_seq 
            OWNED BY file.id;");

        $this->execute("CREATE OR REPLACE VIEW file_number AS
            SELECT count(file.id) AS count 
            FROM file;");

        $this->execute("ALTER TABLE ONLY file 
            ALTER COLUMN id SET DEFAULT nextval('file_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY file
            ADD CONSTRAINT file_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY file
            ADD CONSTRAINT file_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;");

        $this->execute("ALTER TABLE ONLY file
            ADD CONSTRAINT file_format_id_fkey FOREIGN KEY (format_id) REFERENCES file_format(id) ON DELETE CASCADE;");

        $this->execute("ALTER TABLE ONLY file
            ADD CONSTRAINT file_type_id_fkey FOREIGN KEY (type_id) REFERENCES file_type(id) ON DELETE CASCADE;");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE file_id_seq CASCADE;");
        $this->execute("DROP VIEW file_number;");
        $this->dropTable('file');
    }
}
