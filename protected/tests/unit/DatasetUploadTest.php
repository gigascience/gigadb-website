<?php
/**
 * Unit tests for DatasetUpload
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class DatasetUploadTest extends CTestCase
{

	public function testGetDefaultUploadInstructions()
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

        $mockFiledropSrv->expects($this->once())
                 ->method('getAccount')
                 ->with($filedrop_id)
                 ->willReturn(array('title' => 'foo', 'status' => 'bar'))
                 ->willReturn($filedropAccountHash);


		$datasetUpload = new DatasetUpload($filedrop_id, $mockFiledropSrv, $config);

		$renderedInstructions = $datasetUpload->getDefaultUploadInstructions();
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

}
?>