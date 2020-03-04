<?php

class m200304_174810_create_search_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->createTable('search', array(
            'id' => 'integer NOT NULL',
            'user_id' => 'integer NOT NULL',
            'name' => 'string NOT NULL',
            'query' => 'text NOT NULL',
            'result' => 'text',
        ));


        // Create sequence using plain SQL
        Yii::app()->db->createCommand('CREATE SEQUENCE search_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;')->execute();
        Yii::app()->db->createCommand('ALTER SEQUENCE search_id_seq OWNED BY search.id;')->execute();
        Yii::app()->db->createCommand('ALTER TABLE ONLY search ALTER COLUMN id SET DEFAULT nextval(\'search_id_seq\'::regclass);')->execute();

        // No search rows to be added
    }

    public function safeDown()
    {
        $this->dropTable('search');
        Yii::app()->db->createCommand('DROP SEQUENCE search_id_seq;')->execute();
    }
}
