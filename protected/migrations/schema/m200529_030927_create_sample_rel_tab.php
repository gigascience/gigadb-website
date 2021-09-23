<?php

class m200529_030927_create_sample_rel_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS sample_rel (
            id integer NOT NULL,
            sample_id integer NOT NULL,
            related_sample_id integer NOT NULL,
            relationship_id integer);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS sample_rel_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE sample_rel_id_seq 
            OWNED BY sample_rel.id;");

        $this->execute("ALTER TABLE ONLY sample_rel 
            ALTER COLUMN id SET DEFAULT nextval('sample_rel_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY sample_rel
            ADD CONSTRAINT sample_rel_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY sample_rel
            ADD CONSTRAINT sample_rel_relationship_id_fkey FOREIGN KEY (relationship_id) REFERENCES relationship(id);");

        $this->execute("ALTER TABLE ONLY sample_rel
            ADD CONSTRAINT sample_rel_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES sample(id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE sample_rel_id_seq CASCADE;");
        $this->dropTable('sample_rel');
    }
}
