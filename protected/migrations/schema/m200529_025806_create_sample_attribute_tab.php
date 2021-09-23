<?php

class m200529_025806_create_sample_attribute_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS sample_attribute (
            id integer NOT NULL,
            sample_id integer NOT NULL,
            attribute_id integer NOT NULL,
            value character varying(10000),
            unit_id character varying(30));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS sample_attribute_id_seq
            START WITH 30000
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE sample_attribute_id_seq 
            OWNED BY sample_attribute.id;");

        $this->execute("ALTER TABLE ONLY sample_attribute 
            ALTER COLUMN id SET DEFAULT nextval('sample_attribute_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY sample_attribute
            ADD CONSTRAINT sample_attribute_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY sample_attribute
            ADD CONSTRAINT sample_attribute_fkey FOREIGN KEY (attribute_id) REFERENCES attribute(id);");

        $this->execute("ALTER TABLE ONLY sample_attribute
            ADD CONSTRAINT sample_attribute_sample_id_fkey FOREIGN KEY (sample_id) REFERENCES sample(id) ON DELETE CASCADE;");

        $this->execute("ALTER TABLE ONLY sample_attribute
            ADD CONSTRAINT sample_attribute_unit_id_fkey FOREIGN KEY (unit_id) REFERENCES unit(id);");

        $this->execute("CREATE INDEX fki_sample_attribute_fkey ON sample_attribute USING btree (attribute_id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE sample_attribute_id_seq CASCADE;");
        $this->dropTable('sample_attribute');
    }
}
