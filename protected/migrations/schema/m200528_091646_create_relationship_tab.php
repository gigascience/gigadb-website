<?php

class m200528_091646_create_relationship_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS relationship (
            id integer NOT NULL,
            name character varying(100));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS relationship_id_seq
            START WITH 40
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER TABLE ONLY relationship 
            ALTER COLUMN id SET DEFAULT nextval('relationship_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY relationship
            ADD CONSTRAINT relationship_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE relationship_id_seq CASCADE;");
        $this->dropTable('relationship');
    }
}
