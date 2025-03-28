<?php

declare(strict_types=1);

class m250327_024738_add_constraints extends CDbMigration
{
    public function safeUp()
    {
        $this->execute(
            '
            ALTER TABLE external_link_type
            ADD CONSTRAINT fk_external_link_type
            FOREIGN KEY (relationship_id)
            REFERENCES relationship(id)
            ON DELETE RESTRICT ON UPDATE CASCADE
        '
        );
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE external_link_type DROP CONSTRAINT fk_external_link_type');
    }
}
