<?php

namespace backend\tests;

use backend\models\MoveJob;
use \League\Flysystem\Filesystem as NativeFilesystem;


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
    }

    protected function _after()
    {
    }

    /**
     * Test that the worker call copy on Flysystem with the correct file paths
     *
     */
    public function testMoveJobSuccess()
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

        $mockNativeFilesystem->expects($this->once())
                ->method('copy')
                ->with($source, $dest)
                ->willReturn(true);

        $job->fs = new TestFilesystem(["adapter" => $mockLocal,
            "nativeFilesystem" => $mockNativeFilesystem,
            "path" => "/var"
        ]);
        $job->execute($mockQueue);
    }

    /**
     * Test that exception is thrown when source path not found
     *
     * @expectedException League\Flysystem\FileNotFoundException
     * @expectedExceptionMessage File not found at path: var/repo/000007/someFile.png
     */
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

        $job->execute($mockQueue);        
    }

}