<?php

namespace backend\tests;

use backend\models\MoveJob;
use common\models\Upload;
use common\fixtures\UploadFixture;
use common\fixtures\AttributeFixture;
use \League\Flysystem\Filesystem as NativeFilesystem;
use \app\models\UpdateGigaDBJob;
use \Yii;


/**
 * Test for yii2-queue job class (DTO) for moving files
 * 
 * TODO: very basic for now, just making sure it doesn't crash
 * until I figure out how to test queue job classes
 * 
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 **/
class MoveJobTest extends \Codeception\Test\Unit
{
    /**
     * @var \backend\tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->tester->haveFixtures([
            'uploads' => [
                'class' => UploadFixture::className(),
                'dataFile' => codecept_data_dir() . 'upload.php'
            ],
            'attributes' => [
                'class' => AttributeFixture::className(),
                'dataFile' => codecept_data_dir() . 'attribute.php'
            ],            
        ]);
    }

    protected function _after()
    {
    }

    /**
     * Test that the worker call copy on Flysystem with the correct file paths
     * and that the upload is updated with the appropriate status
     *
     */
    public function testMoveJobSuccess()
    {
        $mockQueue = $this->createMock(\yii\queue\Queue::class);
        $mockGigaDBQueue = $this->createMock(\yii\queue\Queue::class);
        $mockLocalFileSystem = $this->createMock(\creocoder\flysystem\LocalFilesystem::class);
        $mockLocal = $this->createMock(\League\Flysystem\Adapter\Local::class);
        $mockNativeFilesystem = $this->createMock(\League\Flysystem\Filesystem::class);
        $job = new MoveJob();
        $job->doi = "200001";
        $job->file = "084.fq";
        $job->filedrop = 100;
        $job->gigaDBQueue = $mockGigaDBQueue;
        $source = "/var/repo/200001/084.fq";
        $dest = "/var/ftp/public/200001/084.fq";

        $filesPublicUrl = Yii::$app->params['dataset_filedrop']["files_public_url"];

        $mockNativeFilesystem->expects($this->once())
                ->method('has')
                ->with($dest)
                ->willReturn(false);

        $mockNativeFilesystem->expects($this->once())
                ->method('copy')
                ->with($source, $dest)
                ->willReturn(true);

        $jobReceipt = 3;
        $mockGigaDBQueue->expects($this->once())
                ->method('push')
                ->with(
                    $this->callback(function($argument) {
                        return $argument->className()  === "app\models\UpdateGigaDBJob";
                    })
                )
                ->willReturn($jobReceipt);

        $job->fs = new TestFilesystem(["adapter" => $mockLocal,
            "nativeFilesystem" => $mockNativeFilesystem,
            "path" => "/var"
        ]);
        $result = $job->execute($mockQueue);
        $this->assertTrue($result);
        $this->tester->seeRecord('common\models\Upload', [
            'doi' => $job->doi, 
            'name' => $job->file, 
            'status' => Upload::STATUS_SYNCHRONIZED,
            'location' => "$filesPublicUrl/{$job->doi}/{$job->file}"
        ]);

    }


/**
     * Test that the worker call copy on Flysystem with the correct file paths
     * and that the upload is updated with the appropriate status
     * scenario where file already exists at destination, so should overwrite
     *
     */
    public function testMoveJobSuccessFileExists()
    {
        $mockQueue = $this->createMock(\yii\queue\Queue::class);
        $mockGigaDBQueue = $this->createMock(\yii\queue\Queue::class);
        $mockLocalFileSystem = $this->createMock(\creocoder\flysystem\LocalFilesystem::class);
        $mockLocal = $this->createMock(\League\Flysystem\Adapter\Local::class);
        $mockNativeFilesystem = $this->createMock(\League\Flysystem\Filesystem::class);
        $job = new MoveJob();
        $job->doi = "200001";
        $job->file = "084.fq";
        $job->filedrop = 100;
        $job->gigaDBQueue = $mockGigaDBQueue;
        $source = "/var/repo/200001/084.fq";
        $dest = "/var/ftp/public/200001/084.fq";


        $mockNativeFilesystem->expects($this->once())
                ->method('has')
                ->with($dest)
                ->willReturn(true);

        $mockNativeFilesystem->expects($this->once())
                ->method('rename')
                ->with($dest, 
                    $this->callback(function($subject) {
                        return 1 === preg_match('/\.todelete\.\d+$/', $subject);
                    })
                )
                ->willReturn(true);

        $mockNativeFilesystem->expects($this->once())
                ->method('copy')
                ->with($source, $dest)
                ->willReturn(true);

        $jobReceipt = 3;
        $mockGigaDBQueue->expects($this->once())
                ->method('push')
                ->with(
                    $this->callback(function($argument) {
                        return $argument->className()  === "app\models\UpdateGigaDBJob";
                    })
                )
                ->willReturn($jobReceipt);

        $job->fs = new TestFilesystem(["adapter" => $mockLocal,
            "nativeFilesystem" => $mockNativeFilesystem,
            "path" => "/var"
        ]);
        $result = $job->execute($mockQueue);
        $this->assertTrue($result);
        $this->tester->seeRecord('common\models\Upload', [
            'doi' => $job->doi, 
            'name' => $job->file, 
            'status' => Upload::STATUS_SYNCHRONIZED,
        ]);
    }

    /**
     * Test that the worker call copy on Flysystem with the correct file paths
     * and that the upload cannot be found
     *
     */
    public function testMoveJobUploadNotFoundFailure()
    {
        $mockQueue = $this->createMock(\yii\queue\Queue::class);
        $mockLocalFileSystem = $this->createMock(\creocoder\flysystem\LocalFilesystem::class);
        $mockLocal = $this->createMock(\League\Flysystem\Adapter\Local::class);
        $mockNativeFilesystem = $this->createMock(\League\Flysystem\Filesystem::class);
        $job = new MoveJob();
        $job->doi = "000009";
        $job->file = "084.fq";
        $source = "/var/repo/000009/084.fq";
        $dest = "/var/ftp/public/000009/084.fq";

        $mockNativeFilesystem->expects($this->once())
                ->method('copy')
                ->with($source, $dest)
                ->willReturn(true);

        $job->fs = new TestFilesystem(["adapter" => $mockLocal,
            "nativeFilesystem" => $mockNativeFilesystem,
            "path" => "/var"
        ]);
        $result = $job->execute($mockQueue);
        $this->assertFalse($result);
  
    }

    /**
     * Test that the worker call copy on Flysystem with the correct file paths
     * and copy failed and returned false
     *
     */
    public function testMoveJobCopyFailure()
    {
        $mockQueue = $this->createMock(\yii\queue\Queue::class);
        $mockLocalFileSystem = $this->createMock(\creocoder\flysystem\LocalFilesystem::class);
        $mockLocal = $this->createMock(\League\Flysystem\Adapter\Local::class);
        $mockNativeFilesystem = $this->createMock(\League\Flysystem\Filesystem::class);
        $job = new MoveJob();
        $job->doi = "000009";
        $job->file = "084.fq";
        $source = "/var/repo/000009/084.fq";
        $dest = "/var/ftp/public/000009/084.fq";

        $mockNativeFilesystem->expects($this->once())
                ->method('copy')
                ->with($source, $dest)
                ->willReturn(false);

        $job->fs = new TestFilesystem(["adapter" => $mockLocal,
            "nativeFilesystem" => $mockNativeFilesystem,
            "path" => "/var"
        ]);
        $result = $job->execute($mockQueue);
        $this->assertFalse($result);
  
    }


    public function testMoveJobThrowsFileNotFound()
    {
        $mockQueue = $this->createMock(\yii\queue\Queue::class);
        $mockLocalFileSystem = $this->createMock(\creocoder\flysystem\LocalFilesystem::class);
        $mockLocal = $this->createMock(\League\Flysystem\Adapter\Local::class);
        $mockNativeFilesystem = $this->createMock(\League\Flysystem\Filesystem::class);
        $job = new MoveJob();
        $job->doi = "000007";
        $job->file = "someFile.png";
        $source = "/var/repo/000007/someFile.png";
        $dest = "/var/ftp/public/000007/someFile.png";

        $this->expectException(\League\Flysystem\FileNotFoundException::class);
        $this->expectExceptionMessage("File not found at path: var/repo/000007/someFile.png");
        $job->execute($mockQueue);        
    }

    public function testMoveJobCreateJobToUpdateGigaDB()
    {
        $mockQueue = $this->createMock(\yii\queue\Queue::class);
        $moveJob = new MoveJob();
        $moveJob->doi = "200001";
        $moveJob->file = "084.fq";

        $updateJob = $moveJob->createUpdateGigaDBJob(Upload::findOne(2));
        $this->assertEquals($moveJob->doi, $updateJob->doi);
        $this->assertEquals($this->tester->grabFixture('uploads')[1]['size'], $updateJob->file['size']);
        $this->assertEquals($this->tester->grabFixture('uploads')[1]['status'], $updateJob->file['status']);
        $this->assertEquals($this->tester->grabFixture('uploads')[1]['location'], $updateJob->file['location']);
        $this->assertEquals($this->tester->grabFixture('uploads')[1]['extension'], $updateJob->file['extension']);
        $this->assertEquals($this->tester->grabFixture('uploads')[1]['datatype'], $updateJob->file['datatype']);
        $this->assertEquals(['sample-1','sample-2','sample-3'], $updateJob->sample_ids);
        $this->assertEquals(2, count($updateJob->file_attributes));
    }

}