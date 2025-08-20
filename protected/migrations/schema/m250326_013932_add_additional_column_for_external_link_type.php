<?php

declare(strict_types=1);

class m250326_013932_add_additional_column_for_external_link_type extends CDbMigration
{
    public function safeUp()
    {
        $this->addColumn('external_link_type', 'description', 'VARCHAR(250) DEFAULT NULL');
        $this->addColumn('external_link_type', 'prefix', 'VARCHAR(100) DEFAULT NULL');
        $this->addColumn('external_link_type', 'displayed_as', 'VARCHAR(50)');
        $this->addColumn('external_link_type', 'multiple', 'BOOLEAN DEFAULT FALSE');
        $this->addColumn('external_link_type', 'relationship_id', 'INTEGER NOT NULL');

        $this->execute("ALTER TABLE external_link_type ADD CONSTRAINT check_displayed_as CHECK (displayed_as IN ('tab', 'link'))");
    }

    public function safeDown()
    {
        $this->dropColumn('external_link_type', 'description');
        $this->dropColumn('external_link_type', 'prefix');
        $this->dropColumn('external_link_type', 'displayed_as');
        $this->dropColumn('external_link_type', 'multiple');
        $this->dropColumn('external_link_type', 'relationship_id');
    }
}
