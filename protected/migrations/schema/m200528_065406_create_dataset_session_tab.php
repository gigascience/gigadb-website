<?php

class m200528_065406_create_dataset_session_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS dataset_session (
            id integer NOT NULL,
            identifier text NOT NULL,
            dataset text,
            dataset_id text,
            datasettypes text,
            images text,
            authors text,
            projects text,
            links text,
            \"externalLinks\" text,
            relations text,
            samples text);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS dataset_session_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE dataset_session_id_seq 
            OWNED BY dataset_session.id;");

        $this->execute("ALTER TABLE ONLY dataset_session 
            ALTER COLUMN id SET DEFAULT nextval('dataset_session_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY dataset_session
            ADD CONSTRAINT dataset_session_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE dataset_session_id_seq CASCADE;");
        $this->dropTable('dataset_session');
    }
}
