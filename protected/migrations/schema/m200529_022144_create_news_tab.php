<?php

class m200529_022144_create_news_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS news (
            id integer NOT NULL,
            title character varying(200) NOT NULL,
            body text DEFAULT ''::text NOT NULL,
            start_date date NOT NULL,
            end_date date NOT NULL);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS news_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE news_id_seq 
            OWNED BY news.id;");

        $this->execute("ALTER TABLE ONLY news 
            ALTER COLUMN id SET DEFAULT nextval('news_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY news
            ADD CONSTRAINT news_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE news_id_seq CASCADE;");
        $this->dropTable('news');
    }
}
