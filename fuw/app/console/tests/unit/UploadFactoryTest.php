<?php 

namespace console\tests;

use console\models\UploadFactory;

class UploadFactoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \console\tests\UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests for retrieving file format

    public function testGetFileFormatFromFileCommon()
    {
        $doi = "100009";
        $datafeedPath = codecept_data_dir();
        $tokenPath = codecept_data_dir();
        $tusd = new UploadFactory($doi,$datafeedPath,$tokenPath);
        


        $format = $tusd->getFileFormatFromFile("foobar.fq");
        $this->assertEquals("FASTQ", $format);
    }


    public function testGetFileFormatFromFileRegular()
    {
        $doi = "100009";
        $datafeedPath = codecept_data_dir();
        $tokenPath = codecept_data_dir();
        $tusd = new UploadFactory($doi,$datafeedPath,$tokenPath);
        
        $format = $tusd->getFileFormatFromFile("foobar.csv");
        $this->assertEquals("CSV", $format);
    }

    public function testGetFileFormatFromUnknown()
    {
        $doi = "100009";
        $datafeedPath = codecept_data_dir();
        $tokenPath = codecept_data_dir();
        $tusd = new UploadFactory($doi,$datafeedPath,$tokenPath);
        
        $format = $tusd->getFileFormatFromFile("foobar.boom");
        $this->assertEquals("UNKNOWN", $format);
    }

    // tests for constructing FTP link

    public function testGenerateFTPLink()
    {
        $doi = "300001";
        $datafeedPath = codecept_data_dir();
        $tokenPath = codecept_data_dir();
        $tusd = new UploadFactory($doi,$datafeedPath,$tokenPath);
        
        $link = $tusd->generateFTPLink("somefile.fq");
        $this->assertEquals("ftp://downloader-300001:foobar@gigadb.gigasciencejournal.com:9021/somefile.fq", $link);
    }

    public function testCreateUploadFromFileWithSuccess()
    {

        $doi = "300001";
        $filedropAccountId =1 ;
        $datafeedPath = codecept_data_dir();
        $tokenPath = codecept_data_dir();
        $inputFileArray = ["doi" => $doi, "path" => codecept_data_dir()."ftp/$doi", "name" => "seq1.fa"];
        $tusd = new UploadFactory($doi,$datafeedPath,$tokenPath);
        $mockUpload = $this->createMock(\common\models\Upload::class);

        $mockUpload->expects($this->once())
            ->method("save")
            ->willReturn(true);

        $outcome = $tusd->createUploadFromFile($filedropAccountId, $inputFileArray, $mockUpload);
        $this->assertTrue($outcome);
    }

    public function testCreateUploadFromFileWithFailure()
    {

        $doi = "300001";
        $filedropAccountId =1 ;
        $datafeedPath = codecept_data_dir();
        $tokenPath = codecept_data_dir();
        $inputFileArray = ["doi" => $doi, "path" => codecept_data_dir()."ftp/$doi", "name" => "seq1.fa"];
        $tusd = new UploadFactory($doi,$datafeedPath,$tokenPath);
        $mockUpload = $this->createMock(\common\models\Upload::class);

        $mockUpload->expects($this->once())
            ->method("save")
            ->willReturn(false);

        $outcome = $tusd->createUploadFromFile($filedropAccountId, $inputFileArray, $mockUpload);
        $this->assertFalse($outcome);
    }

    public function testCreateUploadFromJSONWithSuccess()
    {

        $doi = "300001";
        $filedropAccountId =1 ;
        $datafeedPath = codecept_data_dir();
        $tokenPath = codecept_data_dir();
        $inputJSON = file_get_contents(codecept_data_dir()."tusd.info");
        $tusd = new UploadFactory($doi,$datafeedPath,$tokenPath);
        $mockUpload = $this->createMock(\common\models\Upload::class);

        $mockUpload->expects($this->once())
            ->method("save")
            ->willReturn(true);

        $outcome = $tusd->createUploadFromJSON($filedropAccountId, $inputJSON, $mockUpload);
        $this->assertTrue($outcome);
    }

    public function testCreateUploadFromJSONWithMalformedJSON()
    {

        $doi = "300001";
        $filedropAccountId =1 ;
        $datafeedPath = codecept_data_dir();
        $tokenPath = codecept_data_dir();
        $inputJSON = "{foo:bar}";
        $tusd = new UploadFactory($doi,$datafeedPath,$tokenPath);
        $mockUpload = $this->createMock(\common\models\Upload::class);

        $mockUpload->expects($this->never())
            ->method("save")
            ->willReturn(true);

        $outcome = $tusd->createUploadFromJSON($filedropAccountId, $inputJSON, $mockUpload);
        $this->assertFalse($outcome);
    }

    public function testCreateUploadFromJSONWithDOIMismatch()
    {

        $doi = "300001";
        $filedropAccountId =1 ;
        $datafeedPath = codecept_data_dir();
        $tokenPath = codecept_data_dir();
        $inputJSON = '{"MetaData": {"dataset": "300009"}}';
        $tusd = new UploadFactory($doi,$datafeedPath,$tokenPath);
        $mockUpload = $this->createMock(\common\models\Upload::class);

        $mockUpload->expects($this->never())
            ->method("save")
            ->willReturn(true);

        $outcome = $tusd->createUploadFromJSON($filedropAccountId, $inputJSON, $mockUpload);
        $this->assertFalse($outcome);
    }

    public function testCreateUploadFromJSONWithSaveFailure()
    {

        $doi = "300001";
        $filedropAccountId =1 ;
        $datafeedPath = codecept_data_dir();
        $tokenPath = codecept_data_dir();
        $inputJSON = file_get_contents(codecept_data_dir()."tusd.info");
        $tusd = new UploadFactory($doi,$datafeedPath,$tokenPath);
        $mockUpload = $this->createMock(\common\models\Upload::class);

        $mockUpload->expects($this->once())
            ->method("save")
            ->willReturn(false);

        $outcome = $tusd->createUploadFromJSON($filedropAccountId, $inputJSON, $mockUpload);
        $this->assertFalse($outcome);
    }

}