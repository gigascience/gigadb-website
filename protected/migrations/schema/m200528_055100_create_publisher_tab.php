<?php

class m200528_055100_create_publisher_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS publisher (
            id integer NOT NULL,
            name character varying(45) NOT NULL,
            description text DEFAULT ''::text NOT NULL);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS publisher_id_seq 
            START WITH 10 
            INCREMENT BY 1 
            NO MINVALUE 
            NO MAXVALUE 
            CACHE 1 
            OWNED BY publisher.id;");

        $this->execute("ALTER SEQUENCE publisher_id_seq 
            OWNED BY publisher.id;");

        $this->execute("ALTER TABLE ONLY publisher
            ALTER COLUMN id SET DEFAULT nextval('publisher_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY publisher
            ADD CONSTRAINT publisher_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE publisher_id_seq CASCADE;");
        $this->dropTable('publisher');
    }
}
