<?php

class m200305_203829_create_show_project_view extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createview = sprintf(
            'CREATE VIEW show_project AS
                SELECT (\'DOI: \'::text || (dataset.identifier)::text) AS doi_number, 
                project.name AS project 
                FROM ((dataset JOIN dataset_project ON ((dataset.id = dataset_project.dataset_id))) JOIN project ON ((dataset_project.project_id = project.id)));'
        );

        Yii::app()->db->createCommand($sql_createview)->execute();
    }

    public function safeDown()
    {
        Yii::app()->db->createCommand('DROP VIEW show_project;')->execute();
    }
}
