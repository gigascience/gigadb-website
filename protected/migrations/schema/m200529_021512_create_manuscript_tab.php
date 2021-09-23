<?php

class m200529_021512_create_manuscript_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS manuscript (
            id integer NOT NULL,
            identifier character varying(32) NOT NULL,
            pmid integer,
            dataset_id integer NOT NULL);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS manuscript_id_seq
            START WITH 500
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE manuscript_id_seq 
            OWNED BY manuscript.id;");

        $this->execute("ALTER TABLE ONLY manuscript 
            ALTER COLUMN id SET DEFAULT nextval('manuscript_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY manuscript
            ADD CONSTRAINT manuscript_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY manuscript
            ADD CONSTRAINT manuscript_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE manuscript_id_seq CASCADE;");
        $this->dropTable('manuscript');
    }
}
