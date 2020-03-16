<?php

class m200305_164009_create_curation_log_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE curation_log (
                id integer NOT NULL,
                dataset_id integer NOT NULL,
                creation_date date,
                created_by character varying(100),
                last_modified_date date,
                last_modified_by character varying(100),
                action character varying(100),
                comments character varying(1000));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE curation_log_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE curation_log_id_seq 
                OWNED BY curation_log.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY curation_log 
                ALTER COLUMN id SET DEFAULT nextval(\'curation_log_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY curation_log
                ADD CONSTRAINT curation_log_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY curation_log
                ADD CONSTRAINT curation_log_dataset_id_fkey FOREIGN KEY (dataset_id) 
                REFERENCES dataset(id) ON DELETE CASCADE;'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE curation_log_id_seq CASCADE;')->execute();
        $this->dropTable('curation_log');
    }
}
