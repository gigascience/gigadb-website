<?php

class m200305_193042_create_sample_experiment_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE sample_experiment (
                id integer NOT NULL,
                sample_id integer,
                experiment_id integer);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE sample_experiment_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE sample_experiment_id_seq 
                OWNED BY sample_experiment.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY sample_experiment 
                ALTER COLUMN id SET DEFAULT nextval(\'sample_experiment_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY sample_experiment
                ADD CONSTRAINT sample_experiment_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY sample_experiment
                ADD CONSTRAINT sample_experiment_experiment_id_fkey FOREIGN KEY (experiment_id) 
                REFERENCES experiment(id);'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY sample_experiment
                ADD CONSTRAINT sample_experiment_sample_id_fkey FOREIGN KEY (sample_id) 
                REFERENCES sample(id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();
    }

    public function safeDown()
    {
        $this->dropTable('sample_experiment');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE sample_experiment_id_seq;')->execute();
    }
}
