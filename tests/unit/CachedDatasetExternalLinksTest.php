<?php

/**
 * Unit tests for CachedDatasetExternalLinks to retrieve from cache, external links associated to a dataset
 *
 * @see DatasetAccessionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetExternalLinksTest extends CTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testCachedReturnsDatasetId()
    {
        $dataset_id = 6;
        // create a mock for the StoredDatasetConnection
        $storedDatasetExternalLinks = $this->getMockBuilder(StoredDatasetExternalLinks::class)
                         ->setMethods(['getDatasetId'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a Cache Hit, and therefo
        $storedDatasetExternalLinks->expects($this->once())
                 ->method('getDatasetId')
                 ->willReturn(6);

        // create a stub of the cache and cache dependency (because we don't need to verify expectations on the cache)
        $cache =  $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $daoUnderTest = new CachedDatasetExternalLinks($cache, $cacheDependency, $storedDatasetExternalLinks);
        $this->assertEquals($dataset_id, $daoUnderTest->getDatasetId()) ;
    }

    public function testCachedReturnsDatasetDOI()
    {
        $dataset_id = 6;
        $doi = "100044";
        // create a mock for the StoredDatasetConnection
        $storedDatasetExternalLinks = $this->getMockBuilder(StoredDatasetExternalLinks::class)
                         ->setMethods(['getDatasetDOI'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a Cache Hit, and therefo
        $storedDatasetExternalLinks->expects($this->once())
                 ->method('getDatasetDOI')
                 ->willReturn("100044");
        // create a stub of the cache and cache dependency (because we don't need to verify expectations on the cache)
        $cache =  $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);


        $daoUnderTest = new CachedDatasetExternalLinks($cache, $cacheDependency, $storedDatasetExternalLinks);
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI()) ;
    }

    public function testCachedReturnsDataetExternalLinksCacheHit()
    {
        $dataset_id = 1 ;

        // create a stub for the StoredDatasetConnection, cause we have no expectation on it as cache hit
        $storedDatasetExternalLinks = $this->createMock(StoredDatasetExternalLinks::class);
        $storedDatasetExternalLinks->method('getDatasetID')
                                    ->willReturn(1);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        // create a mock for the cache and we need to make the cache method for getting the key
        $cache = $this->getMockBuilder(CApcCache::class)
                        ->setMethods(['get'])
                        ->getMock();

        //then we set our expectation for a Cache Hit
        $cache->expects($this->exactly(4))
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetExternalLinks_getDatasetExternalLinks"))
                 ->willReturn(
                     array(
                        array(
                            'id' => 1,
                            'dataset_id' => 1,
                            'url' => "http://foo.com",
                            'external_link_type_id' => 1,
                            'external_link_type_name' => "Additional information",
                        ),
                        array(
                            'id' => 2,
                            'dataset_id' => 1,
                            'url' => "http://foo2.com",
                            'external_link_type_id' => 1,
                            'external_link_type_name' => "Additional information",
                        ),
                        array(
                            'id' => 3,
                            'dataset_id' => 1,
                            'url' => "http://foo3.com",
                            'external_link_type_id' => 2,
                            'external_link_type_name' => "Genome browser",
                        ),
                        array(
                            'id' => 4,
                            'dataset_id' => 1,
                            'url' => "http://foo4.com",
                            'external_link_type_id' => 3,
                            'external_link_type_name' => "Protocols.io",
                        ),
                        array(
                            'id' => 5,
                            'dataset_id' => 1,
                            'url' => "http://foo5.com",
                            'external_link_type_id' => 4,
                            'external_link_type_name' => "JBrowse",
                        ),
                     )
                 );

        $expected = array(
            array(
                'id' => 1,
                'dataset_id' => 1,
                'url' => "http://foo.com",
                'external_link_type_id' => 1,
                'external_link_type_name' => "Additional information",
            ),
            array(
                'id' => 2,
                'dataset_id' => 1,
                'url' => "http://foo2.com",
                'external_link_type_id' => 1,
                'external_link_type_name' => "Additional information",
            ),
            array(
                'id' => 3,
                'dataset_id' => 1,
                'url' => "http://foo3.com",
                'external_link_type_id' => 2,
                'external_link_type_name' => "Genome browser",
            ),
            array(
                'id' => 4,
                'dataset_id' => 1,
                'url' => "http://foo4.com",
                'external_link_type_id' => 3,
                'external_link_type_name' => "Protocols.io",
            ),
            array(
                'id' => 5,
                'dataset_id' => 1,
                'url' => "http://foo5.com",
                'external_link_type_id' => 4,
                'external_link_type_name' => "JBrowse",
            ),
        );

        $daoUnderTest = new CachedDatasetExternalLinks($cache, $cacheDependency, $storedDatasetExternalLinks) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetExternalLinks());
        $this->assertEquals([$expected[2]], $daoUnderTest->getDatasetExternalLinks(["Genome browser"])) ;
        $this->assertEquals([$expected[3],$expected[4]], $daoUnderTest->getDatasetExternalLinks(["JBrowse", "Protocols.io"])) ;
        $this->assertEquals([], $daoUnderTest->getDatasetExternalLinks(["fake"])) ;
    }

    public function testCachedReturnsDatasetExternalLinksCacheMiss()
    {
        $dataset_id = 1;

        // create a mock for the StoredDatasetExternalLinks, as we expect it to be called for data retrieval
        $storedDatasetExternalLinks = $this->getMockBuilder(StoredDatasetExternalLinks::class)
                                            ->setMethods(['getDatasetID', 'getDatasetExternalLinks'])
                                            ->disableOriginalConstructor()
                                            ->getMock();
        $storedDatasetExternalLinks->expects($this->exactly(8))
                                    ->method('getDatasetID')
                                    ->willReturn(1);
        $storedDatasetExternalLinks->expects($this->exactly(4))
                                    ->method('getDatasetExternalLinks')
                                    ->willReturn(
                                        array(
                                            array(
                                                'id' => 1,
                                                'dataset_id' => 1,
                                                'url' => "http://foo.com",
                                                'external_link_type_id' => 1,
                                                'external_link_type_name' => "Additional information",
                                            ),
                                            array(
                                                'id' => 2,
                                                'dataset_id' => 1,
                                                'url' => "http://foo2.com",
                                                'external_link_type_id' => 1,
                                                'external_link_type_name' => "Additional information",
                                            ),
                                            array(
                                                'id' => 3,
                                                'dataset_id' => 1,
                                                'url' => "http://foo3.com",
                                                'external_link_type_id' => 2,
                                                'external_link_type_name' => "Genome browser",
                                            ),
                                            array(
                                                'id' => 4,
                                                'dataset_id' => 1,
                                                'url' => "http://foo4.com",
                                                'external_link_type_id' => 3,
                                                'external_link_type_name' => "Protocols.io",
                                            ),
                                            array(
                                                'id' => 5,
                                                'dataset_id' => 1,
                                                'url' => "http://foo5.com",
                                                'external_link_type_id' => 4,
                                                'external_link_type_name' => "JBrowse",
                                            ),
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
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetExternalLinks_getDatasetExternalLinks"))
                 ->willReturn(false);

        $cache->expects($this->exactly(4))
                ->method('set')
                ->with(
                    $this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetExternalLinks_getDatasetExternalLinks"),
                    array(
                        array(
                            'id' => 1,
                            'dataset_id' => 1,
                            'url' => "http://foo.com",
                            'external_link_type_id' => 1,
                            'external_link_type_name' => "Additional information",
                        ),
                        array(
                            'id' => 2,
                            'dataset_id' => 1,
                            'url' => "http://foo2.com",
                            'external_link_type_id' => 1,
                            'external_link_type_name' => "Additional information",
                        ),
                        array(
                            'id' => 3,
                            'dataset_id' => 1,
                            'url' => "http://foo3.com",
                            'external_link_type_id' => 2,
                            'external_link_type_name' => "Genome browser",
                        ),
                        array(
                            'id' => 4,
                            'dataset_id' => 1,
                            'url' => "http://foo4.com",
                            'external_link_type_id' => 3,
                            'external_link_type_name' => "Protocols.io",
                        ),
                        array(
                            'id' => 5,
                            'dataset_id' => 1,
                            'url' => "http://foo5.com",
                            'external_link_type_id' => 4,
                            'external_link_type_name' => "JBrowse",
                        ),
                    ),
                    Cacheable::defaultTTL * 30,
                    $cacheDependency
                )
                ->willReturn(true);

        $expected = array(
            array(
                'id' => 1,
                'dataset_id' => 1,
                'url' => "http://foo.com",
                'external_link_type_id' => 1,
                'external_link_type_name' => "Additional information",
            ),
            array(
                'id' => 2,
                'dataset_id' => 1,
                'url' => "http://foo2.com",
                'external_link_type_id' => 1,
                'external_link_type_name' => "Additional information",
            ),
            array(
                'id' => 3,
                'dataset_id' => 1,
                'url' => "http://foo3.com",
                'external_link_type_id' => 2,
                'external_link_type_name' => "Genome browser",
            ),
            array(
                'id' => 4,
                'dataset_id' => 1,
                'url' => "http://foo4.com",
                'external_link_type_id' => 3,
                'external_link_type_name' => "Protocols.io",
            ),
            array(
                'id' => 5,
                'dataset_id' => 1,
                'url' => "http://foo5.com",
                'external_link_type_id' => 4,
                'external_link_type_name' => "JBrowse",
            ),
        );

        $daoUnderTest = new CachedDatasetExternalLinks($cache, $cacheDependency, $storedDatasetExternalLinks) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetExternalLinks());
        $this->assertEquals([$expected[2]], $daoUnderTest->getDatasetExternalLinks(["Genome browser"])) ;
        $this->assertEquals([$expected[3],$expected[4]], $daoUnderTest->getDatasetExternalLinks(["JBrowse", "Protocols.io"])) ;
        $this->assertEquals([], $daoUnderTest->getDatasetExternalLinks(["fake"])) ;
    }

    public function testCachedReturnsDatasetExternalLinksTypesAndCountCacheHit()
    {
        $dataset_id = 1 ;

        // create a stub for the StoredDatasetConnection, cause we have no expectation on it as cache hit
        $storedDatasetExternalLinks = $this->createMock(StoredDatasetExternalLinks::class);
        $storedDatasetExternalLinks->method('getDatasetID')
                                    ->willReturn(1);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        // create a mock for the cache and we need to make the cache method for getting the key
        $cache = $this->getMockBuilder(CApcCache::class)
                        ->setMethods(['get'])
                        ->getMock();

        //then we set our expectation for a Cache Hit
        $cache->expects($this->exactly(3))
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetExternalLinks_getDatasetExternalLinksTypesAndCount"))
                 ->willReturn(
                     array(
                        "Additional information" => 2,
                        "Genome browser" => 1,
                        "Protocols.io" => 1,
                        "JBrowse" => 1,
                     )
                 );

        $expected = array(
            "Additional information" => 2,
            "Genome browser" => 1,
            "Protocols.io" => 1,
            "JBrowse" => 1,
        );

        $daoUnderTest = new CachedDatasetExternalLinks($cache, $cacheDependency, $storedDatasetExternalLinks) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetExternalLinksTypesAndCount());
        $this->assertEquals(array("Genome browser" => 1), $daoUnderTest->getDatasetExternalLinksTypesAndCount(["Genome browser"])) ;
        $this->assertEquals(array("Protocols.io" => 1, "JBrowse" => 1), $daoUnderTest->getDatasetExternalLinksTypesAndCount(["JBrowse", "Protocols.io"])) ;
    }

    public function testCachedReturnsDatasetExternalLinksTypesAndCountCacheMiss()
    {
        $dataset_id = 1;

        // create a mock for the StoredDatasetExternalLinks, as we expect it to be called for data retrieval
        $storedDatasetExternalLinks = $this->getMockBuilder(StoredDatasetExternalLinks::class)
                                            ->setMethods(['getDatasetID', 'getDatasetExternalLinksTypesAndCount'])
                                            ->disableOriginalConstructor()
                                            ->getMock();
        $storedDatasetExternalLinks->expects($this->exactly(6))
                                    ->method('getDatasetID')
                                    ->willReturn(1);
        $storedDatasetExternalLinks->expects($this->exactly(3))
                                    ->method('getDatasetExternalLinksTypesAndCount')
                                    ->willReturn(
                                        array(
                                            "Additional information" => 2,
                                            "Genome browser" => 1,
                                            "Protocols.io" => 1,
                                            "JBrowse" => 1,
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
        $cache->expects($this->exactly(3))
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetExternalLinks_getDatasetExternalLinksTypesAndCount"))
                 ->willReturn(false);

        $cache->expects($this->exactly(3))
                ->method('set')
                ->with(
                    $this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetExternalLinks_getDatasetExternalLinksTypesAndCount"),
                    array(
                        "Additional information" => 2,
                        "Genome browser" => 1,
                        "Protocols.io" => 1,
                        "JBrowse" => 1,
                    ),
                    Cacheable::defaultTTL * 30,
                    $cacheDependency
                )
                ->willReturn(true);

        $expected = array(
                        "Additional information" => 2,
                        "Genome browser" => 1,
                        "Protocols.io" => 1,
                        "JBrowse" => 1,
                    );

        $daoUnderTest = new CachedDatasetExternalLinks($cache, $cacheDependency, $storedDatasetExternalLinks) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetExternalLinksTypesAndCount());
        $this->assertEquals(array("Genome browser" => 1), $daoUnderTest->getDatasetExternalLinksTypesAndCount(["Genome browser"])) ;
        $this->assertEquals(array("Protocols.io" => 1, "JBrowse" => 1), $daoUnderTest->getDatasetExternalLinksTypesAndCount(["JBrowse", "Protocols.io"])) ;
    }
}
