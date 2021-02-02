<?php

class StoredDatasetLinksPreviewTest extends CDbTestCase
{
    protected $fixtures=array(
        'datasets'=>'Dataset',
    );

    public function setUp()
    {
        parent::setUp();
    }

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
}