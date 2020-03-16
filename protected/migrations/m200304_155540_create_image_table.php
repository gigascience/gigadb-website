<?php

class m200304_155540_create_image_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE image (
                id integer NOT NULL,
                location character varying(200) DEFAULT \'\'::character varying NOT NULL,
                tag character varying(300),
                url character varying(256),
                license text NOT NULL,
                photographer character varying(128) NOT NULL,
                source character varying(256) NOT NULL);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE image_id_seq 
                START WITH 40 
                INCREMENT BY 1 
                NO MINVALUE 
                NO MAXVALUE CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE image_id_seq 
                OWNED BY image.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY image 
                ALTER COLUMN id SET DEFAULT nextval(\'image_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY image
                ADD CONSTRAINT image_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array( $sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table
        $this->insert('image', array(
            'id' => '13',
            'location' => '100003_Macaca_fascicularis.jpg',
            'tag' => 'Crab-eating macaque',
            'url' => 'http://gigadb.org/images/data/cropped/100003_Macaca_fascicularis.jpg',
            'license' => 'CC-BY',
            'photographer' => 'Michael Nordine',
            'source' => 'Flickr: EOL Images'
        ));
        $this->insert('image', array(
            'id' => '15',
            'location' => '100001_Escherichia_coli.jpg',
            'tag' => 'E. coli',
            'url' => 'http://gigadb.org/images/data/cropped/100001_Diverse_e_Coli.png',
            'license' => 'Public Domain, "I grant anyone the right to use this work for any purpose, without any conditions, unless such conditions are required by law."',
            'photographer' => 'Michael Van Woert, 1999',
            'source' => 'Wikimedia Commons'
        ));
        $this->insert('image', array(
            'id' => '25',
            'location' => '100004_Ailuropoda_melanoleuca.jpg',
            'tag' => 'Giant panda',
            'url' => 'http://gigadb.org/images/data/cropped/100004_Ailuropoda_melanoleuca.jpg',
            'license' => 'CC BY-SA',
            'photographer' => 'Shizhao',
            'source' => 'Wikimedia Commons'
        ));
        $this->insert('image', array(
            'id' => '29',
            'location' => '100002_Macaca_mulatta.jpg',
            'tag' => 'Chinese Rhesus macaque',
            'url' => 'http://gigadb.org/images/data/cropped/100002_Macaca_mulatta.jpg',
            'license' => 'CC-BY',
            'photographer' => 'Geoff Gallice',
            'source' => 'Flickr: EOL Images'
        ));
    }

    public function safeDown()
    {
        Yii::app()->db->createCommand('DROP SEQUENCE image_id_seq CASCADE;')->execute();
        $this->dropTable('image');
    }
}
