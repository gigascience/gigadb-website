<?php

class m200305_193749_create_show_accession_view extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // Using plain SQL for schema changes since Yii column
        // types e.g. string will be converted to only varchar(255)
        // and cannot specify smaller varchar sizes
        $sql_createview = sprintf(
            'CREATE VIEW show_accession AS
                SELECT (\'DOI: \'::text || (dataset.identifier)::text) AS doi_number, 
                link.link AS related_accessions 
                FROM (dataset JOIN link ON ((dataset.id = link.dataset_id)));'
        );

        Yii::app()->db->createCommand($sql_createview)->execute();
    }

    public function safeDown()
    {
        Yii::app()->db->createCommand('DROP VIEW show_accession;')->execute();
    }
}
