<?php

class m200528_061933_create_dataset_log_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS dataset_log (
            id integer NOT NULL,
            dataset_id integer NOT NULL,
            message text DEFAULT ''::text,
            created_at timestamp without time zone DEFAULT now(),
            model text,
            model_id integer,
            url text DEFAULT ''::text);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS dataset_log_id_seq
            START WITH 1200
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE dataset_log_id_seq 
            OWNED BY dataset_log.id;");

        $this->execute("ALTER TABLE ONLY dataset_log 
            ALTER COLUMN id SET DEFAULT nextval('dataset_log_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY dataset_log
            ADD CONSTRAINT dataset_log_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY dataset_log
            ADD CONSTRAINT dataset_log_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE dataset_log_id_seq CASCADE;");
        $this->dropTable('dataset_log');
    }
}
