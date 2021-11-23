<?php

class m200528_061339_create_dataset_funder_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS dataset_funder (
            id integer NOT NULL,
            dataset_id integer NOT NULL,
            funder_id integer NOT NULL,
            grant_award text DEFAULT ''::text,
            comments text DEFAULT ''::text,
            awardee character varying(100));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS dataset_funder_id_seq
            START WITH 50
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE dataset_funder_id_seq 
            OWNED BY dataset_funder.id;");

        $this->execute("ALTER TABLE ONLY dataset_funder 
            ALTER COLUMN id SET DEFAULT nextval('dataset_funder_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY dataset_funder
            ADD CONSTRAINT dataset_funder_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY dataset_funder
            ADD CONSTRAINT dataset_funder_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;");

        $this->execute("ALTER TABLE ONLY dataset_funder
            ADD CONSTRAINT dataset_funder_funder_id_fkey FOREIGN KEY (funder_id) REFERENCES funder_name(id) ON DELETE CASCADE;");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE dataset_funder_id_seq CASCADE;");
        $this->dropTable('dataset_funder');
    }
}
