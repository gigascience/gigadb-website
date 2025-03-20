<?php

declare(strict_types=1);

class m250319_000909_insert_additional_external_links extends CDbMigration
{
    public function safeUp()
    {
        $values = ['Pre-Print', 'Software Heritage Archive (SWHA)', 'Referenced GitHub repo'];

        foreach ($values as $value) {
            $this->insert('external_link_type', [
                'name' => $value,
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('external_link_type', [
            'name' => [
                'Pre-Print',
                'Software Heritage Archive (SWHA)',
                'Referenced GitHub repo'
            ]
        ]);
    }
}
