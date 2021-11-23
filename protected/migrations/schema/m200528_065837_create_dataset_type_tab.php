<?php

class m200528_065837_create_dataset_type_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS dataset_type (
            id integer NOT NULL,
            dataset_id integer NOT NULL,
            type_id integer);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS dataset_type_id_seq
            START WITH 50
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE dataset_type_id_seq 
            OWNED BY dataset_type.id;");

        $this->execute("ALTER TABLE ONLY dataset_type 
            ALTER COLUMN id SET DEFAULT nextval('dataset_type_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY dataset_type
            ADD CONSTRAINT dataset_type_pkey PRIMARY KEY (id);");

        $this->execute("ALTER TABLE ONLY dataset_type
            ADD CONSTRAINT dataset_type_dataset_id_fkey FOREIGN KEY (dataset_id) REFERENCES dataset(id) ON DELETE CASCADE;");

        $this->execute("ALTER TABLE ONLY dataset_type
            ADD CONSTRAINT dataset_type_type_id_fkey FOREIGN KEY (type_id) REFERENCES type(id) ON DELETE CASCADE;");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE dataset_type_id_seq CASCADE;");
        $this->dropTable('dataset_type');
    }
}
