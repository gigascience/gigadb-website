<?php
/**
 * Unit tests for ResourcedDatasetFiles to retrieve from a REST API, the files for associated dataset
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class ResourcedDatasetFilesTest extends CDbTestCase
{
	protected $fixtures=array( //careful, the order matters here because of foreign key constraints
        'species'=>'Species',
        'datasets'=>'Dataset',
        'attributes'=>'Attribute',
        'file_formats'=>'FileFormat',
        'file_types'=>'FileType',
        'files'=>'File',
        'file_attributes'=>'FileAttributes',
        'samples'=>'Sample',
        'file_samples'=>'FileSample',
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
		$this->getFixtureManager()->truncateTable("file_sample");
		$this->getFixtureManager()->truncateTable("file_attributes");
	}

	public function testResourcedReturnsDatasetId()
	{
		$dataset_id = 1;
		$fuwClient = new FileUploadService();
		$daoUnderTest = new ResourcedDatasetFiles(
								$dataset_id,
								$this->getFixtureManager()->getDbConnection(),
								$fuwClient
							);
		$this->assertEquals($dataset_id, $daoUnderTest->getDatasetId() ) ;
	}

	public function testResourcedReturnsDatasetDOI()
	{
		$dataset_id = 1;
		$doi = 100243;
		$fuwClient = new FileUploadService();
		$daoUnderTest = new ResourcedDatasetFiles(
								$dataset_id,
								$this->getFixtureManager()->getDbConnection(),
								$fuwClient
							);
		$this->assertEquals($doi, $daoUnderTest->getDatasetDOI() ) ;
	}

	public function testResourcedReturnsDatasetFilesEmpty()
	{
		$dataset_id = 1;
		$doi = 100243;

		$fuwClient = $this->createMock(FileUploadService::class);
		$daoUnderTest = new ResourcedDatasetFiles(
								$dataset_id,
								$this->getFixtureManager()->getDbConnection(),
								$fuwClient
							);
		$fuwClient->expects($this->once())
			->method("getUploads")
			->with($doi)
			->willReturn([]);
		$this->assertEquals([], $daoUnderTest->getDatasetFiles() ) ;
	}

public function testResourcedReturnsDatasetFilesWithResults()
	{
		$dataset_id = 1;
		$doi = 100243;

		$uploadedFiles = [
		    [
		        'id' => 1,
		        'doi' => '010010',
		        'name' => 'FieldDataMethods.docx',
		        'size' => 2352636,
		        'status' => 0,
		        'location' => 'ftp://some.location/FieldDataMethods.docx',
		        'initial_md5' => 't5GU9NwpuGYSfb7FEZMAxqtuz2PkEvv',
		        'description' => 'methods for field data',
		        'datatype' => 'Text',
		        'extension' => 'DOCX',
		        'sample_id' => null,
		        'updated_at' => (new DateTime())->format('U'),
		    ],
		    [
		        'id' => 2,
		        'doi' => '010010',
		        'name' => 'Measurements.csv',
		        'size' => 3252654,
		        'status' => 0,
		        'location' => 'ftp://some.location/Measurements.csv',
		        'initial_md5' => 'X5GU9NwpuGYSfb7FEZMAxqtuz2PkEvv',
		        'description' => 'measurements',
		        'datatype' => 'Text',
		        'extension' => 'CSV',
		        'sample_id' => null,
		        'updated_at' => (new DateTime())->format('U'),
		    ],
		    [
		        'id' => 3,
		        'doi' => '010020',
		        'name' => 'SomeImage.jpg',
		        'size' => 3252654,
		        'status' => 0,
		        'location' => 'ftp://some.location/SomeImage.jpg',
		        'initial_md5' => 'Y5GU9NwpuGYSfb7FEZMAxqtuz2PkEvv',
		        'description' => 'An image',
		        'datatype' => 'Annotation',
		        'extension' => 'JPG',
		        'sample_id' => null,
		        'updated_at' => (new DateTime())->format('U'),
		    ],
		];
		$fuwClient = $this->createMock(FileUploadService::class);
		$daoUnderTest = new ResourcedDatasetFiles(
								$dataset_id,
								$this->getFixtureManager()->getDbConnection(),
								$fuwClient
							);
		$fuwClient->expects($this->once())
			->method("getUploads")
			->with($doi)
			->willReturn($uploadedFiles);
		$datasetFiles = $daoUnderTest->getDatasetFiles();
		$this->assertEquals(count($uploadedFiles), count($datasetFiles) ) ;
		$this->assertEquals(null,$datasetFiles[0]["id"]);
		$this->assertEquals(1,$datasetFiles[0]["dataset_id"]);
		$this->assertEquals($uploadedFiles[0]["name"],$datasetFiles[0]["name"]);
		$this->assertEquals($uploadedFiles[0]["location"],$datasetFiles[0]["location"]);
		$this->assertEquals("docx",$datasetFiles[0]["extension"]);
		$this->assertEquals($uploadedFiles[0]["size"],$datasetFiles[0]["size"]);
		$this->assertEquals($uploadedFiles[0]["description"],$datasetFiles[0]["description"]);
		$this->assertEquals("DOCX",$datasetFiles[0]["format"]);
		$this->assertEquals("Text",$datasetFiles[0]["type"]);
		$this->assertNotNull($datasetFiles[0]["date_stamp"]);
		//test that the returned array is compatible with an array of File
		/* 'id' => 1,
		'dataset_id' => 1,
		'name' => "readme.txt",
		'location'=>'ftp://foo.bar',
		'extension'=>'txt',
		'size'=>'1322123045',
		'description'=>'just readme',
		'date_stamp' => '2015-10-12',
		'format_id' => 1,
		'type_id' => 1,
		'download_count'=>0,*/
		// -> the DOI needs to converted into dataset id
		// -> the datatype and extension need to be converted into database IDs
	}

	public function testResourcedReturnsDatasetFilesWithAttributes()
	{
		$dataset_id = 1;
		$doi = 100243;

		$uploadedFiles = [
		    [
		        'id' => 1,
		        'doi' => '010010',
		        'name' => 'FieldDataMethods.docx',
		        'size' => 2352636,
		        'status' => 0,
		        'location' => 'ftp://some.location/FieldDataMethods.docx',
		        'initial_md5' => 't5GU9NwpuGYSfb7FEZMAxqtuz2PkEvv',
		        'description' => 'methods for field data',
		        'datatype' => 'Text',
		        'extension' => 'DOCX',
		        'sample_id' => null,
		        'updated_at' => (new DateTime())->format('U'),
		    ],
		    [
		        'id' => 2,
		        'doi' => '010010',
		        'name' => 'Measurements.csv',
		        'size' => 3252654,
		        'status' => 0,
		        'location' => 'ftp://some.location/Measurements.csv',
		        'initial_md5' => 'X5GU9NwpuGYSfb7FEZMAxqtuz2PkEvv',
		        'description' => 'measurements',
		        'datatype' => 'Text',
		        'extension' => 'CSV',
		        'sample_id' => null,
		        'updated_at' => (new DateTime())->format('U'),
		    ],
		    [
		        'id' => 3,
		        'doi' => '010020',
		        'name' => 'SomeImage.jpg',
		        'size' => 3252654,
		        'status' => 0,
		        'location' => 'ftp://some.location/SomeImage.jpg',
		        'initial_md5' => 'Y5GU9NwpuGYSfb7FEZMAxqtuz2PkEvv',
		        'description' => 'An image',
		        'datatype' => 'Annotation',
		        'extension' => 'JPG',
		        'sample_id' => null,
		        'updated_at' => (new DateTime())->format('U'),
		    ],
		];

		$allAttributes = [
			1 => [
					[
			                'name' => "Attribute A",
			                'value' => "42",
			                'unit' => "Metre",
			                'upload_id' => 1,
			        ],
		          	[
			                'name' => "Attribute B",
			                'value' => "34",
			                'unit' => "Litre",
			                'upload_id' => 1,
			        ]
			    ],
          2 => [],
          3 =>	[
	          		[
                          'name' => "Attribute A",
                          'value' => "54",
                          'unit' => "Metre",
                          'upload_id' => 3,
                    ],
                ]
		];
		$fuwClient = $this->createMock(FileUploadService::class);
		$daoUnderTest = new ResourcedDatasetFiles(
								$dataset_id,
								$this->getFixtureManager()->getDbConnection(),
								$fuwClient
							);

		$fuwClient->expects($this->once())
			->method("getUploads")
			->with($doi)
			->willReturn($uploadedFiles);

		$fuwClient->expects($this->exactly(3))
			->method("getAttributes")
			->withConsecutive(
				[ 1 ],
				[ 2 ],
				[ 3 ]
			)
			->will($this->onConsecutiveCalls(
				$allAttributes[1],
				$allAttributes[2],
				$allAttributes[3]
			));


		$datasetFiles = $daoUnderTest->getDatasetFiles();
		$this->assertEquals(count($uploadedFiles), count($datasetFiles) ) ;
		$this->assertEquals(null,$datasetFiles[0]["id"]);
		$this->assertEquals(1,$datasetFiles[0]["dataset_id"]);
		$this->assertEquals($uploadedFiles[0]["name"],$datasetFiles[0]["name"]);
		$this->assertEquals($uploadedFiles[0]["location"],$datasetFiles[0]["location"]);
		$this->assertEquals("docx",$datasetFiles[0]["extension"]);
		$this->assertEquals($uploadedFiles[0]["size"],$datasetFiles[0]["size"]);
		$this->assertEquals($uploadedFiles[0]["description"],$datasetFiles[0]["description"]);
		$this->assertEquals("DOCX",$datasetFiles[0]["format"]);
		$this->assertEquals("Text",$datasetFiles[0]["type"]);
		$this->assertNotNull($datasetFiles[0]["date_stamp"]);
		// var_dump($datasetFiles[0]["file_attributes"]);
		$this->assertEquals(["Attribute A" => "42 Metre"],$datasetFiles[0]["file_attributes"][0]);
		$this->assertEquals(["Attribute B" => "34 Litre"],$datasetFiles[0]["file_attributes"][1]);
		$this->assertEquals([],$datasetFiles[1]["file_attributes"]);
		$this->assertEquals(["Attribute A" => "54 Metre"],$datasetFiles[2]["file_attributes"][0]);
	}

}

?>