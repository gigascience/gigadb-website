<?php

class m200305_182358_create_exp_attributes_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE exp_attributes (
                id integer NOT NULL,
                exp_id integer,
                attribute_id integer,
                value character varying(1000),
                units_id character varying(50));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE exp_attributes_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE exp_attributes_id_seq 
                OWNED BY exp_attributes.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY exp_attributes 
                ALTER COLUMN id SET DEFAULT nextval(\'exp_attributes_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY exp_attributes
                ADD CONSTRAINT exp_attributes_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY exp_attributes
                ADD CONSTRAINT exp_attributes_attribute_id_fkey FOREIGN KEY (attribute_id) 
                REFERENCES attribute(id);'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY exp_attributes
                ADD CONSTRAINT exp_attributes_exp_id_fkey FOREIGN KEY (exp_id) 
                REFERENCES experiment(id);'
        );

        $sql_altertab5 = sprintf(
            'ALTER TABLE ONLY exp_attributes
                ADD CONSTRAINT exp_attributes_units_id_fkey FOREIGN KEY (units_id) 
                REFERENCES unit(id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4, $sql_altertab5);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();
    }

    public function safeDown()
    {
        $this->dropTable('exp_attributes');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE exp_attributes_id_seq;')->execute();
    }
}
