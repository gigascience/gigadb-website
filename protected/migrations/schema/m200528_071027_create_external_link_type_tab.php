<?php

class m200528_071027_create_external_link_type_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS external_link_type (
            id integer NOT NULL,
            name character varying(45) NOT NULL);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS external_link_type_id_seq
            START WITH 10
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE external_link_type_id_seq 
            OWNED BY external_link_type.id;");

        $this->execute("ALTER TABLE ONLY external_link_type 
            ALTER COLUMN id SET DEFAULT nextval('external_link_type_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY external_link_type
            ADD CONSTRAINT external_link_type_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE external_link_type_id_seq CASCADE;");
        $this->dropTable('external_link_type');
    }
}
