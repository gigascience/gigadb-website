<?php

class m200305_172001_create_project_table extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createtab = sprintf(
            'CREATE TABLE project (
                id integer NOT NULL,
                url character varying(128) NOT NULL,
                name character varying(255) DEFAULT \'\'::character varying NOT NULL,
                image_location character varying(100));'
        );

        $sql_createseq = sprintf(
            'CREATE SEQUENCE project_id_seq
                START WITH 7
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;'
        );

        $sql_alterseq = sprintf(
            'ALTER SEQUENCE project_id_seq 
                OWNED BY project.id;'
        );

        $sql_altertab1 = sprintf(
            'ALTER TABLE ONLY project 
                ALTER COLUMN id SET DEFAULT nextval(\'project_id_seq\'::regclass);'
        );

        $sql_altertab2 = sprintf(
            'ALTER TABLE ONLY project
                ADD CONSTRAINT project_pkey PRIMARY KEY (id);'
        );

        $sql_cmds = array($sql_createtab, $sql_createseq, $sql_alterseq, $sql_altertab1, $sql_altertab2);
        foreach ($sql_cmds as $sql_cmd)
            Yii::app()->db->createCommand($sql_cmd)->execute();

        // Add data to table. Using insert() method from
        // CDbMigration because the code looks cleaner,
        // logging is provided and will be easier to update
        // if required.
        $this->insert('project', array(
            'id' => '2',
            'url' =>'http://www.genome10k.org/',
            'name' => 'Genome 10K',
            'image_location' => 'http://gigadb.org/images/project/G10Klogo.jpg'
        ));
    }

    public function safeDown()
    {
        $this->dropTable('project');
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP SEQUENCE project_id_seq;')->execute();
    }
}
