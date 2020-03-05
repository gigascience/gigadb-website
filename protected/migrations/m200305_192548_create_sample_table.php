<?php

class m200305_192548_create_sample_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE sample (
                id integer NOT NULL,
                species_id integer NOT NULL,
                name character varying(100) DEFAULT \'SAMPLE:SRS188811\'::character varying NOT NULL,
                consent_document character varying(45),
                submitted_id integer,
                submission_date date,
                contact_author_name character varying(45),
                contact_author_email character varying(100),
                sampling_protocol character varying(100));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE sample_id_seq
                START WITH 210
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE sample_id_seq 
                OWNED BY sample.id;'
        );

        $sql_createview = sprintf(
            'CREATE VIEW sample_number AS
                SELECT count(sample.id) AS count 
                FROM sample;'
        );

        $sql_altertab = sprintf(
            'ALTER TABLE ONLY sample 
                ALTER COLUMN id SET DEFAULT nextval(\'sample_id_seq\'::regclass);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_createview, $sql_altertab);
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
