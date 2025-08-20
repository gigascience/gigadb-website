<?php

declare(strict_types=1);

class m250414_084036_add_column_external_type_parent extends CDbMigration
{
    public function safeUp()
    {
        $this->addColumn('external_link', 'related_id', 'INTEGER NULL');
        $this->addColumn('external_link', 'is_referred', 'BOOL DEFAULT FALSE');

        $this->addColumn('external_link_type', 'can_self_referred', 'BOOL DEFAULT FALSE');

        $this->addForeignKey('fk_external_link_related', 'external_link', 'related_id', 'external_link', 'id', 'SET NULL');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_external_link_related', 'external_link');
        $this->dropColumn('external_link', 'related_id');
        $this->dropColumn('external_link', 'is_referred');
        $this->dropColumn('external_link_type', 'can_self_referred');
    }
}
