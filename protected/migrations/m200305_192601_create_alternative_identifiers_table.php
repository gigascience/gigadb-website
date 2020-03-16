<?php

class m200305_192601_create_alternative_identifiers_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE alternative_identifiers (
                id integer NOT NULL,
                sample_id integer NOT NULL,
                extdb_id integer NOT NULL,
                extdb_accession character varying(100));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE alternative_identifiers_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE alternative_identifiers_id_seq 
                OWNED BY alternative_identifiers.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY alternative_identifiers 
                ALTER COLUMN id SET DEFAULT nextval(\'alternative_identifiers_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY alternative_identifiers
                ADD CONSTRAINT alternative_identifiers_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY alternative_identifiers
                ADD CONSTRAINT alternative_identifiers_extdb_id_fkey FOREIGN KEY (extdb_id) 
                REFERENCES extdb(id);'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY alternative_identifiers
                ADD CONSTRAINT alternative_identifiers_sample_id_fkey FOREIGN KEY (sample_id) 
                REFERENCES sample(id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE alternative_identifiers_id_seq CASCADE;')->execute();
        $this->dropTable('alternative_identifiers');
    }
}
