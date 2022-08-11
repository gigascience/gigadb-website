<?php

class m220218_062834_update_url_image_tab extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("UPDATE image
            SET url = REPLACE(url, 'http://gigadb.org/','https://assets.gigadb-cdn.net/live/images/datasets/')
            WHERE url LIKE 'http://gigadb.org/%';");

        $this->execute("UPDATE image
            SET url = 'https://assets.gigadb-cdn.net/live/images/datasets/no_image.png'
            WHERE url='' AND location LIKE 'no_image%';");
    }

    public function safeDown()
    {
        // This function is empty because reverting safeUp() will eventually
        // result in dataset images not displayed when CNGB server is shutdown.
    }
}