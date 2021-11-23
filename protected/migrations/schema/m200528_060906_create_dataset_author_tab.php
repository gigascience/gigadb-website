<?php

class m200528_060906_create_dataset_author_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS dataset_author (
            id integer NOT NULL,
            dataset_id integer NOT NULL,
            author_id integer NOT NULL,
            rank integer DEFAULT 0,
            role character varying(30));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS dataset_author_id_seq
            START WITH 200
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE dataset_author_id_seq 
            OWNED BY dataset_author.id;");

        $this->execute("ALTER TABLE ONLY dataset_author 
            ALTER COLUMN id SET DEFAULT nextval('dataset_author_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY dataset_author
            ADD CONSTRAINT dataset_author_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY dataset_author
            ADD CONSTRAINT dataset_author_author_id_fkey FOREIGN KEY (author_id) REFERENCES author(id) ON DELETE CASCADE;");

        $this->execute("ALTER TABLE ONLY dataset_author
            ADD CONSTRAINT dataset_author_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE dataset_author_id_seq CASCADE;");
        $this->dropTable('dataset_author');
    }
}
