<?php

class m200528_090557_create_file_attributes_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS file_attributes (
            id integer NOT NULL,
            file_id integer NOT NULL,
            attribute_id integer NOT NULL,
            value character varying(1000),
            unit_id character varying(30));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS file_attributes_id_seq
            START WITH 11000
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE file_attributes_id_seq 
            OWNED BY file_attributes.id;");

        $this->execute("ALTER TABLE ONLY file_attributes 
            ALTER COLUMN id SET DEFAULT nextval('file_attributes_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY file_attributes
            ADD CONSTRAINT file_attributes_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY file_attributes
            ADD CONSTRAINT file_attributes_attribute_id_fkey FOREIGN KEY (attribute_id) REFERENCES attribute(id);");

        $this->execute("ALTER TABLE ONLY file_attributes
            ADD CONSTRAINT file_attributes_file_id_fkey FOREIGN KEY (file_id) REFERENCES file(id) ON DELETE CASCADE;");

        $this->execute("ALTER TABLE ONLY file_attributes
            ADD CONSTRAINT file_attributes_unit_id_fkey FOREIGN KEY (unit_id) REFERENCES unit(id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE file_attributes_id_seq CASCADE;");
        $this->dropTable('file_attributes');
    }
}
