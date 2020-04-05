<?php

class m200305_165138_create_dataset_log_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE dataset_log (
                id integer NOT NULL,
                dataset_id integer NOT NULL,
                message text DEFAULT \'\'::text,
                created_at timestamp without time zone DEFAULT now(),
                model text,
                model_id integer,
                url text DEFAULT \'\'::text);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE dataset_log_id_seq
                START WITH 1200
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE dataset_log_id_seq 
                OWNED BY dataset_log.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY dataset_log 
                ALTER COLUMN id SET DEFAULT nextval(\'dataset_log_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY dataset_log
                ADD CONSTRAINT dataset_log_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY dataset_log
                ADD CONSTRAINT dataset_log_dataset_id_fkey FOREIGN KEY (dataset_id) 
                REFERENCES dataset(id) ON DELETE CASCADE;'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('dataset_log', array(
            'id' => '67',
            'dataset_id' =>'200',
            'message' => 'Dataset publish',
            'created_at' => '2015-05-11 03:00:01.186898',
            'model' => 'dataset',
            'model_id' => '200',
        ));
        $this->insert('dataset_log', array(
            'id' => '68',
            'dataset_id' =>'200',
            'message' => 'Description updated from : The workflow for the production of high-throughput sequencing data from nucleic acid samples is complex. There are a series of protocol steps to be followed in the preparation of samples for next-generation sequencing.  The quantification of bias in a number of protocol steps, namely DNA fractionation, blunting, phosphorylation, adapter ligation and library enrichment, remains to be determined.',
            'created_at' => '2015-05-11 03:00:01.306238',
            'model' => 'dataset',
            'model_id' => '200',
        ));
        $this->insert('dataset_log', array(
            'id' => '498',
            'dataset_id' =>'25',
            'message' => 'File 080620_I330_FC304NVAAXX_L3_PAfwDADHAAPE_1.fq.clean.gz updated',
            'created_at' => '2015-11-04 00:07:32.653238',
            'model' => 'File',
            'model_id' => '5783',
            'url' => '/adminFile/update/id/5783'
        ));
        $this->insert('dataset_log', array(
            'id' => '1176',
            'dataset_id' =>'15',
            'message' => 'Relationship added : DOI 200029',
            'created_at' => '2017-09-15 03:43:42.688242',
            'model' => 'relation',
            'model_id' => '157'
        ));
        $this->insert('dataset_log', array(
            'id' => '1177',
            'dataset_id' =>'15',
            'message' => 'Relationship removed : DOI 200029',
            'created_at' => '2017-09-15 03:43:53.252748',
            'model' => 'relation',
            'model_id' => '157'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE dataset_log_id_seq CASCADE;')->execute();
        $this->dropTable('dataset_log');
    }
}
