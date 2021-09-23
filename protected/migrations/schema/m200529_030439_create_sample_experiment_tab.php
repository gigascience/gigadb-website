<?php

class m200529_030439_create_sample_experiment_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS sample_experiment (
            id integer NOT NULL,
            sample_id integer,
            experiment_id integer);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS sample_experiment_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE sample_experiment_id_seq 
            OWNED BY sample_experiment.id;");

        $this->execute("ALTER TABLE ONLY sample_experiment 
            ALTER COLUMN id SET DEFAULT nextval('sample_experiment_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY sample_experiment
            ADD CONSTRAINT sample_experiment_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY sample_experiment
            ADD CONSTRAINT sample_experiment_experiment_id_fkey FOREIGN KEY (experiment_id) REFERENCES experiment(id);");

        $this->execute("ALTER TABLE ONLY sample_experiment
            ADD CONSTRAINT sample_experiment_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES sample(id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE sample_experiment_id_seq CASCADE;");
        $this->dropTable('sample_experiment');
    }
}
