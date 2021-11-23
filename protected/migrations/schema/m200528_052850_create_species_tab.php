<?php

class m200528_052850_create_species_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS species (
            id integer NOT NULL,
            tax_id integer NOT NULL,
            common_name character varying(128),
            genbank_name character varying(128),
            scientific_name character varying(128) NOT NULL,
            eol_link character varying(100));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS species_id_seq
            START WITH 500
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE species_id_seq 
            OWNED BY species.id;");

        $this->execute("ALTER TABLE ONLY species 
            ALTER COLUMN id SET DEFAULT nextval('species_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY species
            ADD CONSTRAINT species_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE species_id_seq CASCADE;");
        $this->dropTable('species');
    }
}
