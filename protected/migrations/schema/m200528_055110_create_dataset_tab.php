<?php

class m200528_055110_create_dataset_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS dataset (
            id integer NOT NULL,
            submitter_id integer NOT NULL,
            image_id integer,
            identifier character varying(32) NOT NULL,
            title character varying(300) NOT NULL,
            description text DEFAULT ''::text NOT NULL,
            dataset_size bigint NOT NULL,
            ftp_site character varying(100) NOT NULL,
            upload_status character varying(45) DEFAULT 'AuthorReview'::character varying NOT NULL,
            excelfile character varying(50),
            excelfile_md5 character varying(32),
            publication_date date,
            modification_date date,
            publisher_id integer,
            token character varying(16) DEFAULT NULL::character varying,
            fairnuse date,
            curator_id integer,
            manuscript_id character varying(50),
            handing_editor character varying(50));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS dataset_id_seq 
            START WITH 50 
            INCREMENT BY 1 
            NO MINVALUE 
            NO MAXVALUE 
            CACHE 1 
            OWNED BY dataset.id;");

        $this->execute("ALTER SEQUENCE dataset_id_seq 
            OWNED BY dataset.id;");

        $this->execute("ALTER TABLE ONLY dataset 
            ALTER COLUMN id SET DEFAULT nextval('dataset_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY dataset
            ADD CONSTRAINT dataset_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY dataset
            ADD CONSTRAINT dataset_curator_id FOREIGN KEY (curator_id) REFERENCES gigadb_user(id);");

        $this->execute("ALTER TABLE ONLY dataset
            ADD CONSTRAINT dataset_image_id_fkey FOREIGN KEY (image_id) REFERENCES image(id) ON DELETE SET NULL;");

        $this->execute("ALTER TABLE ONLY dataset
            ADD CONSTRAINT dataset_publisher_id_fkey FOREIGN KEY (publisher_id) REFERENCES publisher(id) ON DELETE SET NULL;");

        $this->execute("ALTER TABLE ONLY dataset
            ADD CONSTRAINT dataset_submitter_id_fkey FOREIGN KEY (submitter_id) REFERENCES gigadb_user(id) ON DELETE RESTRICT;");

        $this->execute("CREATE UNIQUE INDEX identifier_idx ON dataset 
            USING btree (identifier);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE dataset_id_seq CASCADE;");
        $this->dropTable('dataset');
    }
}
