<?php
/**
 * Unit tests for DatasetUpload
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class DatasetUploadTest extends CTestCase
{

	public function testGetFiledropAccountDetails()
	{
		$config = [
			"ftpd_endpoint" => "localhost",
			"ftpd_port" => "9021",
			"template_path" => "/var/www/files/templates",
		];

		$filedrop_id = 1;

		$filedropAccountHash = array('upload_login' => 'uploader-232452',
					'upload_token' => '9ad4sf',
					'download_login' => 'downloader-286652',
					'download_token' => 'a97b3');

		$mockFiledropSrv = $this->createMock(FiledropService::class);

        $mockFiledropSrv->expects($this->once())
                 ->method('getAccount')
                 ->with($filedrop_id)
                 ->willReturn($filedropAccountHash);

		$datasetUpload = new DatasetUpload($filedrop_id, $mockFiledropSrv, $config);

		$datasetUpload->getFiledropAccountDetails();
	}

	public function testChangeUploadInstructions()
	{
		$config = [
			"ftpd_endpoint" => "localhost",
			"ftpd_port" => "9021",
			"template_path" => "/var/www/files/templates",
		];

		$filedrop_id = 1;
		$newInstructions = "Lorem ipsum";
		$mockFiledropSrv = $this->createMock(FiledropService::class);
        $mockFiledropSrv->expects($this->once())
                 ->method('saveInstructions')
                 ->with($filedrop_id, $newInstructions)
                 ->willReturn(true);

		$datasetUpload = new DatasetUpload($filedrop_id, $mockFiledropSrv, $config);

		$this->assertTrue( $datasetUpload->changeUploadInstructions($newInstructions) );

	}

	public function testSendUploadInstructions()
	{
		$config = [
			"ftpd_endpoint" => "localhost",
			"ftpd_port" => "9021",
			"template_path" => "/var/www/files/templates",
		];

		$filedrop_id = 1;

		$recipient =  "foo@bar.com" ;
		$subject = "Upload Instructions for the dataset of your GigaDB submission";
		$instructions = "Lorem Ipsum";
		$fakeResponse = ["id" => f2r3s, "instructions" => $instructions];

		$filedropAccountHash = array('upload_login' => 'uploader-232452',
					'upload_token' => '9ad4sf',
					'download_login' => 'downloader-286652',
					'download_token' => 'a97b3');

		$mockFiledropSrv = $this->createMock(FiledropService::class);
		$mockFiledropSrv->identifier = "100001";

        $mockFiledropSrv->expects($this->once())
                 ->method('emailInstructions')
                 ->with($filedrop_id, $recipient, $subject , $instructions)
                 ->willReturn($fakeResponse);

		$datasetUpload = new DatasetUpload($filedrop_id, $mockFiledropSrv, $config);

		$this->assertTrue( $datasetUpload->sendUploadInstructions($recipient, $subject, $instructions) );

	}

	public function testRenderUploadInstructions()
	{

		$config = [
			"ftpd_endpoint" => "localhost",
			"ftpd_port" => "9021",
			"template_path" => "/var/www/files/templates",
		];

		$filedrop_id = 1;

		// set mocks and expected behaviour

		$filedropAccountHash = array('upload_login' => 'uploader-232452',
							'upload_token' => '9ad4sf',
							'download_login' => 'downloader-286652',
							'download_token' => 'a97b3');

		$mockDatasetDAO = $this->createMock(DatasetDAO::class);
		$mockFiledropSrv = $this->createMock(FiledropService::class);

        $mockDatasetDAO->expects($this->once())
                 ->method('getTitleAndStatus')
                 ->willReturn(array('title' => 'foo', 'status' => 'bar'));

        $mockFiledropSrv->dataset = $mockDatasetDAO;


		$datasetUpload = new DatasetUpload($filedrop_id, $mockFiledropSrv, $config);

		$renderedInstructions = $datasetUpload->renderUploadInstructions($filedropAccountHash);
		$this->assertNotNull($renderedInstructions);

		//veriyfing that Twig has interpolated the template tags with the variables
		$this->assertTrue(1 == preg_match('/foo/', $renderedInstructions));
		$this->assertTrue(1 == preg_match('/bar/', $renderedInstructions));
		$this->assertTrue(1 == preg_match('/host: localhost/', $renderedInstructions));
		$this->assertTrue(1 == preg_match('/port: 9021/', $renderedInstructions));
		$this->assertTrue(1 == preg_match('/username: uploader-232452/', $renderedInstructions));
		$this->assertTrue(1 == preg_match('/password: 9ad4sf/', $renderedInstructions));

		// makes sure that unusued template tags don't get interpolated
		$this->assertTrue(0 == preg_match('/downloader-286652/', $renderedInstructions));
		$this->assertTrue(0 == preg_match('/a97b3/', $renderedInstructions));
	}

	public function testRenderCustomizedInstructions()
	{
		$config = [
			"ftpd_endpoint" => "localhost",
			"ftpd_port" => "9021",
			"template_path" => "/var/www/files/templates",
		];

		$filedrop_id = 1;

		// set mocks and expected behaviour

		$filedropAccountHash = array('upload_login' => 'uploader-232452',
							'upload_token' => '9ad4sf',
							'download_login' => 'downloader-286652',
							'download_token' => 'a97b3',
							'instructions' => 'custom instructions here');

		$mockDatasetDAO = $this->createMock(DatasetDAO::class);
		$mockFiledropSrv = $this->createMock(FiledropService::class);

        $mockDatasetDAO->expects($this->once())
                 ->method('getTitleAndStatus')
                 ->willReturn(array('title' => 'foo', 'status' => 'bar'));

        $mockFiledropSrv->dataset = $mockDatasetDAO;


		$datasetUpload = new DatasetUpload($filedrop_id, $mockFiledropSrv, $config);

		$renderedInstructions = $datasetUpload->renderUploadInstructions($filedropAccountHash);
		$this->assertNotNull($renderedInstructions);

		//veriyfing that Twig has NOT interpolated the template tags with the variables
		$this->assertTrue(1 == preg_match('/custom instructions here/', $renderedInstructions));
		$this->assertFalse(1 == preg_match('/foo/', $renderedInstructions));
		$this->assertFalse(1 == preg_match('/bar/', $renderedInstructions));
		$this->assertFalse(1 == preg_match('/host: localhost/', $renderedInstructions));
		$this->assertFalse(1 == preg_match('/port: 9021/', $renderedInstructions));
		$this->assertFalse(1 == preg_match('/username: uploader-232452/', $renderedInstructions));
		$this->assertFalse(1 == preg_match('/password: 9ad4sf/', $renderedInstructions));

		// makes sure that unusued template tags don't get interpolated
		$this->assertTrue(0 == preg_match('/downloader-286652/', $renderedInstructions));
		$this->assertTrue(0 == preg_match('/a97b3/', $renderedInstructions));
	}

}
?>