<?php

class m200305_183100_create_external_link_type_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE external_link_type (
                id integer NOT NULL,
                name character varying(45) NOT NULL);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE external_link_type_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE external_link_type_id_seq 
                OWNED BY external_link_type.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY external_link_type 
                ALTER COLUMN id SET DEFAULT nextval(\'external_link_type_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY external_link_type
                ADD CONSTRAINT external_link_type_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('external_link_type', array(
            'id' => '1',
            'name' =>'Additional information'
        ));
        $this->insert('external_link_type', array(
            'id' => '2',
            'name' =>'Genome browser'
        ));
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE external_link_type_id_seq CASCADE;')->execute();
        $this->dropTable('external_link_type');
    }
}
