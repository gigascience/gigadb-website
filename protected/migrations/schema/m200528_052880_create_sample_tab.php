<?php

class m200528_052880_create_sample_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS sample (
            id integer NOT NULL,
            species_id integer NOT NULL,
            name character varying(100) DEFAULT 'SAMPLE:SRS188811'::character varying NOT NULL,
            consent_document character varying(45),
            submitted_id integer,
            submission_date date,
            contact_author_name character varying(45),
            contact_author_email character varying(100),
            sampling_protocol character varying(100));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS sample_id_seq
            START WITH 500
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE sample_id_seq 
            OWNED BY sample.id;");

        $this->execute("CREATE OR REPLACE VIEW sample_number AS
            SELECT count(sample.id) AS count FROM sample;");

        $this->execute("ALTER TABLE ONLY sample 
            ALTER COLUMN id SET DEFAULT nextval('sample_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY sample
            ADD CONSTRAINT sample_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY sample
            ADD CONSTRAINT sample_species_id_fkey FOREIGN KEY (species_id) REFERENCES species(id) ON DELETE CASCADE;");

        $this->execute("ALTER TABLE ONLY sample
            ADD CONSTRAINT sample_submitted_id_fkey FOREIGN KEY (submitted_id) REFERENCES gigadb_user(id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE sample_id_seq CASCADE;");
        // Use CASCADE to drop view sample_number as dependent on Sample table
        $this->execute("DROP TABLE sample CASCADE;");
    }
}
