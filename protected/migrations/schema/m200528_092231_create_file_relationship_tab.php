<?php

class m200528_092231_create_file_relationship_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS file_relationship (
            id integer NOT NULL,
            file_id integer NOT NULL,
            related_file_id integer NOT NULL,
            relationship_id integer);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS file_relationship_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE file_relationship_id_seq OWNED BY file_relationship.id;");

        $this->execute("ALTER TABLE ONLY file_relationship 
            ALTER COLUMN id SET DEFAULT nextval('file_relationship_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY file_relationship
            ADD CONSTRAINT file_relationship_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY file_relationship
            ADD CONSTRAINT file_relationship_file_id_fkey FOREIGN KEY (file_id) REFERENCES file(id);");

        $this->execute("ALTER TABLE ONLY file_relationship
            ADD CONSTRAINT file_relationship_relationship_id_fkey FOREIGN KEY (relationship_id) REFERENCES relationship(id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE file_relationship_id_seq CASCADE;");
        $this->dropTable('file_relationship');
    }
}
