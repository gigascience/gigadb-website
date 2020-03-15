<?php

class m200305_182150_create_experiment_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE experiment (
                id integer NOT NULL,
                experiment_type character varying(100),
                experiment_name character varying(100),
                exp_description character varying(1000),
                dataset_id integer,
                "protocols.io" character varying(200));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE experiment_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE experiment_id_seq 
                OWNED BY experiment.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY experiment 
                ALTER COLUMN id SET DEFAULT nextval(\'experiment_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY experiment
                ADD CONSTRAINT experiment_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY experiment
                ADD CONSTRAINT experiment_dataset_id_fkey FOREIGN KEY (dataset_id) 
                REFERENCES dataset(id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();
    }

    public function safeDown()
    {
        $this->dropTable('experiment');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE experiment_id_seq;')->execute();
    }
}
