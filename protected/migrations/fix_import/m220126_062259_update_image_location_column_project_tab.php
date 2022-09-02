<?php

class m220126_062259_UPDATE_image_location_column_project_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("UPDATE project
            SET image_location = REPLACE(image_location,'http://gigadb.org/images/project', '');");

        $this->execute("UPDATE project
            SET image_location = REPLACE(image_location,'http://hpc-bioinformatics.cineca.it/fusion/imgs', '');");

        $this->execute("UPDATE project
            SET image_location = REPLACE(image_location,'https://bioinfotraining.bio.cam.ac.uk/images', '');");

        $this->execute("UPDATE project
            SET image_location = REPLACE(image_location,'http://gigadb.org/images/data/cropped', '');");

        $this->execute("ALTER TABLE project
            ALTER COLUMN image_location TYPE CHARACTER VARYING(255);");

        $this->execute("UPDATE project
            SET image_location = CONCAT('https://assets.gigadb-cdn.net/live/images/projects/', lower(REPLACE(REPLACE(REPLACE(name, ' ','_'),'_-_','_'),',','')), image_location)
            WHERE image_location != '' AND image_location NOT LIKE 'https://assets.gigadb-cdn.net/images/projects/%' AND name NOT LIKE 'LiGeA%' AND name != 'https://www.carmen.org.uk';");

        $this->execute("UPDATE project
            SET image_location = CONCAT('https://assets.gigadb-cdn.net/live/images/projects/', REPLACE(name, 'LiGeA: a comprehensive database of human gene fusion events', 'ligea'), image_location)
            WHERE name like 'LiGeA%' AND image_location NOT LIKE 'https://assets.gigadb-cdn.net/images/projects/%';");

        $this->execute("UPDATE project
            SET image_location = CONCAT('https://assets.gigadb-cdn.net/live/images/projects/', REPLACE(name, 'https://www.carmen.org.uk', 'carmen'), image_location)
            WHERE name = 'https://www.carmen.org.uk' AND image_location NOT LIKE 'https://assets.gigadb-cdn.net/images/projects/%';");
    }

    public function safeDown()
    {
        // This function is empty because reverting safeUp() will eventually
        // result in project images not displayed when CNGB server is shutdown.
    }
}