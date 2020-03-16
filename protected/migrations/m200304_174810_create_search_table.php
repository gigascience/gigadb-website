<?php

class m200304_174810_create_search_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab =
            'CREATE TABLE search (
                id integer NOT NULL,
                user_id integer NOT NULL,
                name character varying(128) NOT NULL,
                query text NOT NULL,
                result text);';

        $sql_createseq = sprintf(
            'CREATE SEQUENCE search_id_seq 
                START WITH 1 
                INCREMENT BY 1 
                NO MINVALUE 
                NO MAXVALUE 
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE search_id_seq 
                OWNED BY search.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY search 
                ALTER COLUMN id SET DEFAULT nextval(\'search_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY search
                ADD CONSTRAINT search_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY search
                ADD CONSTRAINT search_user_id_fkey FOREIGN KEY (user_id) 
                REFERENCES gigadb_user(id) ON DELETE RESTRICT;'
        );

        $sql_cmds = array( $sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // The search table is empty in production db
        // so no row data needs to be added
    }

    public function safeDown()
    {
        Yii::app()->db->createCommand('DROP SEQUENCE search_id_seq CASCADE;')->execute();
        $this->dropTable('search');
    }
}
