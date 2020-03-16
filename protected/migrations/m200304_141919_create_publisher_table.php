<?php

class m200304_141919_create_publisher_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE publisher (
                id integer NOT NULL,
                name character varying(45) NOT NULL,
                description text DEFAULT \'\'::text NOT NULL);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE publisher_id_seq 
                START WITH 10 
                INCREMENT BY 1 
                NO MINVALUE 
                NO MAXVALUE 
                CACHE 1 
                OWNED BY publisher.id;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE publisher_id_seq 
                OWNED BY publisher.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY publisher
                ALTER COLUMN id SET DEFAULT nextval(\'publisher_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY publisher
                ADD CONSTRAINT publisher_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array( $sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
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
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE publisher_id_seq CASCADE;')->execute();
        $this->dropTable('publisher');
    }
}
