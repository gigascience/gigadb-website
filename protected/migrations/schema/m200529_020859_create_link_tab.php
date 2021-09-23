<?php

class m200529_020859_create_link_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS link (
            id integer NOT NULL,
            dataset_id integer NOT NULL,
            is_primary boolean DEFAULT false NOT NULL,
            link character varying(100) NOT NULL,
            description character varying(200));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS link_id_seq
            START WITH 80
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE link_id_seq 
            OWNED BY link.id;");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS link_prefix_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER TABLE ONLY link 
            ALTER COLUMN id SET DEFAULT nextval('link_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY link
            ADD CONSTRAINT link_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY link
            ADD CONSTRAINT link_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE link_id_seq CASCADE;");
        $this->execute("DROP SEQUENCE link_prefix_id_seq CASCADE;");
        $this->dropTable('link');
    }
}
