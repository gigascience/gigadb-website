<?php

class m200528_053712_create_attribute_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS attribute (
            id integer NOT NULL,
            attribute_name character varying(100),
            definition character varying(1000),
            model character varying(100),
            structured_comment_name character varying(100),
            value_syntax character varying(500),
            allowed_units character varying(100),
            occurance character varying(5),
            ontology_link character varying(1000),
            note character varying(100));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS attribute_id_seq
            START WITH 700
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE attribute_id_seq 
            OWNED BY attribute.id;");

        $this->execute("ALTER TABLE ONLY attribute 
            ALTER COLUMN id SET DEFAULT nextval('attribute_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY attribute
            ADD CONSTRAINT attribute_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE attribute_id_seq CASCADE;");
        $this->dropTable('attribute');
    }
}
