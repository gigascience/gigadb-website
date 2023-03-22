<?php

namespace tests\unit\models;

use app\fixtures\DatasetFixture;
use app\fixtures\AuthorFixture;
use app\fixtures\DatasetAuthorFixture;
use GigaDB\models\Dataset;

class DatasetTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    public $tester;
    
    /**
     * We need to assume existence of dataset in database hence have fixture for it
     */
    public function _fixtures()
    {
        return [
            'datasets' => [
                'class' => DatasetFixture::className(),
                'dataFile' => codecept_data_dir() . 'dataset.php'
            ],
            'authors' => [
                'class' => AuthorFixture::className(),
                'dataFile' => codecept_data_dir() . 'author.php'
            ],
            'datasetAuthors' => [
                'class' => DatasetAuthorFixture::className(),
                'dataFile' => codecept_data_dir() . 'datasetAuthor.php'
            ],
        ];
    }

    /**
     * Tests the custom getAuthors() function in Dataset model class
     *
     * @return void
     */
    public function testGetAuthors()
    {
        $dataset = $this->tester->grabRecord('GigaDB\models\Dataset', ['identifier' => '100888']);
        codecept_debug($dataset->title);
        $dataset = Dataset::findOne(1);
        $authors = $dataset->authors;  // Returns array of author model objects
        codecept_debug($authors[1]->surname);
        verify($authors[0]->first_name)->equals("Indiana");
        verify($authors[1]->surname)->equals("Belloq");
    }
}
