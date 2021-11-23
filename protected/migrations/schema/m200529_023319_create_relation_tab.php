<?php

class m200529_023319_create_relation_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS relation (
            id integer NOT NULL,
            dataset_id integer NOT NULL,
            related_doi character varying(15) NOT NULL,
            relationship_id integer);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS relation_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE relation_id_seq 
            OWNED BY relation.id;");

        $this->execute("ALTER TABLE ONLY relation 
            ALTER COLUMN id SET DEFAULT nextval('relation_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY relation
            ADD CONSTRAINT relation_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY relation
            ADD CONSTRAINT relation_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;");

        $this->execute("ALTER TABLE ONLY relation
            ADD CONSTRAINT relation_relationship_fkey FOREIGN KEY (relationship_id) REFERENCES relationship(id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE relation_id_seq CASCADE;");
        $this->dropTable('relation');
    }
}
