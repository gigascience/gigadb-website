<?php

class StoredDatasetLinksPreviewTest extends CDbTestCase
{

    public function testGetDatasetId()
    {
        $dataset_id = 1;

        $webClient = $this->createMock(GuzzleHttp\Client::class);
        $idUnderTest = new StoredDatasetLinksPreview($dataset_id, $this->getFixtureManager()->getDbConnection(), $webClient);
        $this->assertEquals($dataset_id, $idUnderTest->getDatasetId()) ;

    }

    public function testGetDatasetDOI()
    {
        $dataset_id = 1;
        $doi = 100043;

        $webClient = $this->createMock(GuzzleHttp\Client::class);
        $doiUnderTest = new StoredDatasetLinksPreview($dataset_id, $this->getFixtureManager()->getDbConnection(), $webClient);
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

        $webClient = $this->createMock(GuzzleHttp\Client::class);
        $imageUrlUnderTest = new StoredDatasetLinksPreview($dataset_id, $this->getFixtureManager()->getDbConnection(), $webClient);
        $this->assertEquals($expected, $imageUrlUnderTest->getImageUrl());
    }
}