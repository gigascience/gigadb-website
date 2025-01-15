<?php

/**
 * Unit tests for CachedDatasetFiles to retrieve from cache,the files associated to a dataset
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetFilesTest extends CTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testCachedReturnsDatasetId()
    {
        $dataset_id = 6;
        // create a mock for the StoredDatasetConnection
        $storedDatasetFiles = $this->getMockBuilder(StoredDatasetFiles::class)
                         ->setMethods(['getDatasetId'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a Cache Hit, and therefo
        $storedDatasetFiles->expects($this->once())
                 ->method('getDatasetId')
                 ->willReturn(6);

        // create a stub of the cache and cache dependency (because we don't need to verify expectations on the cache)
        $cache =  $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $daoUnderTest = new CachedDatasetFiles($cache, $cacheDependency, $storedDatasetFiles);
        $this->assertEquals($dataset_id, $daoUnderTest->getDatasetId()) ;
    }

    public function testCachedReturnsDatasetDOI()
    {
        $dataset_id = 6;
        $doi = "100044";
        // create a mock for the StoredDatasetConnection
        $storedDatasetFiles = $this->getMockBuilder(StoredDatasetFiles::class)
                         ->setMethods(['getDatasetDOI'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a Cache Hit, and therefo
        $storedDatasetFiles->expects($this->once())
                 ->method('getDatasetDOI')
                 ->willReturn("100044");
        // create a stub of the cache and cache dependency (because we don't need to verify expectations on the cache)
        $cache =  $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);


        $daoUnderTest = new CachedDatasetFiles($cache, $cacheDependency, $storedDatasetFiles);
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI()) ;
    }

    public function testCachedReturnsDatasetFilesCacheHit()
    {
        $dataset_id = 1 ;

        $expected = array(
            array(
                'id' => 1,
                'dataset_id' => 1,
                'name' => "readme.txt",
                'location' => 'ftp://foo.bar',
                'extension' => 'txt',
                'size' => 1322123045,
                'description' => 'just readme',
                'date_stamp' => '2015-10-12',
                'format' => 'TEXT',
                'type' => 'Text',
                'file_attributes' => array(
                    array("keyword" => "some value"),
                    array("number of lines" => "155"),
                ),
                'download_count' => 0,
            ),
            array(
                'id' => 2,
                'dataset_id' => 1,
                'name' => "readme.txt",
                'location' => 'ftp://foo.bar',
                'extension' => 'txt',
                'size' => -1,
                'description' => 'just readme',
                'date_stamp' => '2015-10-13',
                'format' => 'TEXT',
                'type' => 'Text',
                'file_attributes' => [],
                'download_count' => 0,
            ),
        );

        // create a stub for the StoredDatasetConnection, cause we have no expectation on it as cache hit
        $storedDatasetFiles = $this->createMock(StoredDatasetFiles::class);
        $storedDatasetFiles->method('getDatasetID')
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
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetFiles_getDatasetFiles"))
                 ->willReturn($expected);

        $daoUnderTest = new CachedDatasetFiles($cache, $cacheDependency, $storedDatasetFiles) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetFiles());
    }

    public function testCachedReturnsDatasetFilesCacheMiss()
    {
        $dataset_id = 1 ;

        $expected = array(
            array(
                'id' => 1,
                'dataset_id' => 1,
                'name' => "readme.txt",
                'location' => 'ftp://foo.bar',
                'extension' => 'txt',
                'size' => 1322123045,
                'description' => 'just readme',
                'date_stamp' => '2015-10-12',
                'format' => 'TEXT',
                'type' => 'Text',
                'file_attributes' => array(
                    array("keyword" => "some value"),
                    array("number of lines" => "155"),
                ),
                'download_count' => 0,
            ),
            array(
                'id' => 2,
                'dataset_id' => 1,
                'name' => "readme.txt",
                'location' => 'ftp://foo.bar',
                'extension' => 'txt',
                'size' => -1,
                'description' => 'just readme',
                'date_stamp' => '2015-10-13',
                'format' => 'TEXT',
                'type' => 'Text',
                'file_attributes' => [],
                'download_count' => 0,
            ),
        );

        // create a mock for the StoredDatasetFiles, as we expect it to be called for data retrieval
        $storedDatasetFiles = $this->getMockBuilder(StoredDatasetFiles::class)
                                            ->setMethods(['getDatasetID', 'getDatasetFiles'])
                                            ->disableOriginalConstructor()
                                            ->getMock();
        $storedDatasetFiles->expects($this->exactly(2))
                                    ->method('getDatasetID')
                                    ->willReturn(1);
        $storedDatasetFiles->expects($this->exactly(1))
                                    ->method('getDatasetFiles')
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
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetFiles_getDatasetFiles"))
                 ->willReturn(false);

        $cache->expects($this->exactly(1))
                ->method('set')
                ->with(
                    $this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetFiles_getDatasetFiles"),
                    $expected,
                    Cacheable::defaultTTL * 30,
                    $cacheDependency
                )
                ->willReturn(true);


        $daoUnderTest = new CachedDatasetFiles($cache, $cacheDependency, $storedDatasetFiles) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetFiles());
    }

    public function testStoredReturnsDatasetFilesSamplesCacheHit()
    {
        $dataset_id = 1;

        $expected = array(
                        array(
                            'sample_id' => 1,
                            'sample_name' => "Sample 1",
                            'file_id' => 1,
                        ),
                        array(
                            'sample_id' => 2,
                            'sample_name' => "Sample 2",
                            'file_id' => 1,
                        ),
                        array(
                            'sample_id' => 3,
                            'sample_name' => "Sample 3",
                            'file_id' => 1,
                        ),
                        array(
                            'sample_id' => 4,
                            'sample_name' => "Sample 4",
                            'file_id' => 1,
                        ),
                        array(
                            'sample_id' => 5,
                            'sample_name' => "Sample 5",
                            'file_id' => 2,
                        ),
                        array(
                            'sample_id' => 6,
                            'sample_name' => "Sample 6",
                            'file_id' => 2,
                        ),
                        array(
                            'sample_id' => 7,
                            'sample_name' => "Sample 7",
                            'file_id' => 2,
                        ),
                    );

        // create a stub for the StoredDatasetConnection, cause we have no expectation on it as cache hit
        $storedDatasetFiles = $this->createMock(StoredDatasetFiles::class);
        $storedDatasetFiles->method('getDatasetID')
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
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetFiles_getDatasetFilesSamples"))
                 ->willReturn($expected);

        $daoUnderTest = new CachedDatasetFiles($cache, $cacheDependency, $storedDatasetFiles) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetFilesSamples());
    }

    public function testStoredReturnsDatasetFilesSamplesCacheMiss()
    {
        $dataset_id = 1;

        $expected = array(
                       array(
                           'sample_id' => 1,
                           'sample_name' => "Sample 1",
                           'file_id' => 1,
                       ),
                       array(
                           'sample_id' => 2,
                           'sample_name' => "Sample 2",
                           'file_id' => 1,
                       ),
                       array(
                           'sample_id' => 3,
                           'sample_name' => "Sample 3",
                           'file_id' => 1,
                       ),
                       array(
                           'sample_id' => 4,
                           'sample_name' => "Sample 4",
                           'file_id' => 1,
                       ),
                       array(
                           'sample_id' => 5,
                           'sample_name' => "Sample 5",
                           'file_id' => 2,
                       ),
                       array(
                           'sample_id' => 6,
                           'sample_name' => "Sample 6",
                           'file_id' => 2,
                       ),
                       array(
                           'sample_id' => 7,
                           'sample_name' => "Sample 7",
                           'file_id' => 2,
                       ),
                   );

        // create a mock for the StoredDatasetFiles, as we expect it to be called for data retrieval
        $storedDatasetFiles = $this->getMockBuilder(StoredDatasetFiles::class)
                                           ->setMethods(['getDatasetID', 'getDatasetFilesSamples'])
                                           ->disableOriginalConstructor()
                                           ->getMock();
        $storedDatasetFiles->expects($this->exactly(2))
                                   ->method('getDatasetID')
                                   ->willReturn(1);
        $storedDatasetFiles->expects($this->exactly(1))
                                   ->method('getDatasetFilesSamples')
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
                ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetFiles_getDatasetFilesSamples"))
                ->willReturn(false);

        $cache->expects($this->exactly(1))
               ->method('set')
               ->with(
                   $this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetFiles_getDatasetFilesSamples"),
                   $expected,
                   Cacheable::defaultTTL * 30,
                   $cacheDependency
               )
               ->willReturn(true);


        $daoUnderTest = new CachedDatasetFiles($cache, $cacheDependency, $storedDatasetFiles) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetFilesSamples());
    }
}
