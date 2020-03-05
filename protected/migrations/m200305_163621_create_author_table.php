<?php

class m200305_163621_create_author_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE author (
                id integer NOT NULL,
                surname character varying(255) NOT NULL,
                middle_name character varying(255),
                first_name character varying(255),
                orcid character varying(255),
                gigadb_user_id integer,
                custom_name character varying(100));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE author_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE author_id_seq 
                OWNED BY author.id;'
        );

        $sql_altertab = sprintf(
            'ALTER TABLE ONLY author 
                ALTER COLUMN id SET DEFAULT nextval(\'author_id_seq\'::regclass);'
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
