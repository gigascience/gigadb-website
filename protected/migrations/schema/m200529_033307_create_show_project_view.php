<?php

class m200529_033307_create_show_project_view extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE VIEW show_project AS
            SELECT ('DOI: '::text || (dataset.identifier)::text) AS doi_number, 
            project.name AS project 
            FROM ((dataset JOIN dataset_project ON ((dataset.id = dataset_project.dataset_id))) JOIN project ON ((dataset_project.project_id = project.id)));");
    }

    public function safeDown()
    {
        $this->execute("DROP VIEW show_project;");
    }
}
