<?php

class m200305_172026_create_dataset_project_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE dataset_project (
                id integer NOT NULL,
                dataset_id integer,
                project_id integer);'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE dataset_project_id_seq
                START WITH 7
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE dataset_project_id_seq 
                OWNED BY dataset_project.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY dataset_project
                ALTER COLUMN id SET DEFAULT nextval(\'dataset_project_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY dataset_project
                ADD CONSTRAINT dataset_project_pkey PRIMARY KEY (id);'
        );

        $sql_altertab3 = sprintf(
            'ALTER TABLE ONLY dataset_project
                ADD CONSTRAINT dataset_project_dataset_id_fkey FOREIGN KEY (dataset_id) 
                REFERENCES dataset(id) ON DELETE CASCADE;'
        );

        $sql_altertab4 = sprintf(
            'ALTER TABLE ONLY dataset_project
                ADD CONSTRAINT dataset_project_project_id_fkey FOREIGN KEY (project_id) 
                REFERENCES project(id) ON DELETE CASCADE;'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2, $sql_altertab3, $sql_altertab4);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('dataset_project', array(
            'id' => '12',
            'dataset_id' =>'29',
            'project_id' => '2'
        ));
        $this->insert('dataset_project', array(
            'id' => '6',
            'dataset_id' =>'13',
            'project_id' => '2'
        ));
        $this->insert('dataset_project', array(
            'id' => '9',
            'dataset_id' =>'25',
            'project_id' => '2'
        ));
    }

    public function safeDown()
    {
        $this->dropTable('dataset_project');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE dataset_project_id_seq;')->execute();
    }
}
