<?php

/**
 * Unit tests for FormattedDatasetMainSection to present to the dataset view main dataset info
 *
 * @see DatasetAccessionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FormattedDatasetMainSectionTest extends CTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testFormattedReturnsDatasetDOI()
    {
        $dataset_id = 1;
        $doi = "100243";
        //we first need to create a mock for the CachedDatasetMainSection
        $cachedDatasetMainSection = $this->getMockBuilder(CachedDatasetMainSection::class)
                         ->setMethods(['getDatasetDOI'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a method call
        $cachedDatasetMainSection->expects($this->once())
                 ->method('getDatasetDOI')
                 ->willReturn($doi);

        $daoUnderTest = new FormattedDatasetMainSection(
            $cachedDatasetMainSection
        );
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI()) ;
    }

    /**
     * Test that we can retrieve formatted headline data from cache successfully
     */
    public function testFormattedReturnsHeadline()
    {
        $dataset_id = 1;

        //we first need to create a mock for the CachedDatasetMainSection
        $cachedDatasetMainSection = $this->getMockBuilder(CachedDatasetMainSection::class)
                         ->setMethods(['getHeadline'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a method call
        $cachedDatasetMainSection->expects($this->once())
                 ->method('getHeadline')
                 ->willReturn(
                     array(
                            "title" => 'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
                            "types" => array(
                                "Genomic",
                                "Workflow"
                            ),
                            "release_date" => '2018-08-23',
                        )
                 );

        $daoUnderTest = new FormattedDatasetMainSection($cachedDatasetMainSection);

        $expected = array(
                        "title" => 'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
                        "types" => "Genomic, Workflow",
                        "release_date" => "August 23, 2018",
                    );

        $this->assertEquals($expected, $daoUnderTest->getHeadline());

        // when empty result is returned from CachedDatasetMainSection
        $cachedDatasetMainSection2 = $this->getMockBuilder(CachedDatasetMainSection::class)
                 ->setMethods(['getHeadline'])
                 ->disableOriginalConstructor()
                 ->getMock();
        $cachedDatasetMainSection2->expects($this->once())
                 ->method('getHeadline')
                 ->willReturn([]);

        $daoUnderTest2 = new FormattedDatasetMainSection($cachedDatasetMainSection2);
        $expected = array(
                        "title" => "",
                        "types" => "",
                        "release_date" => "",
                    );
        $this->assertEquals($expected, $daoUnderTest2->getHeadline());
    }


    /**
     * Test that we can retrieve release details data from cache and format each property for presenttion
     */
    public function testFormattedReturnsReleaseDetails()
    {
        $dataset_id = 1;

        //we first need to create a mock for the CachedDatasetMainSection
        $cachedDatasetMainSection = $this->getMockBuilder(CachedDatasetMainSection::class)
                         ->setMethods(['getReleaseDetails'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a method call
        $cachedDatasetMainSection->expects($this->once())
                 ->method('getReleaseDetails')
                 ->willReturn(
                     array(
                        "authors" => array(
                            array(
                                'id' => 2,
                                'surname' => 'Montana,',
                                'first_name' => 'Carlos',
                                'middle_name' => 'Ábel G',
                                'custom_name' => 'Montana C', //It's in this scenario that this property make a difference
                            ),
                            array(
                                'id' => 1,
                                'surname' => 'Muñoz',
                                'first_name' => 'Ángel',
                                'middle_name' => 'GG',
                                'custom_name' => null,
                            ),
                            array(
                                'id' => 7,
                                'surname' => 'Schiøtt,',
                                'first_name' => 'Morten',
                                'middle_name' => null,
                                'custom_name' => null,
                            ),
                        ),
                        "release_year" => "2018",
                        "dataset_title" => 'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
                        "publisher" => "Gigascience",
                        "full_doi" => "10.5072/100243",
                     )
                 );

        $daoUnderTest = new FormattedDatasetMainSection($cachedDatasetMainSection);

        $expected = array(
                        "authors" => '<a class="result-sub-links" href="/search/new?keyword=Montana C&amp;author_id=2">Montana C</a>; <a class="result-sub-links" href="/search/new?keyword=Muñoz ÁGG&amp;author_id=1">Muñoz ÁGG</a>; <a class="result-sub-links" href="/search/new?keyword=Schiøtt M&amp;author_id=7">Schiøtt M</a>',
                        "release_year" => "2018",
                        "dataset_title" => 'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
                        "publisher" => "Gigascience",
                        "full_doi" => "10.5072/100243",
                    );

        $this->assertEquals($expected, $daoUnderTest->getReleaseDetails());

        // return the skeleton array with keys if no result returned from cache
        $cachedDatasetMainSection2 = $this->getMockBuilder(CachedDatasetMainSection::class)
                 ->setMethods(['getReleaseDetails'])
                 ->disableOriginalConstructor()
                 ->getMock();
        $cachedDatasetMainSection2->expects($this->once())
                 ->method('getReleaseDetails')
                 ->willReturn([]);

        $daoUnderTest2 = new FormattedDatasetMainSection($cachedDatasetMainSection2);
        $expected = array(
                        "authors" => "",
                        "release_year" => "",
                        "dataset_title" => "",
                        "publisher" => "",
                        "full_doi" => "",
                    );
        $this->assertEquals($expected, $daoUnderTest2->getReleaseDetails());
    }


    /**
     * Test that we can retrieve description data from cache for presentation
     */
    public function testFormattedReturnsDescription()
    {
        $dataset_id = 1;

        //we first need to create a mock for the CachedDatasetMainSection
        $cachedDatasetMainSection = $this->getMockBuilder(CachedDatasetMainSection::class)
                         ->setMethods(['getDescription'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a method call
        $cachedDatasetMainSection->expects($this->once())
                 ->method('getDescription')
                 ->willReturn(
                     array(
                        "description" => 'The emergence of Zika virus (ZIKV) as a public health emergency in Latin America and the Caribbean (LAC) occurred during a period of severe drought and unusually high temperatures. Speculation in the literature exists that these climate conditions were associated with the 2015/2016 El Niño event and/or climate change but to date no quantitative '
                     )
                 );

        $daoUnderTest = new FormattedDatasetMainSection($cachedDatasetMainSection);

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
    public function testFormattedReturnsCitationsLinks()
    {
        $source = array(
            'services' => array(
                'scholar_query' => "View citations on Google Scholar",
                'ePMC_query' => "View citations on Europe PubMed Central",
                ),
            'urls' => array(
                'scholar_query' => 'http://scholar.google.com/scholar?q=10.5072/100243',
                'ePMC_query' => "http://europepmc.org/search?scope=fulltext&query=(REF:'10.5072/100243')",
                ),
            'images' => array(
                'scholar_query' => '/images/google_scholar.png',
                'ePMC_query' => "/images/ePMC.jpg",
            ),
        );

        $expected = array(
            "scholar_query" => '<span>1</span>',
            "ePMC_query" => '<span>2</span>',
        );

        //we mock the CachedDatasetMainSection
        $cachedDatasetMainSection = $this->getMockBuilder(CachedDatasetMainSection::class)
                         ->setMethods(['getCitationsLinks'])
                         ->disableOriginalConstructor() //so we dont have to pass doi and db_connection
                         ->getMock();

        //we expect a call to getCitationsLinks
        $cachedDatasetMainSection->expects($this->once())
                 ->method('getCitationsLinks')
                 ->willReturn($source);

        $daoUnderTest = new FormattedDatasetMainSection(
            $cachedDatasetMainSection
        );
        $this->assertCount(count($expected), $daoUnderTest->getCitationsLinks());
    }

    /**
     * unit tests for formatting of the keywords associated to a dataset for use in the view template
     *
     */
    public function testFormattedReturnsKeywords()
    {
        //we mock the CachedDatasetMainSection
        $cachedDatasetMainSection = $this->getMockBuilder(CachedDatasetMainSection::class)
                         ->setMethods(['getKeywords'])
                         ->disableOriginalConstructor() //so we dont have to pass doi and db_connection
                         ->getMock();

        //we expect a call to getKeywords
        $cachedDatasetMainSection->expects($this->once())
                 ->method('getKeywords')
                 ->willReturn(array("am", "gram"));

        $expected = array(
            "<a href='/search/new?keyword=am'>am</a>",
            "<a href='/search/new?keyword=gram'>gram</a>",
        );
        $daoUnderTest = new FormattedDatasetMainSection(
            $cachedDatasetMainSection
        );
        $this->assertEquals($expected, $daoUnderTest->getKeywords()) ;
    }
}
