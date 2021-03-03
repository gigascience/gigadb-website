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


    public function testGetPreviewDataForLinks()
    {
        $dataset_id = 2;
        $expected = array(
            array(
                'short_doi'=>'100249',
                'external_url'=>'http://foo6.com',
                'type'=>'3D Models',
                'external_title'=>'Exercise generates immune cells in bone',
                'external_description'=>'Mechanosensing stem-cell niche promotes lymphocyte production.',
                'external_imageUrl'=>'https://media.nature.com/lw1024/magazine-assets/d41586-021-00419-y/d41586-021-00419-y_18880568.png',
            )
        );

//        $response = $this->getMockBuilder(StoredDatasetLinksPreview::class)
//            ->setMethods(['get_meta_tags'])
//            ->disableOriginalConstructor()
//            ->getMock();
//
//        $response->expects($this->once())
//            ->method('get_meta_tags')
//            ->willReturn(
//                array(
//                    'twitter:title'=>'Exercise generates immune cells in bone',
//                    'twitter:description'=>'Mechanosensing stem-cell niche promotes lymphocyte production.',
//                    'twitter:image'=>'https://media.nature.com/lw1024/magazine-assets/d41586-021-00419-y/d41586-021-00419-y_18880568.png',
//                )
//            );

        $webClient = $this->createMock(GuzzleHttp\Client::class);

//        $webClient->expects($this->once())
//            ->willReturn($response);



        $previewDataUnderTest = new StoredDatasetLinksPreview($dataset_id, $this->getFixtureManager()->getDbConnection(), $webClient);
        file_put_contents('test-guzzle-return.txt', print_r($previewDataUnderTest->getPreviewDataForLinks(), true));
        $this->assertEquals($expected, $previewDataUnderTest->getPreviewDataForLinks());


    }
}