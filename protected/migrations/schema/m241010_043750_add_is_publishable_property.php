<?php

declare(strict_types=1);

class m241010_043750_add_is_publishable_property extends CDbMigration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE dataset ADD COLUMN is_publishable BOOLEAN DEFAULT FALSE');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE dataset DROP COLUMN is_publishable');
    }
}
