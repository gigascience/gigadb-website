<?php

class m200528_052900_create_alternative_identifiers_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS alternative_identifiers (
            id integer NOT NULL,
            sample_id integer NOT NULL,
            extdb_id integer NOT NULL,
            extdb_accession character varying(100));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS alternative_identifiers_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE alternative_identifiers_id_seq 
            OWNED BY alternative_identifiers.id;");

        $this->execute("ALTER TABLE ONLY alternative_identifiers 
            ALTER COLUMN id SET DEFAULT nextval('alternative_identifiers_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY alternative_identifiers
            ADD CONSTRAINT alternative_identifiers_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY alternative_identifiers
            ADD CONSTRAINT alternative_identifiers_extdb_id_fkey FOREIGN KEY (extdb_id) REFERENCES extdb(id);");

        $this->execute("ALTER TABLE ONLY alternative_identifiers
            ADD CONSTRAINT alternative_identifiers_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES sample(id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE alternative_identifiers_id_seq CASCADE;");
        $this->dropTable('alternative_identifiers');
    }
}
