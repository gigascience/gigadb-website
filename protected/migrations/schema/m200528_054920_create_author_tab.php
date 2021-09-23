<?php

class m200528_054920_create_author_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS author (
            id integer NOT NULL,
            surname character varying(255) NOT NULL,
            middle_name character varying(255),
            first_name character varying(255),
            orcid character varying(255),
            gigadb_user_id integer,
            custom_name character varying(100));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS author_id_seq
            START WITH 3500
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE author_id_seq 
            OWNED BY author.id;");

        $this->execute("ALTER TABLE ONLY author 
            ALTER COLUMN id SET DEFAULT nextval('author_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY author
            ADD CONSTRAINT author_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE author_id_seq CASCADE;");
        $this->dropTable('author');
    }
}
