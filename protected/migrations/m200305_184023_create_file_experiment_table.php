<?php

class m200305_184023_create_file_experiment_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE file_experiment (
                id integer NOT NULL,
                file_id integer,
                experiment_id integer);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE file_experiment_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE file_experiment_id_seq 
                OWNED BY file_experiment.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY file_experiment 
                ALTER COLUMN id SET DEFAULT nextval(\'file_experiment_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY file_experiment
                ADD CONSTRAINT file_experiment_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY file_experiment
                ADD CONSTRAINT file_experiment_experiment_id_fkey FOREIGN KEY (experiment_id) 
                REFERENCES experiment(id);'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY file_experiment
                ADD CONSTRAINT file_experiment_file_id_fkey FOREIGN KEY (file_id) 
                REFERENCES file(id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE file_experiment_id_seq CASCADE;')->execute();
        $this->dropTable('file_experiment');
    }
}
