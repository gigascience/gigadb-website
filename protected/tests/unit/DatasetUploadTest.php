<?php

use \PhpOffice\PhpSpreadsheet\Reader\Csv;
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
	 * test parsing metadata spreadsheet
	 */
	public function testParseFromSpreadsheet()
	{
		$mockDatasetDAO = $this->createMock(DatasetDAO::class);
		$mockFileUploadSrv = $this->createMock(FileUploadService::class);

		$bo = new DatasetUpload($mockDatasetDAO, $mockFileUploadSrv,[]);
		list($metadata, $errors) = $bo->parseFromSpreadsheet("text/csv","/var/www/files/examples/bulk-data-upload-example.csv");
		$this->assertEquals(2, count($metadata));
		$this->assertEquals(0, count($errors));
		$this->assertEquals("method.txt", $metadata[0]["name"]);
		$this->assertEquals("The methodology", $metadata[0]["description"]);
		$this->assertEquals("someFile.png", $metadata[1]["name"]);
		$this->assertEquals("That diagram", $metadata[1]["description"]);		
	}

	

}
?>