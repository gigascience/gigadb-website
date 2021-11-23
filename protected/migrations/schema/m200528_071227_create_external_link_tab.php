<?php

class m200528_071227_create_external_link_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS external_link (
            id integer NOT NULL,
            dataset_id integer NOT NULL,
            url character varying(300) NOT NULL,
            external_link_type_id integer NOT NULL);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS external_link_id_seq
            START WITH 1000
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE external_link_id_seq 
            OWNED BY external_link.id;");

        $this->execute("ALTER TABLE ONLY external_link 
            ALTER COLUMN id SET DEFAULT nextval('external_link_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY external_link
            ADD CONSTRAINT external_link_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY external_link
            ADD CONSTRAINT external_link_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;");

        $this->execute("ALTER TABLE ONLY external_link
            ADD CONSTRAINT external_link_external_link_type_id_fkey FOREIGN KEY (external_link_type_id) REFERENCES external_link_type(id) ON DELETE CASCADE;");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE external_link_id_seq CASCADE;");
        $this->dropTable('external_link');
    }
}
