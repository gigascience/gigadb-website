<?php

class m200305_191631_create_relation_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE relation (
                id integer NOT NULL,
                dataset_id integer NOT NULL,
                related_doi character varying(15) NOT NULL,
                relationship_id integer
            );'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE relation_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE relation_id_seq 
                OWNED BY relation.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY relation 
                ALTER COLUMN id SET DEFAULT nextval(\'relation_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY relation
                ADD CONSTRAINT relation_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY relation
                ADD CONSTRAINT relation_dataset_id_fkey FOREIGN KEY (dataset_id) 
                REFERENCES dataset(id) ON DELETE CASCADE;'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY relation
                ADD CONSTRAINT relation_relationship_fkey FOREIGN KEY (relationship_id) 
                REFERENCES relationship(id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();
    }

    public function safeDown()
    {
        $this->dropTable('relation');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE relation_id_seq;')->execute();
    }
}
