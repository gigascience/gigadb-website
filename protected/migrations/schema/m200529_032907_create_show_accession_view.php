<?php

class m200529_032907_create_show_accession_view extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE VIEW show_accession AS
            SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number, 
            link.link AS related_accessions 
            FROM (dataset JOIN link ON ((dataset.id = link.dataset_id)));");
    }

    public function safeDown()
    {
        Yii::app()->db->createCommand('DROP VIEW show_manuscript;')->execute();
    }
}
