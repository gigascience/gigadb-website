<?php

use CUploadedFile;
use creocoder\flysystem\AwsS3Filesystem;
use League\Flysystem\AdapterInterface;

class ImageTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    /**
     * Test writing image content to storage managed by Flysystem (happy path)
     * @return void
     */
    public function testWriteWithSuccess()
    {
        $imageName = "bgi_logo_new.png";
        $tempName = __DIR__ . "/../_data/" . $imageName;
        $expectedOptions = [ 'visibility' => AdapterInterface::VISIBILITY_PUBLIC ];
        $expectedTargetLocation = "dev/images/datasets/$imageName";
        $expectedImageLocation = "https://".Image::BUCKET."/$expectedTargetLocation";

        $sut = new Image(); // System Under Test

        // Codeception's mocking mechanism is enough for this object as mocked functions are simples
        $mockDatasetImage = $this->make('CUploadedFile',[
            "getName" => function () use ($imageName) { return $imageName;},
            "getTempName" => function () use ($tempName){ return $tempName;}
            ]);

        // Need to resort to PHPUnit's MockBuilder as mocked function take arguments
        $mockStorageTarget = $this->getMockBuilder(creocoder\flysystem\AwsS3Filesystem::class)
            ->setMethods(["put"])
            ->disableOriginalConstructor() // otherwise, it will pester about missing configuration for key, secret and bucket
            ->getMock();
        $mockStorageTarget->expects( $this->once())
            ->method("put")
            ->with($expectedTargetLocation, file_get_contents($tempName), $expectedOptions )
            ->willReturn(true);

        $this->assertTrue($sut->write($mockStorageTarget, $mockDatasetImage));
        $this->assertEquals($imageName, $sut->location);
        $this->assertEquals($expectedImageLocation, $sut->url);

    }

    /**
     * Test writing image content to storage managed by Flysystem (unhappy path)
     * @return void
     */
    public function testWriteWithoutSuccess()
    {
        $imageName = "bgi_logo_new.png";
        $tempName = __DIR__ . "/../_data/" . $imageName;
        $expectedOptions = [ 'visibility' => AdapterInterface::VISIBILITY_PUBLIC ];
        $expectedTargetLocation = "dev/images/datasets/$imageName";

        $sut = new Image(); // System Under Test

        // Codeception's mocking mechanism is enough for this object as mocked functions are simples
        $mockDatasetImage = $this->make('CUploadedFile',[
            "getName" => function () use ($imageName) { return $imageName;},
            "getTempName" => function () use ($tempName){ return $tempName;}
        ]);

        // Need to resort to PHPUnit's MockBuilder as mocked function take arguments
        $mockStorageTarget = $this->getMockBuilder(creocoder\flysystem\AwsS3Filesystem::class)
            ->setMethods(["put"])
            ->disableOriginalConstructor() // otherwise, it will pester about missing configuration for key, secret and bucket
            ->getMock();
        $mockStorageTarget->expects( $this->once())
            ->method("put")
            ->with($expectedTargetLocation, file_get_contents($tempName), $expectedOptions )
            ->willReturn(false);

        $this->assertFalse($sut->write($mockStorageTarget, $mockDatasetImage));

    }

}