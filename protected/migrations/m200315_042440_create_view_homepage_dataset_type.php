<?php

class m200315_042440_create_view_homepage_dataset_type extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createview = sprintf(
            'CREATE VIEW homepage_dataset_type AS
                SELECT type.name, count(dataset_type.id) AS count 
                FROM dataset_type, type, dataset 
                WHERE (((dataset_type.type_id = type.id) AND (dataset_type.dataset_id = dataset.id)) AND ((dataset.upload_status)::text = \'Published\'::text)) 
                GROUP BY type.name;'
        );

        Yii::app()->db->createCommand($sql_createview)->execute();
    }

    public function safeDown()
    {
        // Don't think you can drop SEQUENCE with a
        // function in CDbMigration
        Yii::app()->db->createCommand('DROP VIEW homepage_dataset_type;')->execute();
    }
}