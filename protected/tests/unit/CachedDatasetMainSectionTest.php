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
        'datasets'=>'Dataset',
        'types'=>'Type',
        'dataset_types'=>'DatasetType',
        'authors'=>'Author',
        'dataset_author'=>'DatasetAuthor',
    );

	public function setUp()
	{
		parent::setUp();
	}

    public function testCachedReturnsDatasetDOI()
    {
        $dataset_id = 1;
        $doi = "100243";
        //we first need to create a stub object for the cache
        $cache = $this->createMock(CApcCache::class);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
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

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
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
                    60*60*24
                )
                ->willReturn(true);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
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

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
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
                    60*60*24
                )
                ->willReturn(true);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
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

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
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
                    60*60*24
                )
                ->willReturn(true);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
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
        $argument = null;
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
                 ->with($argument)
                 ->willReturn($response);

        $daoUnderTest = new CachedDatasetMainSection (
                            $cache,
                            $storedDatasetMainSection
                        );
        $this->assertEquals($response, $daoUnderTest->getCitationsLinks($argument) ) ;
    }

}