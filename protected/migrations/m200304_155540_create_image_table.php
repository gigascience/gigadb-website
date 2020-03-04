<?php

class m200304_155540_create_image_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->createTable('image', array(
            'id' => 'integer NOT NULL',
            'location' => 'string DEFAULT \'\'::character varying (255) NOT NULL',
            'tag' => 'string',
            'url' => 'string',
            'license' => 'text NOT NULL',
            'photographer' => 'string NOT NULL',
            'source' => 'string NOT NULL'
        ));

        // Create sequence using plain SQL
        Yii::app()->db->createCommand('CREATE SEQUENCE image_id_seq START WITH 31 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;')->execute();
        Yii::app()->db->createCommand('ALTER SEQUENCE image_id_seq OWNED BY image.id;')->execute();
        Yii::app()->db->createCommand('ALTER TABLE ONLY image ALTER COLUMN id SET DEFAULT nextval(\'image_id_seq\'::regclass);')->execute();

        // Add data to table
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
            'id' => '29',
            'location' => '100002_Macaca_mulatta.jpg',
            'tag' => 'Chinese Rhesus macaque',
            'url' => 'http://gigadb.org/images/data/cropped/100002_Macaca_mulatta.jpg',
            'license' => 'CC-BY',
            'photographer' => 'Geoff Gallice',
            'source' => 'Flickr: EOL Images'
        ));
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
            'id' => '25',
            'location' => '100004_Ailuropoda_melanoleuca.jpg',
            'tag' => 'Giant panda',
            'url' => 'http://gigadb.org/images/data/cropped/100004_Ailuropoda_melanoleuca.jpg',
            'license' => 'CC BY-SA',
            'photographer' => 'Shizhao',
            'source' => 'Wikimedia Commons'
        ));
    }

    public function safeDown()
    {
        $this->dropTable('image');
        Yii::app()->db->createCommand('DROP SEQUENCE image_id_seq;')->execute();
    }
}
