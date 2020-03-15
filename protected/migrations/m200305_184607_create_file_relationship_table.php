<?php

class m200305_184607_create_file_relationship_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE file_relationship (
                id integer NOT NULL,
                file_id integer NOT NULL,
                related_file_id integer NOT NULL,
                relationship_id integer);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE file_relationship_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE file_relationship_id_seq 
                OWNED BY file_relationship.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY file_relationship 
                ALTER COLUMN id SET DEFAULT nextval(\'file_relationship_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY file_relationship
                ADD CONSTRAINT file_relationship_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY file_relationship
                ADD CONSTRAINT file_relationship_file_id_fkey FOREIGN KEY (file_id) 
                REFERENCES file(id);'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY file_relationship
                ADD CONSTRAINT file_relationship_relationship_id_fkey FOREIGN KEY (relationship_id) 
                REFERENCES relationship(id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();
    }

    public function safeDown()
    {
        $this->dropTable('file_relationship');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE file_relationship_id_seq;')->execute();
    }
}
