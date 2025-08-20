<?php

declare(strict_types=1);


class m250319_024739_insert_additional_external_links extends CDbMigration
{
    public function safeUp()
    {
        $valuesToCreate = [
            'Software Heritage Archive (SWHA)' => ['The Software heritage archive (or similar) PID for the archival version of record of the code included in this dataset.', true, null, 'link', 14],
            'Workflow'                         => ['The workflow(s) associated with the dataset', true, null, 'tab', 14],
            'Cited code repository'           => ['The GitHub repository URL of any software/code hosted in GitHub that is to be included as part of this dataset', true, null, 'link', 14],
            'Resource ID'                      => ['The Research Resource Identifiers (RRID) of the resource(s) that are to be considered part of the dataset', true, 'https://scicrunch.org/resolver/', 'link', 14]
        ];

        $valuesToUpdate = [
            'Additional information' => ['URL links to data resources that are to be considered part of the dataset', true, null, 'link', 14],
            'Genome Browser'         => ['A link to an externally hosted genome browser displaying the data included in this dataset', false, null, 'link', 14],
            'Protocols.io'           => ['the DOI of the individual protocols or the protocol collection showing the methods used in this dataset', true, null, 'link', 14],
            'Jbrowse'                => ['the GigaDB hosted Jbrowse link displaying the genome of the dataset', false, null, 'tab', 12],
            '3D models'              => ['The link to the 3D model(s) hosted in this dataset', true, null, 'tab', 12],
            'Code Ocean'             => ['The Code Ocean DOI of the code utilised in this dataset', true, null, 'tab', 14],
            'UCSC Tumour Map Viewer' => ['A link to the UCSC tumour map viewer', false, null, 'link', 14],
            '3D SketchFab'           => ['URL of the 3D model(s) from this dataset, hosted in SketchFab', true, null, 'tab', 14],
            'Github links'           => ['The GitHub repository URL of any software/code hosted in GitHub that is to be included as part of this dataset', true, null, 'link', 14, 'Authors code repositories']
        ];

        foreach ($valuesToCreate as $key => $value) {
            $this->insert('external_link_type', [
                'name'            => $key,
                'description'     => $value[0],
                'multiple'        => $value[1],
                'prefix'          => $value[2],
                'displayed_as'    => $value[3],
                'relationship_id' => $value[4]
            ]);
        }

        foreach ($valuesToUpdate as $key => $value) {
            if ($value[5]) {
                $oldName = $key;
                $newName = $value[5];
                $this->update(
                    'external_link_type',
                    [
                        'name'            => $newName,
                        'description'     => $value[0],
                        'multiple'        => $value[1],
                        'prefix'          => $value[2],
                        'displayed_as'    => $value[3],
                        'relationship_id' => $value[4]
                    ],
                    'name = :name',
                    [':name' => $oldName]
                );
            } else {
                $this->update(
                    'external_link_type',
                    [
                        'description'  => $value[0],
                        'multiple'     => $value[1],
                        'prefix'       => $value[2],
                        'displayed_as' => $value[3],
                        'relationship_id' => $value[4]
                    ],
                    'name = :name',
                    [':name' => $key]
                );
            }
        }
    }

    public function safeDown()
    {
        $this->delete('external_link_type', [
            'name' => [
                'Pre-Print',
                'Software Heritage Archive (SWHA)',
                'Cited code repository'
            ]
        ]);
    }
}
