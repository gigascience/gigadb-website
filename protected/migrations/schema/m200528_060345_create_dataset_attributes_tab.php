<?php

class m200528_060345_create_dataset_attributes_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS dataset_attributes (
            id integer NOT NULL,
            dataset_id integer,
            attribute_id integer,
            value character varying(200),
            units_id character varying(30),
            image_id integer,
            until_date date);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS dataset_attributes_id_seq
            START WITH 2500
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE dataset_attributes_id_seq 
            OWNED BY dataset_attributes.id;");

        $this->execute("ALTER TABLE ONLY dataset_attributes 
            ALTER COLUMN id SET DEFAULT nextval('dataset_attributes_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY dataset_attributes
            ADD CONSTRAINT dataset_attributes_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY dataset_attributes
            ADD CONSTRAINT dataset_attributes_attribute_id_fkey FOREIGN KEY (attribute_id) REFERENCES attribute(id);");

        $this->execute("ALTER TABLE ONLY dataset_attributes
            ADD CONSTRAINT dataset_attributes_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id);");

        $this->execute("ALTER TABLE ONLY dataset_attributes
            ADD CONSTRAINT dataset_attributes_units_id_fkey FOREIGN KEY (units_id) REFERENCES unit(id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE dataset_attributes_id_seq CASCADE;");
        $this->dropTable('dataset_attributes');
    }
}
