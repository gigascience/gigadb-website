<?php

class m200528_092609_create_file_sample_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS file_sample (
            id integer NOT NULL,
            sample_id integer NOT NULL,
            file_id integer NOT NULL);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS file_sample_id_seq
            START WITH 5800
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE file_sample_id_seq 
            OWNED BY file_sample.id;");

        $this->execute("ALTER TABLE ONLY file_sample 
            ALTER COLUMN id SET DEFAULT nextval('file_sample_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY file_sample
            ADD CONSTRAINT file_sample_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY file_sample
            ADD CONSTRAINT file_sample_file_id_fkey FOREIGN KEY (file_id) 
            REFERENCES file(id);");

        $this->execute("ALTER TABLE ONLY file_sample
            ADD CONSTRAINT file_sample_sample_id_fkey FOREIGN KEY (sample_id) 
            REFERENCES sample(id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE file_sample_id_seq CASCADE;");
        $this->dropTable('file_sample');
    }
}
