<?php

class m200528_054960_create_author_rel_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS author_rel (
            id integer NOT NULL,
            author_id integer NOT NULL,
            related_author_id integer NOT NULL,
            relationship_id integer);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS author_rel_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE author_rel_id_seq 
            OWNED BY author_rel.id;");

        $this->execute("ALTER TABLE ONLY author_rel 
            ALTER COLUMN id SET DEFAULT nextval('author_rel_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY author_rel
            ADD CONSTRAINT author_rel_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE author_rel_id_seq CASCADE;");
        $this->dropTable('author_rel');
    }
}
