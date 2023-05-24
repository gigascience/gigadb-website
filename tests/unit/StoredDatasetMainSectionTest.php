<?php

/**
 * Unit tests for CachedDatasetMainSection to retrieve from storage the main section of a dataset view page
 *
 * @see DatasetAccessionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetMainSectionTest extends CDbTestCase
{
    protected $fixtures = array( //careful, the order matters here because of foreign key constraints
        'publishers' => 'Publisher',
        'attribute' => 'Attribute',
        'datasets' => 'Dataset',
        'types' => 'Type',
        'dataset_types' => 'DatasetType',
        'authors' => 'Author',
        'dataset_author' => 'DatasetAuthor',
        'dataset_attriutes' => 'DatasetAttributes',
        'dataset_logs' => 'DatasetLog',
        'funder_name' => 'Funder',
        'dataset_funder' => 'DatasetFunder',
    );

    public function setUp()
    {
        parent::setUp();
    }

    public function testStoredReturnsDatasetId()
    {
        $dataset_id = 1;
        $daoUnderTest = new StoredDatasetMainSection($dataset_id, $this->getFixtureManager()->getDbConnection());
        $this->assertEquals($dataset_id, $daoUnderTest->getDatasetId()) ;
    }

    public function testStoredReturnsDatasetDOI()
    {
        $dataset_id = 1;
        $doi = 100243;
        $daoUnderTest = new StoredDatasetMainSection($dataset_id, $this->getFixtureManager()->getDbConnection());
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI()) ;
    }

    public function testStoredReturnsHeadline()
    {
        // normal path
        $dataset_id = 1;
        $daoUnderTest = new StoredDatasetMainSection($dataset_id, $this->getFixtureManager()->getDbConnection());
        $expected = array(
                        "title" => 'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
                        "types" => array(
                            "Genomic",
                            "Workflow"
                        ),
                        "release_date" => '2018-08-23',
                    );

        $this->assertEquals($expected, $daoUnderTest->getHeadline());

        // no result from database
        $dataset_id = 567;
        $daoUnderTest = new StoredDatasetMainSection($dataset_id, $this->getFixtureManager()->getDbConnection());
        $expected = [];

        $this->assertEquals($expected, $daoUnderTest->getHeadline());
    }


    public function testStoredReturnsReleaseDetails()
    {
        $dataset_id = 1;
        $daoUnderTest = new StoredDatasetMainSection($dataset_id, $this->getFixtureManager()->getDbConnection());
        $expected = array(
                        "authors" => array( //remember authors must be sorted alphabetically on the main section body
                            array(
                                'id' => 2,
                                'surname' => 'Montana,',
                                'first_name' => 'Carlos',
                                'middle_name' => 'Ábel G',
                                'custom_name' => null,
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
                    );
        $this->assertEquals($expected, $daoUnderTest->getReleaseDetails());

        // no result from database
        $dataset_id = 567;
        $daoUnderTest = new StoredDatasetMainSection($dataset_id, $this->getFixtureManager()->getDbConnection());
        $expected = [];

        $this->assertEquals($expected, $daoUnderTest->getReleaseDetails());
    }

    public function testStoredReturnsDescription()
    {
        $dataset_id = 1;
        $daoUnderTest = new StoredDatasetMainSection($dataset_id, $this->getFixtureManager()->getDbConnection());
        $expected = array(
                        "description" => 'The emergence of Zika virus (ZIKV) as a public health emergency in Latin America and the Caribbean (LAC) occurred during a period of severe drought and unusually high temperatures. Speculation in the literature exists that these climate conditions were associated with the 2015/2016 El Niño event and/or climate change but to date no quantitative '
                    );
        $this->assertEquals($expected, $daoUnderTest->getDescription());

        // no result from database
        $dataset_id = 567;
        $daoUnderTest = new StoredDatasetMainSection($dataset_id, $this->getFixtureManager()->getDbConnection());
        $expected = [];

        $this->assertEquals($expected, $daoUnderTest->getDescription());
    }

    /**
     * test that we can query the citations links template from the loca.php config and replace the variable with full DOI
     *
     */
    public function testStoredReturnsCitationsLinks()
    {
        $dataset_id = 1;
        $daoUnderTest = new StoredDatasetMainSection($dataset_id, $this->getFixtureManager()->getDbConnection());

        $expected = array(
            'services' => array(
                'scholar_query' => "View citations on Google Scholar",
                'ePMC_query' => "View citations on Europe PubMed Central",
                'dimension_query' => "View citations on Dimensions",
                ),
            'urls' => array(
                'scholar_query' => 'https://scholar.google.com/scholar?q=10.5072/100243',
                'ePMC_query' => "https://europepmc.org/search?scope=fulltext&query=(REF:%2710.5072/100243%27)",
                'dimension_query' => "https://app.dimensions.ai/discover/publication?search_text=10.5072/100243",
                ),
            'images' => array(
                'scholar_query' => '/images/google_scholar.png',
                'ePMC_query' => "/images/ePMC.jpg",
                'dimension_query' => "/images/dimensions.jpg",
            ),
        );
        $this->assertEquals($expected, $daoUnderTest->getCitationsLinks());
    }


    /**
     * unit test for fetching keywords associated with a dataset
     *
     */
    public function testStoredReturnsKeywords()
    {
        $dataset_id = 1;

        $expected  = array("am", "gram");
        $daoUnderTest = new StoredDatasetMainSection($dataset_id, $this->getFixtureManager()->getDbConnection());
        $this->assertEquals($expected, $daoUnderTest->getKeywords());
    }

    /**
     * unit test for fetching the history of changes made to the dataset
     *
     */
    public function testStoredReturnsHistory()
    {
        $dataset_id = 1;
        $expected = array(
            array(
                'id' => 1,
                'dataset_id' => 1,
                'message' => "Updated the title",
                'created_at' => "2015-10-13 23:41:38.899752",
                'model' => "dataset",
                'model_id' => 1,
                'url' => "",
            ),
            array(
                'id' => 2,
                'dataset_id' => 1,
                'message' => "File Tinamus_guttatus.fa.gz updated",
                'created_at' => "2015-10-12 16:16:37.09544",
                'model' => "File",
                'model_id' => 16945,
                'url' => "/adminFile/update/id/16945",
            ),
        );

        $daoUnderTest = new StoredDatasetMainSection($dataset_id, $this->getFixtureManager()->getDbConnection());
        $this->assertEquals($expected, $daoUnderTest->getHistory());
    }

    /**
     * unit test for fetching the funding data for the dataset
     *
     */
    public function testStoredReturnsFunding()
    {
        $dataset_id = 1;
        $expected = array(
            array(
                'id' => 1,
                'dataset_id' => 1,
                'funder_name' => "The Good",
                'grant_award' => "An award",
                'comments' => "A comment",
                'awardee' => "John Doe",
            ),
            array(
                'id' => 2,
                'dataset_id' => 1,
                'funder_name' => "The Charitable",
                'grant_award' => "Another award",
                'comments' => "Some comment",
                'awardee' => "The team",
            ),
        );

        $daoUnderTest = new StoredDatasetMainSection($dataset_id, $this->getFixtureManager()->getDbConnection());
        $this->assertEquals($expected, $daoUnderTest->getFunding());
    }

    public function citationsQueriesExamples()
    {
        return [
            "no_argument" => [
                null,
                array(
                    'scholar_query' => 'http://scholar.google.com/scholar?q=10.5072/100243',
                    'ePMC_query' => "http://europepmc.org/search?scope=fulltext&query=(REF:'10.5072/100243')",
                    'dimension_query' => "https://app.dimensions.ai/discover/publication?search_text=10.5072/100243",
                ),
            ],
            "scholar_argument" => [
                "scholar_query",
                array(
                    'scholar_query' => 'http://scholar.google.com/scholar?q=10.5072/100243',
                ),
            ],
            "ePMC_argument" => [
                "ePMC_query",
                array(
                    'ePMC_query' => "http://europepmc.org/search?scope=fulltext&query=(REF:'10.5072/100243')",
                ),
            ],
            "dimension_argument" => [
                "dimension_query",
                array(
                    'dimension_query' => "https://app.dimensions.ai/discover/publication?search_text=10.5072/100243",
                ),
            ],
        ];
    }
}
