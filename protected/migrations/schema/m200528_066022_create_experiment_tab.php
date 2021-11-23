<?php

class m200528_066022_create_experiment_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS experiment (
            id integer NOT NULL,
            experiment_type character varying(100),
            experiment_name character varying(100),
            exp_description character varying(1000),
            dataset_id integer,
            \"protocols.io\" character varying(200));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS experiment_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE experiment_id_seq 
            OWNED BY experiment.id;");

        $this->execute("ALTER TABLE ONLY experiment 
            ALTER COLUMN id SET DEFAULT nextval('experiment_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY experiment
            ADD CONSTRAINT experiment_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY experiment
            ADD CONSTRAINT experiment_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE experiment_id_seq CASCADE;");
        $this->dropTable('experiment');
    }
}
