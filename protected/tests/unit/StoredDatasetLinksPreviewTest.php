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

        $head ='<meta property="og:title" content="Exercise generates immune cells in bone"/>
                <meta property="og:description" content="Mechanosensing stem-cell niche promotes lymphocyte production."/>
                <meta property="og:image" content="https://media.nature.com/lw1024/magazine-assets/d41586-021-00419-y/d41586-021-00419-y_18880568.png"/>
                <meta name="twitter:title" content="Exercise generates immune cells in bone"/>
                <meta name="twitter:description" content="Mechanosensing stem-cell niche promotes lymphocyte production."/>
                <meta name="twitter:image" content="https://media.nature.com/lw1024/magazine-assets/d41586-021-00419-y/d41586-021-00419-y_18880568.png"/>
                ';


        //create a mock object and queue 1 response
        $mock = new GuzzleHttp\Handler\MockHandler([
            new GuzzleHttp\Psr7\Response(200, ['Foo' => 'Bar'], $head),
            new GuzzleHttp\Exception\RequestException('Fail to make a request', new GuzzleHttp\Psr7\Request('GET', 'https://www.nature.com/articles/d41586-021-00419-y'))
        ]);

        $handler = GuzzleHttp\HandlerStack::create($mock);
        $webClient = new GuzzleHttp\Client(['handler' => $handler]);

        $previewDataUnderTest = new StoredDatasetLinksPreview($dataset_id, $this->getFixtureManager()->getDbConnection(), $webClient);
        $this->assertEquals($expected, $previewDataUnderTest->getPreviewDataForLinks());

    }
}