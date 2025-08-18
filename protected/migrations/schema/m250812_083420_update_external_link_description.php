<?php

declare(strict_types=1);

class m250812_083420_update_external_link_description extends CDbMigration
{
    public function safeUp()
    {
        $this->addColumn('external_link', 'description', 'VARCHAR(200)');
    }

    public function safeDown()
    {
        $this->dropColumn('external_link', 'description');
    }
}
