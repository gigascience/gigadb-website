<?php

class m200305_190311_create_manuscript_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE manuscript (
                id integer NOT NULL,
                identifier character varying(32) NOT NULL,
                pmid integer,
                dataset_id integer NOT NULL);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE manuscript_id_seq
                START WITH 500
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE manuscript_id_seq 
                OWNED BY manuscript.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY manuscript 
                ALTER COLUMN id SET DEFAULT nextval(\'manuscript_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY manuscript
                ADD CONSTRAINT manuscript_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY manuscript
                ADD CONSTRAINT manuscript_dataset_id_fkey FOREIGN KEY (dataset_id) 
                REFERENCES dataset(id) ON DELETE CASCADE;'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('manuscript', array(
            'id' => '9',
            'identifier' =>'10.1038/nbt.1992',
            'pmid' => '22002653',
            'dataset_id' => '13'
        ));
        $this->insert('manuscript', array(
            'id' => '13',
            'identifier' =>'10.1056/NEJMoa1107643',
            'pmid' => '21793736',
            'dataset_id' => '15'
        ));
        $this->insert('manuscript', array(
            'id' => '22',
            'identifier' =>'10.1038/nature08696',
            'pmid' => '20010809',
            'dataset_id' => '25'
        ));
        $this->insert('manuscript', array(
            'id' => '24',
            'identifier' =>'10.1038/nbt.1992',
            'pmid' => '22002653',
            'dataset_id' => '29'
        ));
        $this->insert('manuscript', array(
            'id' => '86',
            'identifier' =>'10.1186/2047-217X-3-10',
            'dataset_id' => '144'
        ));
        $this->insert('manuscript', array(
            'id' => '118',
            'identifier' =>'10.1186/2047-217X-3-26',
            'dataset_id' => '16'
        ));
        $this->insert('manuscript', array(
            'id' => '162',
            'identifier' =>'10.1186/2047-217X-3-27',
            'dataset_id' => '16'
        ));
        $this->insert('manuscript', array(
            'id' => '167',
            'identifier' =>'10.1126/science.1251385',
            'dataset_id' => '16'
        ));
        $this->insert('manuscript', array(
            'id' => '218',
            'identifier' =>'10.1126/science.1253451',
            'dataset_id' => '16'
        ));
        $this->insert('manuscript', array(
            'id' => '281',
            'identifier' =>'10.1186/s13742-015-0064-7',
            'dataset_id' => '200'
        ));
        $this->insert('manuscript', array(
            'id' => '347',
            'identifier' =>'10.1101/069625',
            'dataset_id' => '268'
        ));
        $this->insert('manuscript', array(
            'id' => '473',
            'identifier' =>'10.1093/gigascience/gix082',
            'dataset_id' => '15'
        ));
        $this->insert('manuscript', array(
            'id' => '475',
            'identifier' =>'10.1093/gigascience/gix078',
            'dataset_id' => '15'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE manuscript_id_seq CASCADE;')->execute();
        $this->dropTable('manuscript');
    }
}
