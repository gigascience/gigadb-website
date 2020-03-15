<?php

class m200305_193418_create_sample_rel_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE sample_rel (
                id integer NOT NULL,
                sample_id integer NOT NULL,
                related_sample_id integer NOT NULL,
                relationship_id integer);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE sample_rel_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE sample_rel_id_seq 
                OWNED BY sample_rel.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY sample_rel 
                ALTER COLUMN id SET DEFAULT nextval(\'sample_rel_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY sample_rel
                ADD CONSTRAINT sample_rel_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY sample_rel
                ADD CONSTRAINT sample_rel_relationship_id_fkey FOREIGN KEY (relationship_id) 
                REFERENCES relationship(id);'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY sample_rel
                ADD CONSTRAINT sample_rel_sample_id_fkey FOREIGN KEY (sample_id) 
                REFERENCES sample(id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();
    }

    public function safeDown()
    {
        $this->dropTable('sample_rel');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE sample_rel_id_seq;')->execute();
    }
}
