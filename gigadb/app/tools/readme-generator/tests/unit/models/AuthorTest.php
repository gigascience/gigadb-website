<?php

/**
 *
 * generated with:
 * docker-compose run --rm tool ./vendor/bin/codecept generate:test unit "models\AuthorTest"
 *
 * run with:
 * docker-compose run --rm tool ./vendor/codeception/codeception/codecept run unit tests/unit/models/AuthorTest.php
 *
 */

namespace tests\unit\models;
use GigaDB\models\Author;

class AuthorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testGetAuthorsByDatasetId()
    {

        $expectedAuthors = [
            [
                'id' => 147,
                'surname' => 'Oleksyk',
                'first_name' => 'Taras',
                'middle_name' => 'K',
                'custom_name' => '',
            ],
            [
                'id' => 148,
                'surname' => 'Guiblet',
                'first_name' => 'Wilfried',
                'middle_name' => '',
                'custom_name' => '',
            ],
            [
                'id' => 149,
                'surname' => 'Pombert',
                'first_name' => 'Jean',
                'middle_name' => 'Francois',
                'custom_name' => '',
            ],
            [
                'id' => 150,
                'surname' => 'Valentin',
                'first_name' => 'Ricardo',
                'middle_name' => '',
                'custom_name' => '',
            ],
            [
                'id' => 151,
                'surname' => 'Martinez-Cruzado',
                'first_name' => 'Juan',
                'middle_name' => 'Carlos',
                'custom_name' => '',
            ],
        ];

        $datasetId = 5;
        $authorList = Author::listByDatasetId($datasetId);

        $this->assertNotEmpty($authorList, 'The author list is empty');
        $this->assertEquals($expectedAuthors, $authorList, 'The author list is incorrect');

    }
}