<?php

class m200529_024441_create_rss_message_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS rss_message (
            id integer NOT NULL,
            message character varying(128) NOT NULL,
            publication_date date DEFAULT ('now'::text)::date NOT NULL);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS rss_message_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE rss_message_id_seq 
            OWNED BY rss_message.id;");

        $this->execute("ALTER TABLE ONLY rss_message 
            ALTER COLUMN id SET DEFAULT nextval('rss_message_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY rss_message
            ADD CONSTRAINT rss_message_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE rss_message_id_seq CASCADE;");
        $this->dropTable('rss_message');
    }
}
