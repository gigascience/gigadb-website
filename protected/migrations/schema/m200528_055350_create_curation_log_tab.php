<?php

class m200528_055350_create_curation_log_tab extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS curation_log (
            id integer NOT NULL,
            dataset_id integer NOT NULL,
            creation_date date,
            created_by character varying(100),
            last_modified_date date,
            last_modified_by character varying(100),
            action character varying(100),
            comments character varying(1000));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS curation_log_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE curation_log_id_seq 
            OWNED BY curation_log.id;");

        $this->execute("ALTER TABLE ONLY curation_log 
            ALTER COLUMN id SET DEFAULT nextval('curation_log_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY curation_log
            ADD CONSTRAINT curation_log_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY curation_log
            ADD CONSTRAINT curation_log_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE curation_log_id_seq CASCADE;");
        $this->dropTable('curation_log');
    }
}
