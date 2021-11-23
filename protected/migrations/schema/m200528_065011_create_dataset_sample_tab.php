<?php

class m200528_065011_create_dataset_sample_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS dataset_sample (
            id integer NOT NULL,
            dataset_id integer NOT NULL,
            sample_id integer NOT NULL);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS dataset_sample_id_seq
            START WITH 500
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1");

        $this->execute("ALTER SEQUENCE dataset_sample_id_seq 
            OWNED BY dataset_sample.id;");

        $this->execute("ALTER TABLE ONLY dataset_sample 
            ALTER COLUMN id SET DEFAULT nextval('dataset_sample_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY dataset_sample
            ADD CONSTRAINT dataset_sample_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY dataset_sample
            ADD CONSTRAINT dataset_sample_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;");

        $this->execute("ALTER TABLE ONLY dataset_sample
            ADD CONSTRAINT dataset_sample_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES sample(id) ON DELETE CASCADE;");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE dataset_sample_id_seq CASCADE;");
        $this->dropTable('dataset_sample');
    }
}
