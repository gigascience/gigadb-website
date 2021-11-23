<?php

class m200529_033116_create_show_manuscript_view extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE VIEW show_manuscript AS
            SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number, 
            manuscript.identifier AS related_manuscript 
            FROM (dataset JOIN manuscript ON ((dataset.id = manuscript.dataset_id)));");
    }

    public function safeDown()
    {
        $this->execute("DROP VIEW show_manuscript;");
    }
}
