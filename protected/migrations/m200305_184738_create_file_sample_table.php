<?php

class m200305_184738_create_file_sample_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE file_sample (
                id integer NOT NULL,
                sample_id integer NOT NULL,
                file_id integer NOT NULL);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE file_sample_id_seq
                START WITH 5800
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE file_sample_id_seq 
                OWNED BY file_sample.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY file_sample 
                ALTER COLUMN id SET DEFAULT nextval(\'file_sample_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY file_sample
                ADD CONSTRAINT file_sample_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY file_sample
                ADD CONSTRAINT file_sample_file_id_fkey FOREIGN KEY (file_id) 
                REFERENCES file(id);'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY file_sample
                ADD CONSTRAINT file_sample_sample_id_fkey FOREIGN KEY (sample_id) 
                REFERENCES sample(id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('file_sample', array(
            'id' => '3995',
            'sample_id' =>'334',
            'file_id' => '4281'
        ));
        $this->insert('file_sample', array(
            'id' => '4000',
            'sample_id' =>'334',
            'file_id' => '4286'
        ));
        $this->insert('file_sample', array(
            'id' => '4003',
            'sample_id' =>'334',
            'file_id' => '4289'
        ));
        $this->insert('file_sample', array(
            'id' => '4012',
            'sample_id' =>'334',
            'file_id' => '4298'
        ));
        $this->insert('file_sample', array(
            'id' => '4037',
            'sample_id' =>'336',
            'file_id' => '4324'
        ));
        $this->insert('file_sample', array(
            'id' => '4041',
            'sample_id' =>'336',
            'file_id' => '4328'
        ));
        $this->insert('file_sample', array(
            'id' => '4042',
            'sample_id' =>'336',
            'file_id' => '4329'
        ));
        $this->insert('file_sample', array(
            'id' => '4049',
            'sample_id' =>'336',
            'file_id' => '4336'
        ));
        $this->insert('file_sample', array(
            'id' => '4053',
            'sample_id' =>'337',
            'file_id' => '4341'
        ));
        $this->insert('file_sample', array(
            'id' => '5203',
            'sample_id' =>'452',
            'file_id' => '5717'
        ));
        $this->insert('file_sample', array(
            'id' => '5207',
            'sample_id' =>'452',
            'file_id' => '5721'
        ));
        $this->insert('file_sample', array(
            'id' => '5423',
            'sample_id' =>'452',
            'file_id' => '5937'
        ));
        $this->insert('file_sample', array(
            'id' => '5479',
            'sample_id' =>'452',
            'file_id' => '5993'
        ));
        $this->insert('file_sample', array(
            'id' => '5687',
            'sample_id' =>'456',
            'file_id' => '6201'
        ));
        $this->insert('file_sample', array(
            'id' => '5689',
            'sample_id' =>'456',
            'file_id' => '6203'
        ));
        $this->insert('file_sample', array(
            'id' => '5690',
            'sample_id' =>'456',
            'file_id' => '6204'
        ));
        $this->insert('file_sample', array(
            'id' => '5691',
            'sample_id' =>'456',
            'file_id' => '6205'
        ));
        $this->insert('file_sample', array(
            'id' => '15751',
            'sample_id' =>'337',
            'file_id' => '81839'
        ));
        $this->insert('file_sample', array(
            'id' => '15752',
            'sample_id' =>'337',
            'file_id' => '81840'
        ));
        $this->insert('file_sample', array(
            'id' => '15753',
            'sample_id' =>'337',
            'file_id' => '81841'
        ));
        $this->insert('file_sample', array(
            'id' => '15754',
            'sample_id' =>'337',
            'file_id' => '81842'
        ));
        $this->insert('file_sample', array(
            'id' => '15755',
            'sample_id' =>'337',
            'file_id' => '81843'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE file_sample_id_seq CASCADE;')->execute();
        $this->dropTable('file_sample');
    }
}
