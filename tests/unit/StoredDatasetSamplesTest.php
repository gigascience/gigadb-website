<?php
/**
 * Unit tests for StoredDatasetSamples to retrieve from storage, the samples for associated dataset
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetSamplesTest extends CDbTestCase
{
	protected $fixtures=array( //careful, the order matters here because of foreign key constraints
        'species'=>'Species',
        'datasets'=>'Dataset',
        'attributes'=>'Attributes',
        'samples'=>'Sample',
        'dataset_samples'=>'DatasetSample',
        'sample_attribute'=>'SampleAttribute',
    );

	public function setUp()
	{
		// echo "doing parent setup".PHP_EOL;
		parent::setUp();
		// echo "done with parent setup".PHP_EOL;
	}

	public function tearDown()
	{
		// echo "doing parent tearDown".PHP_EOL;
		parent::tearDown();
		// echo "done with parent tearDown".PHP_EOL;
		// var_dump($this->file_samples);
		// $this->getFixtureManager()->truncateTable("file_sample");
		$this->getFixtureManager()->truncateTable("sample_attribute");
	}

	public function testStoredReturnsDatasetId()
	{
		$dataset_id = 1;

		$daoUnderTest = new StoredDatasetSamples($dataset_id,
								$this->getFixtureManager()->getDbConnection()
							);
		$this->assertEquals($dataset_id, $daoUnderTest->getDatasetId() ) ;
	}

	public function testStoredReturnsDatasetDOI()
	{
		$dataset_id = 1;
		$doi = 100243;
		$daoUnderTest = new StoredDatasetSamples($dataset_id,
								$this->getFixtureManager()->getDbConnection()
							);
		$this->assertEquals($doi, $daoUnderTest->getDatasetDOI() ) ;
	}

	public function testStoredReturnsPaginatedDatasetSamples()
	{
		$dataset_id = 1;

		$expected = array(
			array(
				'sample_id' => 1,
				'linkName' => 'Sample 1',
				'dataset_id' => 1,
				'species_id' => 1,
				'tax_id' => 9238,
				'common_name'=>'Adelie penguin',
				'scientific_name'=>'Pygoscelis adeliae',
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
				'common_name'=>'Foxtail millet',
				'scientific_name'=>'Setaria italica',
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
				'common_name'=>'Adelie penguin',
				'scientific_name'=>'Pygoscelis adeliae',
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

		$daoUnderTest = new StoredDatasetSamples($dataset_id,
								$this->getFixtureManager()->getDbConnection()
							);
        $this->assertEquals([$expected[1]], $daoUnderTest->getDatasetSamples(1,1)) ;
        $this->assertEquals([$expected[0]], $daoUnderTest->getDatasetSamples(1,0)) ;
        $this->assertEquals([$expected[2]], $daoUnderTest->getDatasetSamples(1,2)) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetSamples(3)) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetSamples("ALL",0)) ;
        $this->assertEquals($expected, $daoUnderTest->getDatasetSamples()) ;
	}

}

?>