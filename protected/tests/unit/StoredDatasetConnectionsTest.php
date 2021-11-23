<?php
/**
 * Unit tests for StoredDatasetConnections to retrieve from storage connected resources
 *
 * @see DatasetAccessionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetConnectionsTest extends CDbTestCase
{
	protected $fixtures=array( //careful, the order matters here because of foreign key constraints
        'publishers'=>'Publisher',
        'relationships'=>'Relationship',
        'datasets'=>'Dataset',
        'relations'=>'Relation',
        'manuscripts'=>'Manuscript',
        'projects'=>'Project',
        'dataset_projects'=>'DatasetProject',

    );

	public function setUp()
	{
		parent::setUp();
	}

	public function testStoredReturnsDatasetId()
	{
		$dataset_id = 1;

		//create a stub for the web client
		$webClient = $this->createMock(GuzzleHttp\Client::class);
		$daoUnderTest = new StoredDatasetConnections($dataset_id,
								$this->getFixtureManager()->getDbConnection(),
								$webClient
							);
		$this->assertEquals($dataset_id, $daoUnderTest->getDatasetId() ) ;
	}

	public function testStoredReturnsDatasetDOI()
	{
		$dataset_id = 1;
		$doi = 100243;
		//create a stub for the web client
		$webClient = $this->createMock(GuzzleHttp\Client::class);
		$daoUnderTest = new StoredDatasetConnections($dataset_id,
								$this->getFixtureManager()->getDbConnection(),
								$webClient
							);
		$daoUnderTest = new StoredDatasetConnections($dataset_id,
								$this->getFixtureManager()->getDbConnection(),
								$webClient);
		$this->assertEquals($doi, $daoUnderTest->getDatasetDOI() ) ;
	}

	public function testStoredReturnsRelations()
	{
		$dataset_id = 6;
		$expected = array(
			// array(
			// 	'dataset_id'=>3, // 101001
			// 	'related_id'=>4, // 101000
			// 	'relationship_id'=>3, //IsSupplementTo
			// ),
			// array(
			// 	'dataset_id'=>4, // 101000
			// 	'related_id'=>3, // 101001
			// 	'relationship_id'=>4, //IsSupplementedBy
			// ),
			// array(
			// 	'dataset_id'=>5, // 100038
			// 	'related_id'=>6, // 100044
			// 	'relationship_id'=>17, //IsCompiledBy
			// ),
			array(
				'dataset_id'=>6, // 100044
				'dataset_doi'=>"100044", // 100044
				'related_id'=>5, // 100038
				'related_doi'=>"100038", // 100038
				'relationship'=>"Compiles", //18 Compiles
			),
			array(
				'dataset_id'=>6, // 100044
				'dataset_doi'=>"100044", // 100044
				'related_id'=>7, // 100148
				'related_doi'=>"100148", // 100148
				'relationship'=>"IsPreviousVersionOf", //10 IsPreviousVersionOf
			),
			// array(
			// 	'dataset_id'=>7, // 100148
			// 	'related_id'=>6, // 100044
			// 	'relationship_id'=>9, //IsNewVersionOf
			// ),
		);

		//create a stub for the web client
		$webClient = $this->createMock(GuzzleHttp\Client::class);

		$daoUnderTest = new StoredDatasetConnections($dataset_id,
								$this->getFixtureManager()->getDbConnection(),
								$webClient
							);
		$this->assertEquals($expected, $daoUnderTest->getRelations());
		$this->assertEquals([$expected[1]], $daoUnderTest->getRelations("IsPreviousVersionOf"));
		$this->assertEquals([$expected[0]], $daoUnderTest->getRelations("Compiles"));
		$this->assertEquals([], $daoUnderTest->getRelations("DoesNotExistRelationship"));


	}

	public function testStoredReturnsPublications()
	{
		$dataset_id = 1;

		// we need to create a mock for a PSR-7 Http response returned by the http request to get citations
		$response = $this->getMockBuilder(GuzzleHttp\Psr7\Response::class)
						->setMethods(['getBody'])
						->disableOriginalConstructor()
						->getMock();

		$response->expects($this->exactly(2))
				->method('getBody')
				->will(
					$this->onConsecutiveCalls(
						"full citation fetched remotely. doi:10.1186/gb-2012-13-10-r100",
						"Another full citation fetched remotely. doi:10.1038/nature10158"
					));

		// create a mock for the HTTP request to dx.doi
		$webClient = $this->getMockBuilder(GuzzleHttp\Client::class)
						->setMethods(['request'])
						->disableOriginalConstructor()
						->getMock();

		// create the expectations for each call to dx.doi for citations
		$webClient->expects($this->exactly(2))
					->method('request')
					->withConsecutive(
						[
							'GET', 'https://doi.org/10.1186/gb-2012-13-10-r100', [
							    'headers' => [
							        'Accept' => 'text/x-bibliography',
							    ],
							    'connect_timeout' => 30
							]
						],
						[
							'GET', 'https://doi.org/10.1038/nature10158', [
							    'headers' => [
							        'Accept' => 'text/x-bibliography',
							    ],
							    'connect_timeout' => 30
							]
						]
					)
					->willReturn($response);


		$expected = array(
						array(
							'id' => 1,
							'identifier' => "10.1186/gb-2012-13-10-r100",
							'pmid' => 23075480,
							'dataset_id'=>1,
							'citation' => "full citation fetched remotely. doi:10.1186/gb-2012-13-10-r100",
							'pmurl' => "http://www.ncbi.nlm.nih.gov/pubmed/23075480",
						),
						array(
							'id' => 2,
							'identifier' => "10.1038/nature10158",
							'pmid' => null,
							'dataset_id'=>1,
							'citation' => "Another full citation fetched remotely. doi:10.1038/nature10158",
							'pmurl' => null,
						),
					);
		$daoUnderTest = new StoredDatasetConnections($dataset_id,
								$this->getFixtureManager()->getDbConnection(),
								$webClient
							);
		$this->assertEquals($expected, $daoUnderTest->getPublications());
	}

	public function testStoredReturnsPublicationsWithGatewayTimeoutError()
	{
		// Create mock handler with two 504 responses in its queue
		$mock = new GuzzleHttp\Handler\MockHandler([
			new GuzzleHttp\Psr7\Response(504, ['Foo' => 'Bar'], "Simulating first time out"),
			new GuzzleHttp\Psr7\Response(504, ['Fred' => 'Waldo'], "Simulating second time out error")
		]);
		$handler = GuzzleHttp\HandlerStack::create($mock);
		$webClient = new GuzzleHttp\Client(['handler' => $handler]);

		$dataset_id = 1;

		// Value for citation key pair is null if there is a gateway time out
		// error when trying to retrieve citation information using identifier
		$expected = array(
						array(
							'id' => 1,
							'identifier' => "10.1186/gb-2012-13-10-r100",
							'pmid' => 23075480,
							'dataset_id'=>1,
							'citation' => null,
							'pmurl' => "http://www.ncbi.nlm.nih.gov/pubmed/23075480",
						),
						array(
							'id' => 2,
							'identifier' => "10.1038/nature10158",
							'pmid' => null,
							'dataset_id'=>1,
							'citation' => null,
							'pmurl' => null,
						),
					);
		$daoUnderTest = new StoredDatasetConnections($dataset_id,
			$this->getFixtureManager()->getDbConnection(),
			$webClient
		);
		$this->assertEquals($expected, $daoUnderTest->getPublications(), "Array from getPublications() did not match contents expected by testStoredReturnsPublicationsWithGatewayTimeoutError()");
	}

	public function testStoredReturnsProjects()
	{
		$dataset_id = 1;
		$expected = array(
			array(
					'id'=>1,
					'url'=>"http://avian.genomics.cn/en/index.html",
					'name'=>"The Avian Phylogenomic Project",
					'image_location'=>"http://gigadb.org/images/project/phylogenomiclogo.png",
			),
			array(
					'id'=>2,
					'url'=>"http://www.genome10k.org/",
					'name'=>"Genome 10K",
					'image_location'=>null,
			),
		);

		//create a stub for the web client
		$webClient = $this->createMock(GuzzleHttp\Client::class);

		$daoUnderTest = new StoredDatasetConnections($dataset_id,
								$this->getFixtureManager()->getDbConnection(),
								$webClient
							);
		$this->assertEquals($expected, $daoUnderTest->getProjects());

		// testing it returns empty array if no result
		$daoUnderTestEmpty = new StoredDatasetConnections(2,
								$this->getFixtureManager()->getDbConnection(),
								$webClient
							);
		$this->assertEquals([], $daoUnderTestEmpty->getProjects());

	}
}
?>