<?php

declare(strict_types=1);

class m250324_013644_add_is_pre_print_column_in_manuscript extends CDbMigration
{
    public function safeUp()
    {
        $this->addColumn('manuscript', 'is_pre_print', 'BOOLEAN NOT NULL DEFAULT FALSE');
    }

    public function safeDown()
    {
        $this->dropColumn('manuscript', 'is_pre_print');
    }
}
