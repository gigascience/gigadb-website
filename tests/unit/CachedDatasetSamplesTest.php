<?php

/**
 * Unit tests for CachedDatasetSamples to retrieve from cache,the samples associated to a dataset
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetSamplesTest extends CTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testCachedReturnsDatasetId()
    {
        $dataset_id = 6;
        // create a mock for the StoredDatasetConnection
        $storedDatasetSamples = $this->getMockBuilder(StoredDatasetSamples::class)
                         ->setMethods(['getDatasetId'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a Cache Hit, and therefo
        $storedDatasetSamples->expects($this->once())
                 ->method('getDatasetId')
                 ->willReturn(6);

        // create a stub of the cache and cache dependency (because we don't need to verify expectations on the cache)
        $cache =  $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $daoUnderTest = new CachedDatasetSamples($cache, $cacheDependency, $storedDatasetSamples);
        $this->assertEquals($dataset_id, $daoUnderTest->getDatasetId()) ;
    }

    public function testCachedReturnsDatasetDOI()
    {
        $dataset_id = 6;
        $doi = "100044";
        // create a mock for the StoredDatasetConnection
        $storedDatasetSamples = $this->getMockBuilder(StoredDatasetSamples::class)
                         ->setMethods(['getDatasetDOI'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a Cache Hit, and therefo
        $storedDatasetSamples->expects($this->once())
                 ->method('getDatasetDOI')
                 ->willReturn("100044");
        // create a stub of the cache and cache dependency (because we don't need to verify expectations on the cache)
        $cache =  $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);


        $daoUnderTest = new CachedDatasetSamples($cache, $cacheDependency, $storedDatasetSamples);
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI()) ;
    }

    public function testCachedReturnsDatasetSamplesCacheHit()
    {
        $dataset_id = 1 ;

        $expected = array(
            array(
                'sample_id' => 1,
                'linkName' => 'Sample 1',
                'dataset_id' => 1,
                'species_id' => 1,
                'tax_id' => 9238,
                'common_name' => 'Adelie penguin',
                'scientific_name' => 'Pygoscelis adeliae',
                'genbank_name' => 'Adelie penguin',
                'name' => "Sample 1",
                'consent_document' => "",
                'submitted_id' => null,
                'submission_date' => null,
                'contact_author_name' => null,
                'contact_author_email' => null,
                'sampling_protocol' => null,
                'sample_attributes' => array(
                    array("keyword" => "some value"),
                    array("number of lines" => "155"),
                ),
            ),
            array(
                'sample_id' => 2,
                'linkName' => 'Sample 2',
                'dataset_id' => 1,
                'species_id' => 2,
                'tax_id' => 4555,
                'common_name' => 'Foxtail millet',
                'scientific_name' => 'Setaria italica',
                'genbank_name' => 'Foxtail millet',
                'name' => "Sample 2",
                'consent_document' => "",
                'submitted_id' => null,
                'submission_date' => null,
                'contact_author_name' => null,
                'contact_author_email' => null,
                'sampling_protocol' => null,
                'sample_attributes' => [],
            ),
            array(
                'sample_id' => 3,
                'linkName' => 'Sample 3',
                'dataset_id' => 1,
                'species_id' => 1,
                'tax_id' => 9238,
                'common_name' => 'Adelie penguin',
                'scientific_name' => 'Pygoscelis adeliae',
                'genbank_name' => 'Adelie penguin',
                'name' => "Sample 3",
                'consent_document' => "",
                'submitted_id' => null,
                'submission_date' => null,
                'contact_author_name' => null,
                'contact_author_email' => null,
                'sampling_protocol' => null,
                'sample_attributes' => [],
            ),
        );


        // create a stub for the StoredDatasetConnection, cause we have no expectation on it as cache hit
        $storedDatasetSamples = $this->createMock(StoredDatasetSamples::class);
        $storedDatasetSamples->method('getDatasetID')
                                    ->willReturn(1);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        // create a mock for the cache and we need to make the cache method for getting the key
        $cache = $this->getMockBuilder(CApcCache::class)
                        ->setMethods(['get'])
                        ->getMock();

        //then we set our expectation for a Cache Hit
        $cache->expects($this->exactly(1))
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetSamples_getDatasetSamples"))
                 ->willReturn($expected);

        $daoUnderTest = new CachedDatasetSamples($cache, $cacheDependency, $storedDatasetSamples) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetSamples());
    }

    public function testCachedReturnsDatasetSamplesCacheMiss()
    {
        $dataset_id = 1 ;

        $expected = array(
            array(
                'sample_id' => 1,
                'linkName' => 'Sample 1',
                'dataset_id' => 1,
                'species_id' => 1,
                'tax_id' => 9238,
                'common_name' => 'Adelie penguin',
                'scientific_name' => 'Pygoscelis adeliae',
                'genbank_name' => 'Adelie penguin',
                'name' => "Sample 1",
                'consent_document' => "",
                'submitted_id' => null,
                'submission_date' => null,
                'contact_author_name' => null,
                'contact_author_email' => null,
                'sampling_protocol' => null,
                'sample_attributes' => array(
                    array("keyword" => "some value"),
                    array("number of lines" => "155"),
                ),
            ),
            array(
                'sample_id' => 2,
                'linkName' => 'Sample 2',
                'dataset_id' => 1,
                'species_id' => 2,
                'tax_id' => 4555,
                'common_name' => 'Foxtail millet',
                'scientific_name' => 'Setaria italica',
                'genbank_name' => 'Foxtail millet',
                'name' => "Sample 2",
                'consent_document' => "",
                'submitted_id' => null,
                'submission_date' => null,
                'contact_author_name' => null,
                'contact_author_email' => null,
                'sampling_protocol' => null,
                'sample_attributes' => [],
            ),
            array(
                'sample_id' => 3,
                'linkName' => 'Sample 3',
                'dataset_id' => 1,
                'species_id' => 1,
                'tax_id' => 9238,
                'common_name' => 'Adelie penguin',
                'scientific_name' => 'Pygoscelis adeliae',
                'genbank_name' => 'Adelie penguin',
                'name' => "Sample 3",
                'consent_document' => "",
                'submitted_id' => null,
                'submission_date' => null,
                'contact_author_name' => null,
                'contact_author_email' => null,
                'sampling_protocol' => null,
                'sample_attributes' => [],
            ),
        );

        // create a mock for the StoredDatasetSamples, as we expect it to be called for data retrieval
        $storedDatasetSamples = $this->getMockBuilder(StoredDatasetSamples::class)
                                            ->setMethods(['getDatasetID', 'getDatasetSamples'])
                                            ->disableOriginalConstructor()
                                            ->getMock();
        $storedDatasetSamples->expects($this->exactly(2))
                                    ->method('getDatasetID')
                                    ->willReturn(1);
        $storedDatasetSamples->expects($this->exactly(1))
                                    ->method('getDatasetSamples')
                                    ->willReturn($expected);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cache =  $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);

        // create a mock for the cache and we need to make the cache method for getting the key
        $cache = $this->getMockBuilder(CApcCache::class)
                        ->setMethods(['get', 'set'])
                        ->getMock();

        //then we set our expectations for a Cache Miss
        $cache->expects($this->exactly(1))
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetSamples_getDatasetSamples"))
                 ->willReturn(false);

        $cache->expects($this->exactly(1))
                ->method('set')
                ->with(
                    $this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetSamples_getDatasetSamples"),
                    $expected,
                    Cacheable::defaultTTL * 30,
                    $cacheDependency
                )
                ->willReturn(true);


        $daoUnderTest = new CachedDatasetSamples($cache, $cacheDependency, $storedDatasetSamples) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetSamples());
    }
}
