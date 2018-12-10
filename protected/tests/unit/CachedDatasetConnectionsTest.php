<?php
/**
 * Unit tests for CachedDatasetConnections to retrieve from cache connected datasets (through relations and keywords)
 *
 * @see DatasetAccessionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetConnectionsTest extends CDbTestCase
{
    protected $fixtures=array( //careful, the order matters here because of foreign key constraints
        'publishers'=>'Publisher',
        'relationships'=>'Relationship',
        'datasets'=>'Dataset',
        'relations'=>'Relation',

    );

    public function setUp()
    {
        parent::setUp();
    }

    public function testCachedReturnsDatasetId()
    {
        $dataset_id = 6;
        // create a mock for the StoredDatasetConnection
        $storedDatasetConnections = $this->getMockBuilder(StoredDatasetConnections::class)
                         ->setMethods(['getDatasetId'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a Cache Hit, and therefo
        $storedDatasetConnections->expects($this->once())
                 ->method('getDatasetId')
                 ->willReturn(6);

        // create a stub of the cache and cache dependency (because we don't need to verify expectations on the cache)
        $cache =  $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $daoUnderTest = new CachedDatasetConnections($cache, $cacheDependency, $storedDatasetConnections);
        $this->assertEquals($dataset_id, $daoUnderTest->getDatasetId() ) ;
    }

    public function testCachedReturnsDatasetDOI()
    {
        $dataset_id = 6;
        $doi = "100044";
        // create a mock for the StoredDatasetConnection
        $storedDatasetConnections = $this->getMockBuilder(StoredDatasetConnections::class)
                         ->setMethods(['getDatasetDOI'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a Cache Hit, and therefo
        $storedDatasetConnections->expects($this->once())
                 ->method('getDatasetDOI')
                 ->willReturn("100044");
        // create a stub of the cache and cache dependency (because we don't need to verify expectations on the cache)
        $cache =  $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);


        $daoUnderTest = new CachedDatasetConnections($cache, $cacheDependency, $storedDatasetConnections);
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI() ) ;
    }

    public function testCachedReturnsRelationsCacheHit()
    {
        $dataset_id = 6;

        // create a stub for the StoredDatasetConnection, cause we have no expectation on it as cache hit
        $storedDatasetConnections = $this->createMock(StoredDatasetConnections::class);
        $storedDatasetConnections->method('getDatasetID')
                                    ->willReturn(6);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        // create a mock for the cache and we need to make the cache method for getting the key
        $cache = $this->getMockBuilder(CApcCache::class)
                        ->setMethods(['get'])
                        ->getMock();

        //then we set our expectation for a Cache Hit
        $cache->expects($this->exactly(4))
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetConnections_getRelations"))
                 ->willReturn(
                    array(
                        array(
                            'dataset_id'=>6, // 100044
                            'related_id'=>5, // 100038
                            'relationship'=>"Compiles", //18 Compiles
                        ),
                        array(
                            'dataset_id'=>6, // 100044
                            'related_id'=>7, // 100148
                            'relationship'=>"IsPreviousVersionOf", //10 IsPreviousVersionOf
                        )
                    )
                );

        $expected = array(
            array(
                'dataset_id'=>6, // 100044
                'related_id'=>5, // 100038
                'relationship'=>"Compiles", //18 Compiles
            ),
            array(
                'dataset_id'=>6, // 100044
                'related_id'=>7, // 100148
                'relationship'=>"IsPreviousVersionOf", //10 IsPreviousVersionOf
            )
        );

        $daoUnderTest = new CachedDatasetConnections($cache, $cacheDependency, $storedDatasetConnections) ;
        $this->assertEquals($expected, $daoUnderTest->getRelations());
        $this->assertEquals([$expected[1]], $daoUnderTest->getRelations("IsPreviousVersionOf"));
        $this->assertEquals([$expected[0]], $daoUnderTest->getRelations("Compiles"));
        $this->assertEquals([], $daoUnderTest->getRelations("DoesNotExistRelationship"));


    }


    public function testCachedReturnsRelationsCacheMiss()
    {
        $dataset_id = 6;

        // create a mock for the StoredDatasetConnection, as we expect it to be called for data retrieval
        $storedDatasetConnections = $this->getMockBuilder(StoredDatasetConnections::class)
                                            ->setMethods(['getDatasetID', 'getRelations'])
                                            ->disableOriginalConstructor()
                                            ->getMock();
        $storedDatasetConnections->expects($this->exactly(2))
                                    ->method('getDatasetID')
                                    ->willReturn(6);
        $storedDatasetConnections->expects($this->exactly(1))
                                    ->method('getRelations')
                                    ->willReturn(
                                        array(
                                            array(
                                                'dataset_id'=>6, // 100044
                                                'related_id'=>5, // 100038
                                                'relationship'=>"Compiles", //18 Compiles
                                            ),
                                            array(
                                                'dataset_id'=>6, // 100044
                                                'related_id'=>7, // 100148
                                                'relationship'=>"IsPreviousVersionOf", //10 IsPreviousVersionOf
                                            )
                                        )
                                    );

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cache =  $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);

        // create a mock for the cache and we need to make the cache method for getting the key
        $cache = $this->getMockBuilder(CApcCache::class)
                        ->setMethods(['get', 'set'])
                        ->getMock(
                            array(
                                array(
                                    'dataset_id'=>6, // 100044
                                    'related_id'=>5, // 100038
                                    'relationship'=>"Compiles", //18 Compiles
                                ),
                                array(
                                    'dataset_id'=>6, // 100044
                                    'related_id'=>7, // 100148
                                    'relationship'=>"IsPreviousVersionOf", //10 IsPreviousVersionOf
                                )
                            )
                        );

        //then we set our expectations for a Cache Miss
        $cache->expects($this->exactly(1))
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetConnections_getRelations"))
                 ->willReturn( false );

        $cache->expects($this->exactly(1))
                ->method('set')
                ->with(
                    $this->equalTo("dataset_${dataset_id}_CachedDatasetConnections_getRelations"),
                    array(
                        array(
                            'dataset_id'=>6, // 100044
                            'related_id'=>5, // 100038
                            'relationship'=>"Compiles", //18 Compiles
                        ),
                        array(
                            'dataset_id'=>6, // 100044
                            'related_id'=>7, // 100148
                            'relationship'=>"IsPreviousVersionOf", //10 IsPreviousVersionOf
                        )
                    ),
                    Cacheable::defaultTTL,
                    $cacheDependency
                )
                ->willReturn(true);

        $expected = array(
            array(
                'dataset_id'=>6, // 100044
                'related_id'=>5, // 100038
                'relationship'=>"Compiles", //18 Compiles
            ),
            array(
                'dataset_id'=>6, // 100044
                'related_id'=>7, // 100148
                'relationship'=>"IsPreviousVersionOf", //10 IsPreviousVersionOf
            )
        );

        $daoUnderTest = new CachedDatasetConnections($cache, $cacheDependency, $storedDatasetConnections) ;
        $this->assertEquals($expected, $daoUnderTest->getRelations());
        // $this->assertEquals([$expected[1]], $daoUnderTest->getRelations("IsPreviousVersionOf"));
        // $this->assertEquals([$expected[0]], $daoUnderTest->getRelations("Compiles"));
        // $this->assertEquals([], $daoUnderTest->getRelations("DoesNotExistRelationship"));


    }
}
?>