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
        $this->assertEquals("ftp://downloader-300001:foobar@gigadb.org:9021/somefile.fq", $link);
    }

    // tests for creating and saving the upload object

    public function testCreateUploadWithSuccess()
    {
        $metadata = [
              'ID' => '8cd11d9b349dbf7d4539d25a2af03fe2',
              'Size' => 117,
              'SizeIsDeferred' => false,
              'Offset' => 0,
              'MetaData' => [
                  'checksum' => '58e51b8d263ca3e89712c65c4485a8c9',
                  'dataset' => '300001',
                  'filename' => 'seq1.fq',
                  'filetype' => 'text/plain',
                  'name' => 'seq1.fa',
                  'relativePath' => 'null',
                  'type' => 'text/plain',
              ],
              'IsPartial' => false,
              'IsFinal' => false,
              'PartialUploads' => null,
          ];
        $doi = "300001";
        $filedropAccountId =1 ;
        $datafeedPath = codecept_data_dir();
        $tokenPath = codecept_data_dir();
        $tusd = new UploadFactory($doi,$datafeedPath,$tokenPath);
        $mockUpload = $this->createMock(\common\models\Upload::class);

        $mockUpload->expects($this->once())
            ->method("save")
            ->willReturn(true);

        $outcome = $tusd->createUpload($mockUpload, $metadata, $filedropAccountId);
        $this->assertTrue($outcome);
    }

   public function testCreateUploadWithFailure()
    {
        $metadata = [
              'ID' => '8cd11d9b349dbf7d4539d25a2af03fe2',
              'Size' => 117,
              'SizeIsDeferred' => false,
              'Offset' => 0,
              'MetaData' => [
                  'checksum' => '58e51b8d263ca3e89712c65c4485a8c9',
                  'dataset' => '300001',
                  'filename' => 'seq1.fq',
                  'filetype' => 'text/plain',
                  'name' => 'seq1.fa',
                  'relativePath' => 'null',
                  'type' => 'text/plain',
              ],
              'IsPartial' => false,
              'IsFinal' => false,
              'PartialUploads' => null,
          ];
        $doi = "300001";
        $filedropAccountId =1 ;
        $datafeedPath = codecept_data_dir();
        $tokenPath = codecept_data_dir();
        $tusd = new UploadFactory($doi,$datafeedPath,$tokenPath);
        $mockUpload = $this->createMock(\common\models\Upload::class);

        $mockUpload->expects($this->once())
            ->method("save")
            ->willReturn(false);

        $outcome = $tusd->createUpload($mockUpload, $metadata, $filedropAccountId);
        $this->assertFalse($outcome);
    }



}