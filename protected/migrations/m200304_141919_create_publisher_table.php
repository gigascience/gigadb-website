<?php

class m200304_141919_create_publisher_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->createTable('publisher', array(
            'id' => 'integer NOT NULL',
            'name' => 'string NOT NULL',
            'description' => 'text DEFAULT \'\'::text NOT NULL'
        ));

        // Create sequence using plain SQL
        Yii::app()->db->createCommand('CREATE SEQUENCE publisher_id_seq START WITH 3 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1 OWNED BY publisher.id;')->execute();
        Yii::app()->db->createCommand('ALTER SEQUENCE publisher_id_seq OWNED BY publisher.id;')->execute();
        Yii::app()->db->createCommand('ALTER TABLE ONLY publisher ALTER COLUMN id SET DEFAULT nextval(\'publisher_id_seq\'::regclass);')->execute();

        // Add data to table
        $this->insert('publisher', array(
            'id' => '1',
            'name' =>'GigaScience'
        ));
        $this->insert('publisher', array(
            'id' => '2',
            'name' =>'BGI Shenzhen'
        ));
        $this->insert('publisher', array(
            'id' => '3',
            'name' =>'GigaScience Database'
        ));
        $this->insert('publisher', array(
            'id' => '4',
            'name' =>'UC Davis'
        ));
    }

    public function safeDown()
    {
         $this->dropTable('publisher');
         Yii::app()->db->createCommand('DROP SEQUENCE publisher_id_seq;')->execute();
    }
}
