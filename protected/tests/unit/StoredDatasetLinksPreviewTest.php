<?php

class StoredDatasetLinksPreviewTest extends CDbTestCase
{
    protected $fixtures=array( //include in fixture to avoid foreign key constraint error
        'external_link_types'=>'ExternalLinkType',
        'datasets'=>'Dataset',
        'external_links'=>'ExternalLink',
    );

    public function testGetDatasetId()
    {
        $dataset_id = 1;

        //create mock webClient
        $webClient = $this->createMock(GuzzleHttp\Client::class);

        $idUnderTest = new StoredDatasetLinksPreview($dataset_id, $this->getFixtureManager()->getDbConnection(), $webClient);
        $this->assertEquals($dataset_id, $idUnderTest->getDatasetId()) ;

    }

    public function testGetDatasetDOI()
    {
        $dataset_id = 1;
        $doi = '100243';

        $webClient = $this->createMock(GuzzleHttp\Client::class);

        $doiUnderTest = new StoredDatasetLinksPreview($dataset_id, $this->getFixtureManager()->getDbConnection(), $webClient);
        $this->assertEquals($doi, $doiUnderTest->getDatasetDOI());
    }

    public function testGetImageUrl()
    {
        $dataset_id =1;
        $expected = array(
            array(
                'url'=>'http://gigadb.org/images/data/cropped/100243.gif',
            ),
        );

        $webClient = $this->createMock(GuzzleHttp\Client::class);

        $imageUrlUnderTest = new StoredDatasetLinksPreview($dataset_id, $this->getFixtureManager()->getDbConnection(), $webClient);
        $this->assertEquals($expected, $imageUrlUnderTest->getImageUrl());
    }

    public function testGetPreviewDataForLinks()
    {
        $dataset_id = 2;
        $expected = array(
            array(
                'short_doi'=>'100249',
                'external_url'=>'http://foo6.com',
                'type' =>'3D Models',
            )
        );

        $webClient = $this->createMock(GuzzleHttp\Client::class);

        $previewDataUnderTest = new StoredDatasetLinksPreview($dataset_id, $this->getFixtureManager()->getDbConnection(), $webClient);
//        file_put_contents('test-external.txt', print_r($previewDataUnderTest->getPreviewDataForLinks()));
        $this->assertEquals($expected, $previewDataUnderTest->getPreviewDataForLinks());


    }
}