<?php

declare(strict_types=1);

class m250218_235441_update_data_in_relationship_table extends CDbMigration
{
    public function safeUp()
    {
        $this->execute(
            "
                INSERT INTO relationship (name) VALUES
                ('Describes'),
                ('IsDescribedBy'),
                ('HasVersion'),
                ('IsVersionOf'),
                ('IsPublishedIn'),
                ('IsReviewedBy'),
                ('Reviews'),
                ('IsDerivedFrom'),
                ('IsSourceOf'),
                ('IsRequiredBy'),
                ('Requires'),
                ('Obsoletes'),
                ('IsObsoletedBy'),
                ('IsCollectedBy'),
                ('Collects')
            "
        );
    }

    public function safeDown()
    {
        $this->delete('relationship', ['name' => 'Describes']);
        $this->delete('relationship', ['name' => 'IsDescribedBy']);
        $this->delete('relationship', ['name' => 'HasVersion']);
        $this->delete('relationship', ['name' => 'IsVersionOf']);
        $this->delete('relationship', ['name' => 'IsPublishedIn']);
        $this->delete('relationship', ['name' => 'IsReviewedBy']);
        $this->delete('relationship', ['name' => 'Reviews']);
        $this->delete('relationship', ['name' => 'IsDerivedFrom']);
        $this->delete('relationship', ['name' => 'IsSourceOf']);
        $this->delete('relationship', ['name' => 'IsRequiredBy']);
        $this->delete('relationship', ['name' => 'Requires']);
        $this->delete('relationship', ['name' => 'Obsoletes']);
        $this->delete('relationship', ['name' => 'IsObsoletedBy']);
        $this->delete('relationship', ['name' => 'IsCollectedBy']);
        $this->delete('relationship', ['name' => 'Collects']);
    }
}
