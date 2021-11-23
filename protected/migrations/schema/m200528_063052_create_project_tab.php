<?php

class m200528_063052_create_project_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS project (
            id integer NOT NULL,
            url character varying(128) NOT NULL,
            name character varying(255) DEFAULT ''::character varying NOT NULL,
            image_location character varying(100));");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS project_id_seq
            START WITH 10
            INCREMENT BY 1
            NO MINVALUE
            NO MAXVALUE
            CACHE 1;");

        $this->execute("ALTER SEQUENCE project_id_seq 
            OWNED BY project.id;");

        $this->execute("ALTER TABLE ONLY project 
            ALTER COLUMN id SET DEFAULT nextval('project_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY project
            ADD CONSTRAINT project_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE project_id_seq CASCADE;");
        $this->dropTable('project');
    }
}
