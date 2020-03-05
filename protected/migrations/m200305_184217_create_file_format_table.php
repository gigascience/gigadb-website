<?php

class m200305_184217_create_file_format_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE file_format (
                id integer NOT NULL,
                name character varying(20) NOT NULL,
                description text DEFAULT \'\'::text NOT NULL,
                edam_ontology_id character varying(100));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE file_format_id_seq
                START WITH 26
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE file_format_id_seq 
                OWNED BY file_format.id;'
        );

        $sql_altertab = sprintf(
            'ALTER TABLE ONLY file_format 
                ALTER COLUMN id SET DEFAULT nextval(\'file_format_id_seq\'::regclass);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab);
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
        $this->dropTable('publisher');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE publisher_id_seq;')->execute();
    }
}
