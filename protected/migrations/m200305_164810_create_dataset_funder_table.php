<?php

class m200305_164810_create_dataset_funder_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE dataset_funder (
                id integer NOT NULL,
                dataset_id integer NOT NULL,
                funder_id integer NOT NULL,
                grant_award text DEFAULT \'\'::text,
                comments text DEFAULT \'\'::text,
                awardee character varying(100));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE dataset_funder_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE dataset_funder_id_seq 
                OWNED BY dataset_funder.id;'
        );

        $sql_altertab = sprintf(
            'ALTER TABLE ONLY dataset_funder 
                ALTER COLUMN id SET DEFAULT nextval(\'dataset_funder_id_seq\'::regclass);'
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
