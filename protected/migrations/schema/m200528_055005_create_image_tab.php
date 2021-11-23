<?php

class m200528_055005_create_image_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS image (
            id integer NOT NULL,
            location character varying(200) DEFAULT ''::character varying NOT NULL,
            tag character varying(300),
            url character varying(256),
            license text NOT NULL,
            photographer character varying(128) NOT NULL,
            source character varying(256) NOT NULL);");

        $this->execute("CREATE SEQUENCE IF NOT EXISTS image_id_seq 
            START WITH 40 
            INCREMENT BY 1 
            NO MINVALUE 
            NO MAXVALUE CACHE 1;");

        $this->execute("ALTER SEQUENCE image_id_seq 
            OWNED BY image.id;");

        $this->execute("ALTER TABLE ONLY image 
            ALTER COLUMN id SET DEFAULT nextval('image_id_seq'::regclass);");

        $this->execute("ALTER TABLE ONLY image
            ADD CONSTRAINT image_pkey PRIMARY KEY (id);");
    }

    public function safeDown()
    {
        $this->execute("DROP SEQUENCE image_id_seq CASCADE;");
        $this->dropTable('image');
    }
}
