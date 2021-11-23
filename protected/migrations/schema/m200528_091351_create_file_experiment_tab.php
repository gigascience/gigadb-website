<?php

class m200528_091351_create_file_experiment_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS file_experiment (
            id integer NOT NULL,
            file_id integer,
            experiment_id integer);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS file_experiment_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE file_experiment_id_seq 
            OWNED BY file_experiment.id;");

        $this->execute("ALTER TABLE ONLY file_experiment 
            ALTER COLUMN id SET DEFAULT nextval('file_experiment_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY file_experiment
            ADD CONSTRAINT file_experiment_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY file_experiment
            ADD CONSTRAINT file_experiment_experiment_id_fkey FOREIGN KEY (experiment_id) REFERENCES experiment(id);");

        $this->execute("ALTER TABLE ONLY file_experiment
            ADD CONSTRAINT file_experiment_file_id_fkey FOREIGN KEY (file_id) REFERENCES file(id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE file_experiment_id_seq CASCADE;");
        $this->dropTable('file_experiment');
    }
}
