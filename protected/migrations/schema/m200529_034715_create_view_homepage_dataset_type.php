<?php

class m200529_034715_create_view_homepage_dataset_type extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE VIEW homepage_dataset_type AS
            SELECT type.name, count(dataset_type.id) AS count 
            FROM dataset_type, type, dataset 
            WHERE (((dataset_type.type_id = type.id) AND (dataset_type.dataset_id = dataset.id)) AND ((dataset.upload_status)::text = 'Published'::text)) 
            GROUP BY type.name;");
    }

    public function safeDown()
    {
        $this->execute("DROP VIEW homepage_dataset_type;");
    }
}
