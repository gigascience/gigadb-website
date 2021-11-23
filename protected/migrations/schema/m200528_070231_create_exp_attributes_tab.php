<?php

class m200528_070231_create_exp_attributes_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS exp_attributes (
            id integer NOT NULL,
            exp_id integer,
            attribute_id integer,
            value character varying(1000),
            units_id character varying(50));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS exp_attributes_id_seq
            START WITH 1
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE exp_attributes_id_seq 
            OWNED BY exp_attributes.id;");

        $this->execute("ALTER TABLE ONLY exp_attributes 
            ALTER COLUMN id SET DEFAULT nextval('exp_attributes_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY exp_attributes
            ADD CONSTRAINT exp_attributes_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY exp_attributes
            ADD CONSTRAINT exp_attributes_attribute_id_fkey FOREIGN KEY (attribute_id) REFERENCES attribute(id);");

        $this->execute("ALTER TABLE ONLY exp_attributes
            ADD CONSTRAINT exp_attributes_exp_id_fkey FOREIGN KEY (exp_id) REFERENCES experiment(id);");

        $this->execute("ALTER TABLE ONLY exp_attributes
            ADD CONSTRAINT exp_attributes_units_id_fkey FOREIGN KEY (units_id) REFERENCES unit(id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE exp_attributes_id_seq CASCADE;");
        $this->dropTable('exp_attributes');
    }
}
