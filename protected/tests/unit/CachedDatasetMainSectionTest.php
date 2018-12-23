<?php
/**
 * Unit tests for CachedDatasetMainSection to retrieve from cache the main section of a dataset view page
 *
 * @see DatasetAccessionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetMainSectionTest extends CDbTestCase
{
	protected $fixtures=array( //careful, the order matters here because of foreign key constraints
        'publishers'=>'Publisher',
        'attribute'=>'Attribute',
        'datasets'=>'Dataset',
        'types'=>'Type',
        'dataset_types'=>'DatasetType',
        'authors'=>'Author',
        'dataset_author'=>'DatasetAuthor',
        'dataset_attriutes'=>'DatasetAttributes',
    );

	public function setUp()
	{
		parent::setUp();
	}

    public function testCachedReturnsDatasetDOI()
    {
        $dataset_id = 1;
        $doi = "100243";
        // create a stub of the cache and cache dependency (because we don't need to verify expectations on the cache)
        $cache =  $this->createMock(CApcCache::class);
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
                            $cacheDependency,
                            new StoredDatasetMainSection(
                                $dataset_id,  $this->getFixtureManager()->getDbConnection()
                            )
                        );
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI() ) ;
    }

    /**
     * Test that we can retrieve headline data from cache successfully
     */
    public function testCachedReturnsHeadlineCacheHit()
    {
        $dataset_id = 1;


        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get'])
                         ->getMock();
        //then we set our expectation for a Cache Hit
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetMainSection_getHeadline"))
                 ->willReturn(
                    array(
                        "title" => 'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
                        "types" => array(
                            "Genomic",
                            "Workflow"
                        ),
                        "release_date"=> '2018-08-23',
                    )
                 );
        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
                            $cacheDependency,
                            new StoredDatasetMainSection(
                                $dataset_id,  $this->getFixtureManager()->getDbConnection()
                            )
                        );

        $expected = array(
                        "title" => 'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
                        "types" => array(
                            "Genomic",
                            "Workflow"
                        ),
                        "release_date"=> '2018-08-23',
                    );

        $this->assertEquals($expected, $daoUnderTest->getHeadline());
    }

    /**
     * Test that we can retrieve headline data from storage and populate the cache successfully if not found in cache 
     */
    public function testCachedReturnsHeadlineCacheMiss()
    {
        $dataset_id = 1;

        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get', 'set'])
                         ->getMock();
        //then we set our expectation for a Cache Miss
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetMainSection_getHeadline"))
                 ->willReturn(
                    false
                 );
        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        //And the expectation for setting up the data into cache
        $cache->expects($this->once())
                 ->method('set')
                 ->with(
                    $this->equalTo("dataset_${dataset_id}_CachedDatasetMainSection_getHeadline"),
                    array(
                        "title" => 'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
                        "types" => array(
                            "Genomic",
                            "Workflow"
                        ),
                        "release_date"=> '2018-08-23',
                    ),
                    Cacheable::defaultTTL*30,
                    $cacheDependency
                )
                ->willReturn(true);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
                            $cacheDependency,
                            new StoredDatasetMainSection(
                                $dataset_id,  $this->getFixtureManager()->getDbConnection()
                            )
                        );

        $expected = array(
                        "title" => 'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
                        "types" => array(
                            "Genomic",
                            "Workflow"
                        ),
                        "release_date"=> '2018-08-23',
                    );

        $this->assertEquals($expected, $daoUnderTest->getHeadline());
    }

    /**
     * Test that we can retrieve release details data from cache successfully
     */
    public function testCachedReturnsReleaseDetailsCacheHit()
    {
        $dataset_id = 1;

        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get'])
                         ->getMock();


        //then we set our expectation for a Cache Hit
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetMainSection_getReleaseDetails"))
                 ->willReturn(
                    array(
                        "authors" => array(
                            array(
                                'id' => 2,
                                'surname'=>'Montana,',
                                'first_name'=>'Carlos',
                                'middle_name'=>'Ábel G',
                                'custom_name'=>null,
                            ),
                            array(
                                'id' => 1,
                                'surname'=>'Muñoz',
                                'first_name'=>'Ángel',
                                'middle_name'=>'GG',
                                'custom_name'=>null,
                            ),
                            array(
                                'id' => 7,
                                'surname'=>'Schiøtt,',
                                'first_name'=>'Morten',
                                'middle_name'=>null,
                                'custom_name'=>null,
                            ),
                        ),
                        "release_year" => "2018",
                        "dataset_title" => 'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
                        "publisher" => "Gigascience",
                        "full_doi" => "10.5072/100243",
                    )
                 );

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
                            $cacheDependency,
                            new StoredDatasetMainSection(
                                $dataset_id,  $this->getFixtureManager()->getDbConnection()
                            )
                        );

        $expected = array(
                        "authors" => array(
                            array(
                                'id' => 2,
                                'surname'=>'Montana,',
                                'first_name'=>'Carlos',
                                'middle_name'=>'Ábel G',
                                'custom_name'=>null,
                            ),
                            array(
                                'id' => 1,
                                'surname'=>'Muñoz',
                                'first_name'=>'Ángel',
                                'middle_name'=>'GG',
                                'custom_name'=>null,
                            ),
                            array(
                                'id' => 7,
                                'surname'=>'Schiøtt,',
                                'first_name'=>'Morten',
                                'middle_name'=>null,
                                'custom_name'=>null,
                            ),
                        ),
                        "release_year" => "2018",
                        "dataset_title" => 'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
                        "publisher" => "Gigascience",
                        "full_doi" => "10.5072/100243",
                    );

        $this->assertEquals($expected, $daoUnderTest->getReleaseDetails());
    }

    /**
     * Test that we can retrieve release details data from storage and seed the cache when cache is invalid
     */
    public function testCachedReturnsReleaseDetailsCacheMiss()
    {
        $dataset_id = 1;

        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get','set'])
                         ->getMock();
        //then we set our expectations for a Cache Miss
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetMainSection_getReleaseDetails"))
                 ->willReturn(
                    false
                 );

        $cache->expects($this->once())
                 ->method('set')
                 ->with(
                    "dataset_${dataset_id}_CachedDatasetMainSection_getReleaseDetails",
                    array(
                        "authors" => array(
                            array(
                                'id' => 2,
                                'surname'=>'Montana,',
                                'first_name'=>'Carlos',
                                'middle_name'=>'Ábel G',
                                'custom_name'=>null,
                            ),
                            array(
                                'id' => 1,
                                'surname'=>'Muñoz',
                                'first_name'=>'Ángel',
                                'middle_name'=>'GG',
                                'custom_name'=>null,
                            ),
                            array(
                                'id' => 7,
                                'surname'=>'Schiøtt,',
                                'first_name'=>'Morten',
                                'middle_name'=>null,
                                'custom_name'=>null,
                            ),
                        ),
                        "release_year" => "2018",
                        "dataset_title" => 'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
                        "publisher" => "Gigascience",
                        "full_doi" => "10.5072/100243",
                    ),
                    Cacheable::defaultTTL*30
                )
                ->willReturn(true);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
                            $cacheDependency,
                            new StoredDatasetMainSection(
                                $dataset_id,  $this->getFixtureManager()->getDbConnection()
                            )
                        );

        $expected = array(
                        "authors" => array(
                            array(
                                'id' => 2,
                                'surname'=>'Montana,',
                                'first_name'=>'Carlos',
                                'middle_name'=>'Ábel G',
                                'custom_name'=>null,
                            ),
                            array(
                                'id' => 1,
                                'surname'=>'Muñoz',
                                'first_name'=>'Ángel',
                                'middle_name'=>'GG',
                                'custom_name'=>null,
                            ),
                            array(
                                'id' => 7,
                                'surname'=>'Schiøtt,',
                                'first_name'=>'Morten',
                                'middle_name'=>null,
                                'custom_name'=>null,
                            ),
                        ),
                        "release_year" => "2018",
                        "dataset_title" => 'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
                        "publisher" => "Gigascience",
                        "full_doi" => "10.5072/100243",
                    );

        $this->assertEquals($expected, $daoUnderTest->getReleaseDetails());
    }

    /**
     * Test that we can retrieve description data from cache successfully
     */
    public function testCachedReturnsDescriptionCacheHit()
    {
        $dataset_id = 1;

        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get'])
                         ->getMock();
        //then we set our expectation for a Cache Hit
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetMainSection_getDescription"))
                 ->willReturn(
                    array(
                        "description" => 'The emergence of Zika virus (ZIKV) as a public health emergency in Latin America and the Caribbean (LAC) occurred during a period of severe drought and unusually high temperatures. Speculation in the literature exists that these climate conditions were associated with the 2015/2016 El Niño event and/or climate change but to date no quantitative '
                    )
                 );

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
                            $cacheDependency,
                            new StoredDatasetMainSection(
                                $dataset_id,  $this->getFixtureManager()->getDbConnection()
                            )
                        );

        $expected = array(
                        "description" => 'The emergence of Zika virus (ZIKV) as a public health emergency in Latin America and the Caribbean (LAC) occurred during a period of severe drought and unusually high temperatures. Speculation in the literature exists that these climate conditions were associated with the 2015/2016 El Niño event and/or climate change but to date no quantitative '
                    );

        $this->assertEquals($expected, $daoUnderTest->getDescription());
    }

    /**
     * Test that we can retrieve description data from storage and seed the cache when cache entry is invalid
     */
    public function testCachedReturnsDescriptionCacheMiss()
    {
        $dataset_id = 1;

        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get','set'])
                         ->getMock();
        //then we set our expectation for a Cache Hit
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetMainSection_getDescription"))
                 ->willReturn(
                    false
                 );
        $cache->expects($this->once())
                 ->method('set')
                 ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetMainSection_getDescription"),
                    array(
                        "description" => 'The emergence of Zika virus (ZIKV) as a public health emergency in Latin America and the Caribbean (LAC) occurred during a period of severe drought and unusually high temperatures. Speculation in the literature exists that these climate conditions were associated with the 2015/2016 El Niño event and/or climate change but to date no quantitative '
                    ),
                    Cacheable::defaultTTL*30
                )
                ->willReturn(true);
        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
                            $cacheDependency,
                            new StoredDatasetMainSection(
                                $dataset_id,  $this->getFixtureManager()->getDbConnection()
                            )
                        );

        $expected = array(
                        "description" => 'The emergence of Zika virus (ZIKV) as a public health emergency in Latin America and the Caribbean (LAC) occurred during a period of severe drought and unusually high temperatures. Speculation in the literature exists that these climate conditions were associated with the 2015/2016 El Niño event and/or climate change but to date no quantitative '
                    );

        $this->assertEquals($expected, $daoUnderTest->getDescription());
    }

    /**
     * Testing getting the query links to citations search engines.
     *
     * We delegate straight to StoredDatasetMainSection as the data is read from config already loaded in memory,
     * so no need to be cached.
     */
    public function testCachedReturnsCitationsLinks()
    {
        $response =["dummy_key" => "dummy_value"];

        //we first need to stub the cache (not need to mock it in this test)
        $cache = $this->createMock(CApcCache::class);

        //we mock the StoredDatasetMainSection
        $storedDatasetMainSection = $this->getMockBuilder(StoredDatasetMainSection::class)
                         ->setMethods(['getCitationsLinks'])
                         ->disableOriginalConstructor() //so we dont have to pass doi and db_connection
                         ->getMock();

        //we expect a call to getCitationsLinks
        $storedDatasetMainSection->expects($this->once())
                 ->method('getCitationsLinks')
                 ->willReturn($response);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
                            $cacheDependency,
                            $storedDatasetMainSection
                        );
        $this->assertEquals($response, $daoUnderTest->getCitationsLinks() ) ;
    }

    /**
     * unit test for fetching keywords associated with a dataset, cache hit scenario
     *
     */
    public function testCachedReturnsKeywordsCacheHit()
    {
        $dataset_id = 1;

        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get'])
                         ->getMock();
        //then we set our expectation for a Cache Hit
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetMainSection_getKeywords"))
                 ->willReturn(
                    array("am", "gram")
                 );

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
                            $cacheDependency,
                            new StoredDatasetMainSection(
                                $dataset_id,  $this->getFixtureManager()->getDbConnection()
                            )
                        );

        $expected  = array("am", "gram");
        $this->assertEquals($expected, $daoUnderTest->getKeywords());
    }

    /**
     * unit test for fetching keywords associated with a dataset, cache miss scenario
     *
     */
    public function testCachedReturnsKeywordsCacheMiss()
    {
         $dataset_id = 1;

        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get','set'])
                         ->getMock();
        //then we set our expectation for a Cache Hit
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetMainSection_getKeywords"))
                 ->willReturn(
                    false
                 );

        $cache->expects($this->once())
                 ->method('set')
                 ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetMainSection_getKeywords"),
                    array("am", "gram"),
                    Cacheable::defaultTTL*30
                )
                ->willReturn(true);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
                            $cacheDependency,
                            new StoredDatasetMainSection(
                                $dataset_id,  $this->getFixtureManager()->getDbConnection()
                            )
                        );

        $expected  = array("am", "gram");
        $this->assertEquals($expected, $daoUnderTest->getKeywords());
    }

    /**
     * unit test for fetching keywords associated with a dataset, cache hit scenario
     *
     */
    public function testCachedReturnsHistoryCacheHit()
    {
        $dataset_id = 1;

        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get'])
                         ->getMock();
        //then we set our expectation for a Cache Hit
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetMainSection_getHistory"))
                 ->willReturn(
                    array(
                        array(
                            'id'=>1,
                            'dataset_id'=>1,
                            'message'=>"Updated the title",
                            'created_at'=>"2015-10-13 23:41:38.899752",
                            'model'=>"dataset",
                            'model_id'=>1,
                            'url'=>"",
                        ),
                        array(
                            'id'=>2,
                            'dataset_id'=>1,
                            'message'=>"File Tinamus_guttatus.fa.gz updated",
                            'created_at'=>"2015-10-12 16:16:37.09544",
                            'model'=>"File",
                            'model_id'=>16945,
                            'url'=>"/adminFile/update/id/16945",
                        )
                    )
                 );

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
                            $cacheDependency,
                            new StoredDatasetMainSection(
                                $dataset_id,  $this->getFixtureManager()->getDbConnection()
                            )
                        );

        $expected  = array(
                        array(
                            'id'=>1,
                            'dataset_id'=>1,
                            'message'=>"Updated the title",
                            'created_at'=>"2015-10-13 23:41:38.899752",
                            'model'=>"dataset",
                            'model_id'=>1,
                            'url'=>"",
                        ),
                        array(
                            'id'=>2,
                            'dataset_id'=>1,
                            'message'=>"File Tinamus_guttatus.fa.gz updated",
                            'created_at'=>"2015-10-12 16:16:37.09544",
                            'model'=>"File",
                            'model_id'=>16945,
                            'url'=>"/adminFile/update/id/16945",
                        )
                    );
        $this->assertEquals($expected, $daoUnderTest->getHistory());
    }

    /**
     * unit test for fetching keywords associated with a dataset, cache hit scenario
     *
     */
    public function testCachedReturnsHistoryCacheMiss()
    {
        $dataset_id = 1;

        $expected  = array(
                array(
                    'id'=>1,
                    'dataset_id'=>1,
                    'message'=>"Updated the title",
                    'created_at'=>"2015-10-13 23:41:38.899752",
                    'model'=>"dataset",
                    'model_id'=>1,
                    'url'=>"",
                ),
                array(
                    'id'=>2,
                    'dataset_id'=>1,
                    'message'=>"File Tinamus_guttatus.fa.gz updated",
                    'created_at'=>"2015-10-12 16:16:37.09544",
                    'model'=>"File",
                    'model_id'=>16945,
                    'url'=>"/adminFile/update/id/16945",
                )
            );

        //we mock the StoredDatasetMainSection
        $storedDatasetMainSection = $this->getMockBuilder(StoredDatasetMainSection::class)
                         ->setMethods(['getHistory','getDatasetId'])
                         ->disableOriginalConstructor() //so we dont have to pass doi and db_connection
                         ->getMock();

        //we expect a call to getHistory
        $storedDatasetMainSection->expects($this->once())
                 ->method('getHistory')
                 ->willReturn($expected);

        //we expect a call to getDatasetId
        $storedDatasetMainSection->expects($this->exactly(2))
                 ->method('getDatasetId')
                 ->willReturn(1);

        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get','set'])
                         ->getMock();
        //then we set our expectation for a Cache Hit
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetMainSection_getHistory"))
                 ->willReturn(
                    false
                 );

         $cache->expects($this->once())
                 ->method('set')
                 ->with($this->equalTo("dataset_${dataset_id}_CachedDatasetMainSection_getHistory"),
                    $expected,
                    Cacheable::defaultTTL*30
                )
                ->willReturn(true);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
                            $cacheDependency,
                            $storedDatasetMainSection
                        );

        $this->assertEquals($expected, $daoUnderTest->getHistory());
    }


}