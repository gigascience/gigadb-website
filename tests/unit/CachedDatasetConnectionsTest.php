<?php

/**
 * Unit tests for CachedDatasetConnections to retrieve from cache connected datasets
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetConnectionsTest extends CTestCase
{
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
        $this->assertEquals($dataset_id, $daoUnderTest->getDatasetId()) ;
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
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI()) ;
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
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetConnections_getRelations"))
                 ->willReturn(
                     array(
                        array(
                            'dataset_id' => 6, // 100044
                            'dataset_doi' => "100044", // 100044
                            'related_id' => 5, // 100038
                            'related_doi' => "100038", // 100038
                            'relationship' => "Compiles", //18 Compiles
                        ),
                        array(
                            'dataset_id' => 6, // 100044
                            'dataset_doi' => "100044", // 100044
                            'related_id' => 7, // 100148
                            'related_doi' => "100148", // 100148
                            'relationship' => "IsPreviousVersionOf", //10 IsPreviousVersionOf
                        )
                     )
                 );

        $expected = array(
            array(
                'dataset_id' => 6, // 100044
                'dataset_doi' => "100044", // 100044
                'related_id' => 5, // 100038
                'related_doi' => "100038", // 100038
                'relationship' => "Compiles", //18 Compiles
            ),
            array(
                'dataset_id' => 6, // 100044
                'dataset_doi' => "100044", // 100044
                'related_id' => 7, // 100148
                'related_doi' => "100148", // 100148
                'relationship' => "IsPreviousVersionOf", //10 IsPreviousVersionOf
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
        $storedDatasetConnections->expects($this->exactly(8))
                                    ->method('getDatasetID')
                                    ->willReturn(6);
        $storedDatasetConnections->expects($this->exactly(4))
                                    ->method('getRelations')
                                    ->willReturn(
                                        array(
                                            array(
                                                'dataset_id' => 6, // 100044
                                                'dataset_doi' => "100044", // 100044
                                                'related_id' => 5, // 100038
                                                'related_doi' => "100038", // 100038
                                                'relationship' => "Compiles", //18 Compiles
                                            ),
                                            array(
                                                'dataset_id' => 6, // 100044
                                                'dataset_doi' => "100044", // 100044
                                                'related_id' => 7, // 100148
                                                'related_doi' => "100148", // 100148
                                                'relationship' => "IsPreviousVersionOf", //10 IsPreviousVersionOf
                                            )
                                        )
                                    );

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cache =  $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);

        // create a mock for the cache and we need to make the cache method for getting the key
        $cache = $this->getMockBuilder(CApcCache::class)
                        ->setMethods(['get', 'set'])
                        ->getMock();

        //then we set our expectations for a Cache Miss
        $cache->expects($this->exactly(4))
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetConnections_getRelations"))
                 ->willReturn(false);

        $cache->expects($this->exactly(4))
                ->method('set')
                ->with(
                    $this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetConnections_getRelations"),
                    array(
                        array(
                            'dataset_id' => 6, // 100044
                            'dataset_doi' => "100044", // 100044
                            'related_id' => 5, // 100038
                            'related_doi' => "100038", // 100038
                            'relationship' => "Compiles", //18 Compiles
                        ),
                        array(
                            'dataset_id' => 6, // 100044
                            'dataset_doi' => "100044", // 100044
                            'related_id' => 7, // 100148
                            'related_doi' => "100148", // 100148
                            'relationship' => "IsPreviousVersionOf", //10 IsPreviousVersionOf
                        )
                    ),
                    Cacheable::defaultTTL * 30,
                    $cacheDependency
                )
                ->willReturn(true);

        $expected = array(
            array(
                'dataset_id' => 6, // 100044
                'dataset_doi' => "100044", // 100044
                'related_id' => 5, // 100038
                'related_doi' => "100038", // 100038
                'relationship' => "Compiles", //18 Compiles
            ),
            array(
                'dataset_id' => 6, // 100044
                'dataset_doi' => "100044", // 100044
                'related_id' => 7, // 100148
                'related_doi' => "100148", // 100148
                'relationship' => "IsPreviousVersionOf", //10 IsPreviousVersionOf
            )
        );

        $daoUnderTest = new CachedDatasetConnections($cache, $cacheDependency, $storedDatasetConnections) ;
        $this->assertEquals($expected, $daoUnderTest->getRelations());
        $this->assertEquals([$expected[1]], $daoUnderTest->getRelations("IsPreviousVersionOf"));
        $this->assertEquals([$expected[0]], $daoUnderTest->getRelations("Compiles"));
        $this->assertEquals([], $daoUnderTest->getRelations("DoesNotExistRelationship"));
    }

    public function testCachedReturnsPublicationsCacheHit()
    {
        $dataset_id = 1;

        // create a stub for the StoredDatasetConnection, cause we have no expectation on it as cache hit
        $storedDatasetConnections = $this->createMock(StoredDatasetConnections::class);
        $storedDatasetConnections->method('getDatasetID')
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
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetConnections_getPublications"))
                 ->willReturn(
                     array(
                        array(
                            'id' => 1,
                            'identifier' => "10.1186/gb-2012-13-10-r100",
                            'pmid' => 23075480,
                            'dataset_id' => 1,
                            'citation' => "full citation fetched remotely. doi:10.1186/gb-2012-13-10-r100",
                            'pmurl' => "http://www.ncbi.nlm.nih.gov/pubmed/23075480",
                        ),
                        array(
                            'id' => 2,
                            'identifier' => "10.1038/nature10158",
                            'pmid' => null,
                            'dataset_id' => 1,
                            'citation' => "Another full citation fetched remotely. doi:10.1038/nature10158",
                            'pmurl' => null,
                        ),
                     )
                 );

        $expected = array(
                        array(
                            'id' => 1,
                            'identifier' => "10.1186/gb-2012-13-10-r100",
                            'pmid' => 23075480,
                            'dataset_id' => 1,
                            'citation' => "full citation fetched remotely. doi:10.1186/gb-2012-13-10-r100",
                            'pmurl' => "http://www.ncbi.nlm.nih.gov/pubmed/23075480",
                        ),
                        array(
                            'id' => 2,
                            'identifier' => "10.1038/nature10158",
                            'pmid' => null,
                            'dataset_id' => 1,
                            'citation' => "Another full citation fetched remotely. doi:10.1038/nature10158",
                            'pmurl' => null,
                        ),
                    );

        $daoUnderTest = new CachedDatasetConnections($cache, $cacheDependency, $storedDatasetConnections) ;
        $this->assertEquals($expected, $daoUnderTest->getPublications());
    }

    public function testCachedReturnsPublicationsCacheMiss()
    {
        $dataset_id = 1;

        // create a mock for the StoredDatasetConnection, cause we expect a call
        $storedDatasetConnections = $this->getMockBuilder(StoredDatasetConnections::class)
                                            ->setMethods(['getDatasetID','getPublications'])
                                            ->disableOriginalConstructor()
                                            ->getMock();

        $storedDatasetConnections->expects($this->exactly(2))
                                    ->method('getDatasetID')
                                    ->willReturn(1);

        $storedDatasetConnections->expects($this->once())
                                    ->method('getPublications')
                                    ->willReturn(
                                        array(
                                            array(
                                                'id' => 1,
                                                'identifier' => "10.1186/gb-2012-13-10-r100",
                                                'pmid' => 23075480,
                                                'dataset_id' => 1,
                                                'citation' => "full citation fetched remotely. doi:10.1186/gb-2012-13-10-r100",
                                                'pmurl' => "http://www.ncbi.nlm.nih.gov/pubmed/23075480",
                                            ),
                                            array(
                                                'id' => 2,
                                                'identifier' => "10.1038/nature10158",
                                                'pmid' => null,
                                                'dataset_id' => 1,
                                                'citation' => "Another full citation fetched remotely. doi:10.1038/nature10158",
                                                'pmurl' => null,
                                            ),
                                        )
                                    );
        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        // create a mock for the cache and we need to make the cache method for getting the key
        $cache = $this->getMockBuilder(CApcCache::class)
                        ->setMethods(['get','set'])
                        ->getMock();

        //then we set our expectations for a Cache Miss
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetConnections_getPublications"))
                 ->willReturn(
                     false
                 );

        $cache->expects($this->once())
                ->method('set')
                ->with(
                    $this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetConnections_getPublications"),
                    array(
                        array(
                            'id' => 1,
                            'identifier' => "10.1186/gb-2012-13-10-r100",
                            'pmid' => 23075480,
                            'dataset_id' => 1,
                            'citation' => "full citation fetched remotely. doi:10.1186/gb-2012-13-10-r100",
                            'pmurl' => "http://www.ncbi.nlm.nih.gov/pubmed/23075480",
                        ),
                        array(
                            'id' => 2,
                            'identifier' => "10.1038/nature10158",
                            'pmid' => null,
                            'dataset_id' => 1,
                            'citation' => "Another full citation fetched remotely. doi:10.1038/nature10158",
                            'pmurl' => null,
                        ),
                    ),
                    Cacheable::defaultTTL * 30,
                    $cacheDependency
                )
                ->willReturn(true);

        $expected = array(
                        array(
                            'id' => 1,
                            'identifier' => "10.1186/gb-2012-13-10-r100",
                            'pmid' => 23075480,
                            'dataset_id' => 1,
                            'citation' => "full citation fetched remotely. doi:10.1186/gb-2012-13-10-r100",
                            'pmurl' => "http://www.ncbi.nlm.nih.gov/pubmed/23075480",
                        ),
                        array(
                            'id' => 2,
                            'identifier' => "10.1038/nature10158",
                            'pmid' => null,
                            'dataset_id' => 1,
                            'citation' => "Another full citation fetched remotely. doi:10.1038/nature10158",
                            'pmurl' => null,
                        ),
                    );

        $daoUnderTest = new CachedDatasetConnections($cache, $cacheDependency, $storedDatasetConnections) ;
        $this->assertEquals($expected, $daoUnderTest->getPublications());
    }

    public function testCachedReturnsProjectsCacheHit()
    {
         $dataset_id = 1;

        // create a stub for the StoredDatasetConnection, cause we have no expectation on it as cache hit
        $storedDatasetConnections = $this->createMock(StoredDatasetConnections::class);
        $storedDatasetConnections->method('getDatasetID')
                                    ->willReturn(1);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        // create a mock for the cache and we need to make the cache method for getting the key
        $cache = $this->getMockBuilder(CApcCache::class)
                        ->setMethods(['get'])
                        ->getMock();

        //then we set our expectation for a Cache Hit
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetConnections_getProjects"))
                 ->willReturn(
                     array(
                        array(
                            'id' => 1,
                            'url' => "http://avian.genomics.cn/en/index.html",
                            'name' => "The Avian Phylogenomic Project",
                            'image_location' => "http://gigadb.org/images/project/phylogenomiclogo.png",
                        ),
                        array(
                            'id' => 2,
                            'url' => "http://www.genome10k.org/",
                            'name' => "Genome 10K",
                            'image_location' => null,
                        ),
                     )
                 );

        $expected = array(
                        array(
                            'id' => 1,
                            'url' => "http://avian.genomics.cn/en/index.html",
                            'name' => "The Avian Phylogenomic Project",
                            'image_location' => "http://gigadb.org/images/project/phylogenomiclogo.png",
                        ),
                        array(
                            'id' => 2,
                            'url' => "http://www.genome10k.org/",
                            'name' => "Genome 10K",
                            'image_location' => null,
                        ),
        );

        $daoUnderTest = new CachedDatasetConnections($cache, $cacheDependency, $storedDatasetConnections) ;
        $this->assertEquals($expected, $daoUnderTest->getProjects());
    }

    public function testCachedReturnsProjectsCacheMiss()
    {
        $dataset_id = 1;

        // create a mock for the StoredDatasetConnection, cause we expect a call
        $storedDatasetConnections = $this->getMockBuilder(StoredDatasetConnections::class)
                                            ->setMethods(['getDatasetID','getProjects'])
                                            ->disableOriginalConstructor()
                                            ->getMock();

        $storedDatasetConnections->expects($this->exactly(2))
                                    ->method('getDatasetID')
                                    ->willReturn(1);

        $storedDatasetConnections->expects($this->once())
                                    ->method('getProjects')
                                    ->willReturn(
                                        array(
                                            array(
                                                'id' => 1,
                                                'url' => "http://avian.genomics.cn/en/index.html",
                                                'name' => "The Avian Phylogenomic Project",
                                                'image_location' => "http://gigadb.org/images/project/phylogenomiclogo.png",
                                            ),
                                            array(
                                                'id' => 2,
                                                'url' => "http://www.genome10k.org/",
                                                'name' => "Genome 10K",
                                                'image_location' => null,
                                            ),
                                        )
                                    );

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        // create a mock for the cache and we need to make the cache method for getting the key
        $cache = $this->getMockBuilder(CApcCache::class)
                        ->setMethods(['get','set'])
                        ->getMock();

        //then we set our expectations for a Cache Miss
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetConnections_getProjects"))
                 ->willReturn(
                     false
                 );

        $cache->expects($this->once())
                 ->method('set')
                                 ->with(
                                     $this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetConnections_getProjects"),
                                     array(
                                     array(
                                     'id' => 1,
                                     'url' => "http://avian.genomics.cn/en/index.html",
                                     'name' => "The Avian Phylogenomic Project",
                                     'image_location' => "http://gigadb.org/images/project/phylogenomiclogo.png",
                                     ),
                                     array(
                                     'id' => 2,
                                     'url' => "http://www.genome10k.org/",
                                     'name' => "Genome 10K",
                                     'image_location' => null,
                                     ),
                                     ),
                                     Cacheable::defaultTTL * 30,
                                     $cacheDependency
                                 )
                 ->willReturn(true);

        $expected = array(
                        array(
                            'id' => 1,
                            'url' => "http://avian.genomics.cn/en/index.html",
                            'name' => "The Avian Phylogenomic Project",
                            'image_location' => "http://gigadb.org/images/project/phylogenomiclogo.png",
                        ),
                        array(
                            'id' => 2,
                            'url' => "http://www.genome10k.org/",
                            'name' => "Genome 10K",
                            'image_location' => null,
                        ),
        );

        $daoUnderTest = new CachedDatasetConnections($cache, $cacheDependency, $storedDatasetConnections) ;
        $this->assertEquals($expected, $daoUnderTest->getProjects());
    }
}
