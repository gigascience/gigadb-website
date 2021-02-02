<?php

class StoredDatasetLinksPreviewTest extends CDbTestCase
{

    public function testGetDatasetId()
    {
        $dataset_id = 1;

        $idUnderTest = new StoredDatasetLinksPreview($dataset_id, $this->getFixtureManager()->getDbConnection());
        $this->assertEquals($dataset_id, $idUnderTest->getDatasetId()) ;

    }

    public function testGetDatasetDOI()
    {
        $dataset_id = 1;
        $doi = 100243;

        $doiUnderTest = new StoredDatasetLinksPreview($dataset_id, $this->getFixtureManager()->getDbConnection());
        $this->assertEquals($doi, $doiUnderTest->getDatasetDOI());
    }

    public function testGetImageUrl()
    {
        $dataset_id =1;
        $expected = array(
            array(
                'url'=>'http://gigadb.org/images/data/cropped/100043_Trichinella-spiralis.jpg',
            ),
        );

        $imageUrlUnderTest = new StoredDatasetLinksPreview($dataset_id, $this->getFixtureManager()->getDbConnection());
        $this->assertEquals($expected, $imageUrlUnderTest->getImageUrl());
    }
}