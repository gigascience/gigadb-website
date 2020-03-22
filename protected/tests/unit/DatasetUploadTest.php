<?php

use \PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
/**
 * Unit tests for DatasetUpload
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class DatasetUploadTest extends CTestCase
{

	public function testSetStatusToDataAvailableForReview()
	{
		$config = [
			"sender" => "admin@gigadb.org",
			"recipient" => "editorial@gigadb.test",
			"template_path" => "/var/www/files/templates",
		];

		$content = "test content";

		$mockDatasetDAO = $this->createMock(DatasetDAO::class);
		$mockFileUploadSrv = $this->createMock(FileUploadService::class);

        $mockDatasetDAO->expects($this->once())
                 ->method('transitionStatus')
                 ->with("UserUploadingData", "DataAvailableForReview")
                 ->willReturn(true);

        $mockFileUploadSrv->expects($this->once())
                 ->method('emailSend')
                 ->with(
                 	$config["sender"], 
                 	"editorial@gigadb.test", 
                 	"Data available for review", 
                 	$content
                 )
                 ->willReturn(true);


		$datasetFileUpload = new DatasetUpload($mockDatasetDAO, $mockFileUploadSrv, $config);

		$result = $datasetFileUpload->setStatusToDataAvailableForReview($content);
	}

	public function testSetStatusToSubmitted()
	{
		$config = [
			"sender" => "admin@gigadb.org",
			"curators_email" => "database@gigadb.test",
			"template_path" => "/var/www/files/templates",
		];

		$content = "test content";

		$mockDatasetDAO = $this->createMock(DatasetDAO::class);
		$mockFileUploadSrv = $this->createMock(FileUploadService::class);

        $mockDatasetDAO->expects($this->once())
                 ->method('transitionStatus')
                 ->with("DataAvailableForReview", "Submitted")
                 ->willReturn(true);

        $mockDatasetDAO->expects($this->once())
                 ->method('getId')
                 ->willReturn(1);       

        $mockFileUploadSrv->expects($this->once())
                 ->method('emailSend')
                 ->with(
                 	$config["sender"], 
                 	"database@gigadb.test", 
                 	"Dataset has been submitted", 
                 	$content
                 )
                 ->willReturn(true);


		$datasetFileUpload = new DatasetUpload($mockDatasetDAO, $mockFileUploadSrv, $config);
		$nbItemsInCurationLog = CurationLog::Model()->count();
		$result = $datasetFileUpload->setStatusToSubmitted($content);
		$this->assertTrue($result);
		$this->assertEquals($nbItemsInCurationLog+1, CurationLog::Model()->count());
	}

	public function testRenderNotificationEmailBody()
	{

		$config = [
			"sender" => "admin@gigadb.org",
			"recipient" => "editorial@gigadb.test",
			"template_path" => "/var/www/files/templates",
		];

		$mockDatasetDAO = $this->createMock(DatasetDAO::class);
		$mockFileUploadSrv = $this->createMock(FileUploadService::class);

        $mockDatasetDAO->expects($this->once())
                 ->method('getIdentifier')
                 ->willReturn("003000");

		$datasetFileUpload = new DatasetUpload($mockDatasetDAO, $mockFileUploadSrv, $config);
		$renderedContent = $datasetFileUpload->renderNotificationEmailBody("DataAvailableForReview");
		$this->assertTrue(1 == preg_match('/dataset with DOI 003000/', $renderedContent));
	}

	/**
	 * test parsing metadata from spreadsheet
	 */
	public function testParseFromSpreadsheet()
	{
		$mockDatasetDAO = $this->createMock(DatasetDAO::class);
		$mockFileUploadSrv = $this->createMock(FileUploadService::class);

		$bo = new DatasetUpload($mockDatasetDAO, $mockFileUploadSrv,["spreadsheet_supported_format" => ["text/csv", "text/tab-separated-values"]]);
		list($metadata, $errors) = $bo->parseFromSpreadsheet("/var/www/files/examples/bulk-data-upload-example.csv");
		$this->assertEquals(2, count($metadata));
		$this->assertEquals(0, count($errors));
		$this->assertEquals("method.txt", $metadata[0]["name"]);
		$this->assertEquals("The methodology", $metadata[0]["description"]);
		$this->assertEquals("someFile.png", $metadata[1]["name"]);
		$this->assertEquals("That diagram", $metadata[1]["description"]);		
	}

	/**
	 * test can reconcile data from spreadsheet with uploads in database
	 */
	public function testReconcileSheetData()
	{
		$mockDatasetDAO = $this->createMock(DatasetDAO::class);
		$mockFileUploadSrv = $this->createMock(FileUploadService::class);

		$storedUploads = [
			[
				"id" => 23, 
				"doi" => "00000000", 
				"name" => "method.txt", 
				"description" => "", 
				"datatype" => "Text", 
				"extension" => "TEXT", 
				"size" => "", 
			],
			[ 
				"id" => 35,
				"doi" => "00000000", 
				"name" => "someFile.png", 
				"description" => "Some original description", 
				"datatype" => "Image", 
				"extension" => "PNG", 
				"size" => "345634",
			],
			[
				"id" => 47,
				"doi" => "00000000", 
				"name" => "foobar.PDF", 
				"description" => "", 
				"datatype" => "Rich Text", 
				"extension" => "PDF", 
				"size" => "", 
			],			
			
		];

		$attributesData = [
			[ 
				"name" => "Temperature", 
				"value" => "35", 
				"unit" => "Celsius", 
				"upload_id" =>35,
			]
		];
		$sheetData = [
			[ 
				"name" => "method.txt", 
				"description" => "The methodology", 
				"datatype" => "Readme", 
				"extension" => "TEXT", 
				"sampleId" => 342,
				"attr1" => null, 
				"attr2" => null, 
				"attr3" => null, 
				"attr4" => null, 
				"attr5" => null, 
			],
			[ 
				"name" => "someFile.png", 
				"description" => " That diagram", 
				"datatype" => "Annotation", 
				"extension" => "PNG", 
				"sampleId" => null, 
				"attr1" => " Rating::9::Some guys's scale", 
				"attr2" => null, 
				"attr3" => null, 
				"attr4" => null, 
				"attr5" => null, 
			],		
		];

		$bo = new DatasetUpload($mockDatasetDAO, $mockFileUploadSrv,[]);
		list($uploadData, $attributes, $errors) = $bo->mergeMetadata(
												$storedUploads, 
												$sheetData
										);
		// var_dump($uploadData);
		// var_dump($attributes);
		// var_dump($errors);
		$this->assertEquals(2, count($uploadData));
		$this->assertEquals(23, $uploadData[23]["id"]);
		$this->assertEquals("method.txt", $uploadData[23]["name"]);
		$this->assertEquals("The methodology", $uploadData[23]["description"]);
		$this->assertEquals(35, $uploadData[35]["id"]);
		$this->assertEquals("someFile.png", $uploadData[35]["name"]);
		$this->assertEquals("That diagram", $uploadData[35]["description"]);
		$this->assertEquals(1, count($attributes));
		$this->assertEquals("Rating", $attributes[0]["name"]);
		$this->assertEquals(9, $attributes[0]["value"]);
		$this->assertEquals("Some guys's scale", $attributes[0]["unit"]);
		$this->assertEquals(35, $attributes[0]["upload_id"]);
	}

	/**
	 * test can reconcile data from spreadsheet with uploads in database
	 */
	public function testReconcileSheetDataMalformedAttributes()
	{
		$mockDatasetDAO = $this->createMock(DatasetDAO::class);
		$mockFileUploadSrv = $this->createMock(FileUploadService::class);

		$storedUploads = [
			[
				"id" => 23, 
				"doi" => "00000000", 
				"name" => "method.txt", 
				"description" => "", 
				"datatype" => "Text", 
				"extension" => "TEXT", 
				"size" => "", 
			],
			[ 
				"id" => 35,
				"doi" => "00000000", 
				"name" => "someFile.png", 
				"description" => "Some original description", 
				"datatype" => "Image", 
				"extension" => "PNG", 
				"size" => "345634",
			],
			[
				"id" => 47,
				"doi" => "00000000", 
				"name" => "foobar.PDF", 
				"description" => "", 
				"datatype" => "Rich Text", 
				"extension" => "PDF", 
				"size" => "", 
			],			
			
		];

		$attributesData = [
			[ 
				"name" => "Temperature", 
				"value" => "35", 
				"unit" => "Celsius", 
				"upload_id" =>35,
			]
		];
		$sheetData = [
			[ 
				"name" => "method.txt", 
				"description" => "The methodology", 
				"datatype" => "Annotation", 
				"extension" => "text", 
				"sampleId" => 342,
				"attr1" => null, 
				"attr2" => null, 
				"attr3" => null, 
				"attr4" => null, 
				"attr5" => null, 
			],
			[ 
				"name" => "someFile.png", 
				"description" => " That diagram", 
				"datatype" => "Repeat sequence", 
				"extension" => "PNG", 
				"sampleId" => null, 
				"attr1" => " Rating:9::Some guys's scale", 
				"attr2" => null, 
				"attr3" => null, 
				"attr4" => null, 
				"attr5" => null, 
			],		
		];

		$bo = new DatasetUpload($mockDatasetDAO, $mockFileUploadSrv,[]);
		list($uploadData, $attributes, $errors) = $bo->mergeMetadata(
												$storedUploads, 
												$sheetData
										);
		// var_dump($uploadData);
		// var_dump($attributes);
		// var_dump($errors);
		$this->assertEquals(2, count($uploadData));
		$this->assertEquals(23, $uploadData[23]["id"]);
		$this->assertEquals("method.txt", $uploadData[23]["name"]);
		$this->assertEquals("The methodology", $uploadData[23]["description"]);
		$this->assertEquals(35, $uploadData[35]["id"]);
		$this->assertEquals("someFile.png", $uploadData[35]["name"]);
		$this->assertEquals("That diagram", $uploadData[35]["description"]);
		$this->assertTrue(empty($attributes));
		$this->assertEquals("(someFile.png) Malformed attribute: Rating:9::Some guys's scale", $errors[0]);
	}

	/**
	 * test parsing metadata from spreadsheet, detecting missing column
	 */
	public function testParseFromSpreadsheetMissingColumn()
	{
		// setup test data
		//File Name, Data Type, File Format, Description, Sample ID, Attribute 1, Attribute 2, Attribute 3, Attribute 4, Attribute 5
		$spreadsheet = new Spreadsheet();
		$spreadsheet->setActiveSheetIndex(0)
		    ->setCellValue('A1', 'File Name')
		    ->setCellValue('B1', 'Data Type')
		    ->setCellValue('C1', 'File Format')
		    ->setCellValue('D1', 'Dexcription')
		    ->setCellValue('E1', 'Sample ID')
		    ->setCellValue('F1', 'Attribute 1')
		    ->setCellValue('G1', 'Attribute 2')
		    ->setCellValue('H1', 'Attribute 3')
		    ->setCellValue('J1', 'Attribute 5')
		    ->setCellValue('A2', 'dummy.tiff')
		    ->setCellValue('B2', 'Script')
		    ->setCellValue('C2', 'TIFF')
		    ->setCellValue('D2', 'Some cool picture')
		    ->setCellValue('E2', '')
		    ->setCellValue('F2', '')
		    ->setCellValue('G2', '')
		    ->setCellValue('H2', '')
		    ->setCellValue('I2', '')
		    ->setCellValue('J2', '');
		$writer = new Csv($spreadsheet);
		$testFile = sys_get_temp_dir().Yii::$app->security->generateRandomString(4).".csv" ;
		$writer->save($testFile);

		$mockDatasetDAO = $this->createMock(DatasetDAO::class);
		$mockFileUploadSrv = $this->createMock(FileUploadService::class);

		$bo = new DatasetUpload($mockDatasetDAO, $mockFileUploadSrv,["spreadsheet_supported_format" => ["text/csv", "text/tab-separated-values"]]);
		list($metadata, $errors) = $bo->parseFromSpreadsheet("$testFile");
		$this->assertEquals(0, count($metadata));
		$this->assertEquals(1, count($errors));
		$this->assertEquals("Could not load spreadsheet, missing column(s): Description,Attribute 4", $errors[0]);

	}

	/**
	 * test parsing metadata from spreadsheet, detecting missing column
	 */
	public function testMergeFromSpreadsheetIncorrectDataType()
	{
				$mockDatasetDAO = $this->createMock(DatasetDAO::class);
		$mockFileUploadSrv = $this->createMock(FileUploadService::class);

		$storedUploads = [
			[
				"id" => 23, 
				"doi" => "00000000", 
				"name" => "method.txt", 
				"description" => "", 
				"datatype" => "Annotation", 
				"extension" => "txt", 
				"size" => "", 
			],
			[ 
				"id" => 35,
				"doi" => "00000000", 
				"name" => "someFile.csv", 
				"description" => "Some original description", 
				"datatype" => "Protein sequence", 
				"extension" => "CSV", 
				"size" => "345634",
			],
			[
				"id" => 47,
				"doi" => "00000000", 
				"name" => "foobar.PDF", 
				"description" => "", 
				"datatype" => "Script", 
				"extension" => "PDF", 
				"size" => "", 
			],			
			
		];

		$attributesData = [
			[ 
				"name" => "Temperature", 
				"value" => "35", 
				"unit" => "Celsius", 
				"upload_id" =>35,
			]
		];
		$sheetData = [
			[ 
				"name" => "method.txt", 
				"description" => "The methodology", 
				"datatype" => "Script", 
				"extension" => "text", 
				"sampleId" => 342,
				"attr1" => null, 
				"attr2" => null, 
				"attr3" => null, 
				"attr4" => null, 
				"attr5" => null, 
			],
			[ 
				"name" => "someFile.csv", 
				"description" => " That diagram", 
				"datatype" => "Repea sequence", 
				"extension" => "CSV", 
				"sampleId" => null, 
				"attr1" => " Rating::9::Some guys's scale", 
				"attr2" => null, 
				"attr3" => null, 
				"attr4" => null, 
				"attr5" => null, 
			],
			[ 
				"name" => "foobar.PDF", 
				"description" => "The methodology", 
				"datatype" => "Reap Me",
				"extension" => "text", 
				"sampleId" => null,
				"attr1" => null, 
				"attr2" => null, 
				"attr3" => null, 
				"attr4" => null, 
				"attr5" => null, 
			],	
		];

		$bo = new DatasetUpload($mockDatasetDAO, $mockFileUploadSrv,[]);
		list($uploadData, $attributes, $errors) = $bo->mergeMetadata(
												$storedUploads, 
												$sheetData
										);
		// var_dump($uploadData);
		// var_dump($attributes);
		// var_dump($errors);
		// echo $bo->getFiletypesJSON();
		$this->assertEquals(1, count($uploadData));
		$this->assertEquals(23, $uploadData[23]["id"]);
		$this->assertEquals("method.txt", $uploadData[23]["name"]);
		$this->assertEquals("The methodology", $uploadData[23]["description"]);
		$this->assertFalse(isset($uploadData[35]));
		$this->assertFalse(isset($uploadData[47]));
		$this->assertEquals("(someFile.csv) Cannot load file, incorrect Data type: Repea sequence", $errors[0]);
		$this->assertEquals("(foobar.PDF) Cannot load file, incorrect Data type: Reap Me", $errors[1]);

	}


/**
	 * test parsing metadata from spreadsheet, detecting missing column
	 */
	public function testMergeFromSpreadsheetIncorrectFileFormat()
	{
				$mockDatasetDAO = $this->createMock(DatasetDAO::class);
		$mockFileUploadSrv = $this->createMock(FileUploadService::class);

		$storedUploads = [
			[
				"id" => 23, 
				"doi" => "00000000", 
				"name" => "method.txt", 
				"description" => "", 
				"datatype" => "Annotation", 
				"extension" => "TEXT", 
				"size" => "", 
			],
			[ 
				"id" => 35,
				"doi" => "00000000", 
				"name" => "someFile.csv", 
				"description" => "Some original description", 
				"datatype" => "Repeat sequence", 
				"extension" => "CSV", 
				"size" => "345634",
			],
			[
				"id" => 47,
				"doi" => "00000000", 
				"name" => "foobar.PDF", 
				"description" => "", 
				"datatype" => "Script", 
				"extension" => "PDF", 
				"size" => "", 
			],			
			
		];

		$attributesData = [
			[ 
				"name" => "Temperature", 
				"value" => "35", 
				"unit" => "Celsius", 
				"upload_id" =>35,
			]
		];
		$sheetData = [
			[ 
				"name" => "method.txt", 
				"description" => "The methodology", 
				"datatype" => "Script", 
				"extension" => "TEXT", 
				"sampleId" => 342,
				"attr1" => null, 
				"attr2" => null, 
				"attr3" => null, 
				"attr4" => null, 
				"attr5" => null, 
			],
			[ 
				"name" => "someFile.csv", 
				"description" => " That diagram", 
				"datatype" => "Repeat sequence", 
				"extension" => "CXV", 
				"sampleId" => null, 
				"attr1" => " Rating::9::Some guys's scale", 
				"attr2" => null, 
				"attr3" => null, 
				"attr4" => null, 
				"attr5" => null, 
			],
			[ 
				"name" => "foobar.PDF", 
				"description" => "The methodology", 
				"datatype" => "Readme",
				"extension" => "PDG", 
				"sampleId" => null,
				"attr1" => null, 
				"attr2" => null, 
				"attr3" => null, 
				"attr4" => null, 
				"attr5" => null, 
			],	
		];

		$bo = new DatasetUpload($mockDatasetDAO, $mockFileUploadSrv,["spreadsheet_supported_format" => ["text/csv", "text/tab-separated-values"]]);
		list($uploadData, $attributes, $errors) = $bo->mergeMetadata(
												$storedUploads, 
												$sheetData
										);
		// var_dump($uploadData);
		// var_dump($attributes);
		// var_dump($errors);
		// echo $bo->getFiletypesJSON();
		$this->assertEquals(1, count($uploadData));
		$this->assertEquals(23, $uploadData[23]["id"]);
		$this->assertEquals("method.txt", $uploadData[23]["name"]);
		$this->assertEquals("The methodology", $uploadData[23]["description"]);
		$this->assertFalse(isset($uploadData[35]));
		$this->assertFalse(isset($uploadData[47]));
		$this->assertEquals("(someFile.csv) Cannot load file, incorrect File format: CXV", $errors[0]);
		$this->assertEquals("(foobar.PDF) Cannot load file, incorrect File format: PDG", $errors[1]);

	}

/**
	 * test parsing metadata from spreadsheet, support .TSV
	 */
	public function testParseFromSpreadsheetSupportTsv()
	{
		// setup test data
		//File Name, Data Type, File Format, Description, Sample ID, Attribute 1, Attribute 2, Attribute 3, Attribute 4, Attribute 5
		$spreadsheet = new Spreadsheet();
		$spreadsheet->setActiveSheetIndex(0)
		    ->setCellValue('A1', 'File Name')
		    ->setCellValue('B1', 'Data Type')
		    ->setCellValue('C1', 'File Format')
		    ->setCellValue('D1', 'Description')
		    ->setCellValue('E1', 'Sample ID')
		    ->setCellValue('F1', 'Attribute 1')
		    ->setCellValue('G1', 'Attribute 2')
		    ->setCellValue('H1', 'Attribute 3')
		    ->setCellValue('I1', 'Attribute 4')
		    ->setCellValue('J1', 'Attribute 5')
		    ->setCellValue('A2', 'dummy.gff')
		    ->setCellValue('B2', 'Script')
		    ->setCellValue('C2', 'GFF')
		    ->setCellValue('D2', 'Latest part')
		    ->setCellValue('E2', '')
		    ->setCellValue('F2', '')
		    ->setCellValue('G2', '')
		    ->setCellValue('H2', '')
		    ->setCellValue('I2', '')
		    ->setCellValue('J2', '');
		$writer = new Csv($spreadsheet);
		$writer->setDelimiter("\t"); // make it a .TSV file
		$testFile = sys_get_temp_dir().Yii::$app->security->generateRandomString(4).".tsv" ;
		$writer->save($testFile);

		$mockDatasetDAO = $this->createMock(DatasetDAO::class);
		$mockFileUploadSrv = $this->createMock(FileUploadService::class);

		$bo = new DatasetUpload($mockDatasetDAO, $mockFileUploadSrv,["spreadsheet_supported_format" => ["text/csv", "text/tab-separated-values"]]);
		list($metadata, $errors) = $bo->parseFromSpreadsheet("$testFile");
		$this->assertEquals(1, count($metadata));
		$this->assertEquals(0, count($errors));

	}
}
?>