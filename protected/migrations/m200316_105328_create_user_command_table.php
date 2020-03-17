<?php

class m200316_105328_create_user_command_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE user_command (
                id integer NOT NULL,
                action_label character varying(32) NOT NULL,
                requester_id integer NOT NULL,
                actioner_id integer,
                actionable_id integer NOT NULL,
                request_date timestamp without time zone,
                action_date timestamp without time zone,
                status character varying(32) NOT NULL);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE user_command_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE user_command_id_seq OWNED BY user_command.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY user_command 
                ALTER COLUMN id SET DEFAULT nextval(\'user_command_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY user_command
                ADD CONSTRAINT user_command_pkey PRIMARY KEY (id);'
        );

        $sql_pg_catalog = sprintf(
            'SELECT pg_catalog.setval(\'user_command_id_seq\', 1, false);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_pg_catalog);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE user_command_id_seq CASCADE;')->execute();
        $this->dropTable('user_command');
    }
}