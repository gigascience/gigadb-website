<?php

class m200305_173717_create_dataset_session_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE dataset_session (
                id integer NOT NULL,
                identifier text NOT NULL,
                dataset text,
                dataset_id text,
                datasettypes text,
                images text,
                authors text,
                projects text,
                links text,
                "externalLinks" text,
                relations text,
                samples text);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE dataset_session_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE dataset_session_id_seq 
                OWNED BY dataset_session.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY dataset_session 
                ALTER COLUMN id SET DEFAULT nextval(\'dataset_session_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY dataset_session
                ADD CONSTRAINT dataset_session_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE dataset_session_id_seq CASCADE;')->execute();
        $this->dropTable('dataset_session');
    }
}
