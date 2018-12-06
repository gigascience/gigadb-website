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

	public function testStoredReturnsDatasetId()
	{
		$dataset_id = 1;
		$daoUnderTest = new StoredDatasetMainSection($dataset_id,  $this->getFixtureManager()->getDbConnection());
		$this->assertEquals($dataset_id, $daoUnderTest->getDatasetId() ) ;
	}

	public function testStoredReturnsDatasetDOI()
	{
		$dataset_id = 1;
		$doi = 100243;
		$daoUnderTest = new StoredDatasetMainSection($dataset_id,  $this->getFixtureManager()->getDbConnection());
		$this->assertEquals($doi, $daoUnderTest->getDatasetDOI() ) ;
	}

	public function testStoredReturnsHeadline()
	{
		// normal path
		$dataset_id = 1;
		$daoUnderTest = new StoredDatasetMainSection($dataset_id,  $this->getFixtureManager()->getDbConnection());
		$expected = array(
						"title" => 'Supporting data for "Analyzing climate variations on multiple timescales can guide Zika virus response measures"',
						"types" => array(
							"Genomic",
							"Workflow"
						),
						"release_date"=> '2018-08-23',
					);

		$this->assertEquals($expected, $daoUnderTest->getHeadline());

		// no result from database
		$dataset_id = 567;
		$daoUnderTest = new StoredDatasetMainSection($dataset_id,  $this->getFixtureManager()->getDbConnection());
		$expected = [];

		$this->assertEquals($expected, $daoUnderTest->getHeadline());
	}


	public function testStoredReturnsReleaseDetails()
	{
		$dataset_id = 1;
		$daoUnderTest = new StoredDatasetMainSection($dataset_id,  $this->getFixtureManager()->getDbConnection());
		$expected = array(
						"authors" => array( //remember authors must be sorted alphabetically on the main section body
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

		// no result from database
		$dataset_id = 567;
		$daoUnderTest = new StoredDatasetMainSection($dataset_id,  $this->getFixtureManager()->getDbConnection());
		$expected = [];

		$this->assertEquals($expected, $daoUnderTest->getReleaseDetails());
	}

	public function testStoredReturnsDescription()
	{
		$dataset_id = 1;
		$daoUnderTest = new StoredDatasetMainSection($dataset_id,  $this->getFixtureManager()->getDbConnection());
		$expected = array(
						"description" => 'The emergence of Zika virus (ZIKV) as a public health emergency in Latin America and the Caribbean (LAC) occurred during a period of severe drought and unusually high temperatures. Speculation in the literature exists that these climate conditions were associated with the 2015/2016 El Niño event and/or climate change but to date no quantitative '
					);
		$this->assertEquals($expected, $daoUnderTest->getDescription());

		// no result from database
		$dataset_id = 567;
		$daoUnderTest = new StoredDatasetMainSection($dataset_id,  $this->getFixtureManager()->getDbConnection());
		$expected = [];

		$this->assertEquals($expected, $daoUnderTest->getDescription());
	}

	/**
	 * test that we can query the citations links template from the loca.php config and replace the variable with full DOI
	 *
	 * @dataProvider citationsQueriesExamples
	 */
	public function testStoredReturnsCitationsLinks($argument, $expected)
	{
		$dataset_id = 1;
		$daoUnderTest = new StoredDatasetMainSection($dataset_id,  $this->getFixtureManager()->getDbConnection());

        $this->assertEquals( $expected, $daoUnderTest->getCitationsLinks($argument));
	}

	public function citationsQueriesExamples()
	{
		return [
			"no_argument"=> [
				null,
				array(
		            'scholar_query' => 'http://scholar.google.com/scholar?q=10.5072/100243',
		            'ePMC_query' => "http://europepmc.org/search?scope=fulltext&query=(REF:'10.5072/100243')",
		        ),
	        ],
			"scholar_argument"=> [
				"scholar_query",
				array(
		            'scholar_query' => 'http://scholar.google.com/scholar?q=10.5072/100243',
		        ),
	        ],
			"ePMC_argument"=> [
				"ePMC_query",
				array(
		            'ePMC_query' => "http://europepmc.org/search?scope=fulltext&query=(REF:'10.5072/100243')",
		        ),
	        ],
		];
	}
}

?>