<?php

class m200305_203702_create_show_manuscript_view extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createview = sprintf(
            'CREATE VIEW show_manuscript AS
                SELECT (\'DOI: \'::text || (dataset.identifier)::text) AS doi_number, 
                manuscript.identifier AS related_manuscript 
                FROM (dataset JOIN manuscript ON ((dataset.id = manuscript.dataset_id)));'
        );

        Yii::app()->db->createCommand($sql_createview)->execute();
    }

    public function safeDown()
    {
        Yii::app()->db->createCommand('DROP VIEW show_manuscript;')->execute();
    }
}
