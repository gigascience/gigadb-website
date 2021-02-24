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
        $doi = '100243';

        $doiUnderTest = new StoredDatasetLinksPreview($dataset_id, $this->getFixtureManager()->getDbConnection());
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

        $imageUrlUnderTest = new StoredDatasetLinksPreview($dataset_id, $this->getFixtureManager()->getDbConnection());
        $this->assertEquals($expected, $imageUrlUnderTest->getImageUrl());
    }

    public function testGetPreviewDataForLinks()
    {
        $dataset_id = 1;
        $expected = array(
            array(
                'short_doi'=>'100243',
                'url'=>'https://doi.org/10.5524/100243',
                'title'=>'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
                'description'=>'The emergence of Zika virus (ZIKV) as a public health emergency in Latin America and the Caribbean (LAC) occurred during a period of severe drought and unusually high temperatures. Speculation in the literature exists that these climate conditions were associated with the 2015/2016 El Niño event and/or climate change but to date no quantitative ',
                'image_url'=>'http://gigadb.org/images/data/cropped/100243.gif',
            )
        );

        $previewDataUnderTest = new StoredDatasetLinksPreview($dataset_id, $this->getFixtureManager()->getDbConnection());
        $this->assertEquals($expected, $previewDataUnderTest->getPreviewDataForLinks());


    }
}