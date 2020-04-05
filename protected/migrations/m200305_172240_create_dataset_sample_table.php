<?php

class m200305_172240_create_dataset_sample_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE dataset_sample (
                id integer NOT NULL,
                dataset_id integer NOT NULL,
                sample_id integer NOT NULL);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE dataset_sample_id_seq
                START WITH 500
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE dataset_sample_id_seq 
                OWNED BY dataset_sample.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY dataset_sample 
                ALTER COLUMN id SET DEFAULT nextval(\'dataset_sample_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY dataset_sample
                ADD CONSTRAINT dataset_sample_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY dataset_sample
                ADD CONSTRAINT dataset_sample_dataset_id_fkey FOREIGN KEY (dataset_id) 
                REFERENCES dataset(id) ON DELETE CASCADE;'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY dataset_sample
                ADD CONSTRAINT dataset_sample_sample_id_fkey FOREIGN KEY (sample_id) 
                REFERENCES sample(id) ON DELETE CASCADE;'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('dataset_sample', array(
            'id' => '334',
            'dataset_id' =>'13',
            'sample_id' =>'334'
        ));
        $this->insert('dataset_sample', array(
            'id' => '336',
            'dataset_id' =>'15',
            'sample_id' =>'336'
        ));
        $this->insert('dataset_sample', array(
            'id' => '337',
            'dataset_id' =>'16',
            'sample_id' =>'337'
        ));
        $this->insert('dataset_sample', array(
            'id' => '453',
            'dataset_id' =>'25',
            'sample_id' =>'452'
        ));
        $this->insert('dataset_sample', array(
            'id' => '457',
            'dataset_id' =>'29',
            'sample_id' =>'456'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE dataset_sample_id_seq CASCADE;')->execute();
        $this->dropTable('dataset_sample');
    }
}
