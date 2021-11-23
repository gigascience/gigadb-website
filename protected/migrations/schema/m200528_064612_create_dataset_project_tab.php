<?php

class m200528_064612_create_dataset_project_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS dataset_project (
            id integer NOT NULL,
            dataset_id integer,
            project_id integer);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS dataset_project_id_seq
            START WITH 20
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1");

        $this->execute("ALTER SEQUENCE dataset_project_id_seq 
            OWNED BY dataset_project.id;");

        $this->execute("ALTER TABLE ONLY dataset_project
            ALTER COLUMN id SET DEFAULT nextval('dataset_project_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY dataset_project
            ADD CONSTRAINT dataset_project_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY dataset_project
            ADD CONSTRAINT dataset_project_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;");

        $this->execute("ALTER TABLE ONLY dataset_project
            ADD CONSTRAINT dataset_project_project_id_fkey FOREIGN KEY (project_id) REFERENCES project(id) ON DELETE CASCADE;");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE dataset_project_id_seq CASCADE;");
        $this->dropTable('dataset_project');
    }
}
