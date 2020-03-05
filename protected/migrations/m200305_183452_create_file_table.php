<?php

class m200305_183452_create_file_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE file (
                id integer NOT NULL,
                dataset_id integer NOT NULL,
                name character varying(200) NOT NULL,
                location character varying(500) NOT NULL,
                extension character varying(100) NOT NULL,
                size bigint NOT NULL,
                description text DEFAULT \'\'::text NOT NULL,
                date_stamp date,
                format_id integer,
                type_id integer,
                code character varying(200) DEFAULT \'FILE_CODE\'::character varying,
                index4blast character varying(50),
                download_count integer DEFAULT 0 NOT NULL,
                alternative_location character varying(200));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE file_id_seq
                START WITH 6716
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE file_id_seq 
                OWNED BY file.id;'
        );

        $sql_createview = sprintf(
            'CREATE VIEW file_number AS
                SELECT count(file.id) AS count 
                FROM file;'
        );

        $sql_altertab = sprintf(
            'ALTER TABLE ONLY file 
                ALTER COLUMN id SET DEFAULT nextval(\'file_id_seq\'::regclass);'
        );

        $sql_cmds = array( $sql_createtab, $sql_createseq, $sql_alterseq, $sql_createview, $sql_altertab);
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
