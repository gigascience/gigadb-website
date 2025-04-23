<?php

declare(strict_types=1);

class m241003_011415_update_comments_length_curation_log extends CDbMigration
{

    public function safeUp()
    {
        $this->execute('ALTER TABLE curation_log ALTER COLUMN comments TYPE TEXT');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE curation_log ALTER COLUMN comments TYPE VARCHAR(1000)');
    }
}
