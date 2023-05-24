<?php

use Codeception\Example;
use CUploadedFile;
use creocoder\flysystem\AwsS3Filesystem;
use League\Flysystem\AdapterInterface;
use Ramsey\Uuid\Uuid;

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
        $datasetUuid = Uuid::uuid4(); //random UUID
        $imageName = "bgi_logo_new.png";
        $tempName = __DIR__ . "/../_data/" . $imageName;
        $expectedOptions = [ 'visibility' => AdapterInterface::VISIBILITY_PUBLIC ];
        $expectedTargetLocationPattern = "/images\/datasets\/$datasetUuid\/$imageName/"; //use regex so test works on dev and CI environments

        $sut = new Image(); // System Under Test

        // Codeception's mocking mechanism is enough for this object as mocked functions are simple
        $mockDatasetImage = $this->make('CUploadedFile', [
            "getName" => function () use ($imageName) {
                return $imageName;
            },
            "getTempName" => function () use ($tempName) {
                return $tempName;
            }
            ]);

        // Need to resort to PHPUnit's MockBuilder as mocked function take arguments
        $mockStorageTarget = $this->getMockBuilder(creocoder\flysystem\AwsS3Filesystem::class)
            ->setMethods(["put"])
            ->disableOriginalConstructor() // otherwise, it will pester about missing configuration for key, secret and bucket
            ->getMock();
        $mockStorageTarget->expects($this->once())
            ->method("put")
            ->with($this->matchesRegularExpression($expectedTargetLocationPattern), file_get_contents($tempName), $expectedOptions)
            ->willReturn(true);

        $this->assertTrue($sut->write($mockStorageTarget, $datasetUuid, $mockDatasetImage));

        $this->assertEquals($imageName, $sut->location);
        $urlArray = parse_url($sut->url); // we compare by URL component because root directory varies with the environments
        $this->assertEquals("https", $urlArray["scheme"]);
        $this->assertEquals(Image::BUCKET, $urlArray["host"]);
        $this->assertRegExp($expectedTargetLocationPattern, $urlArray["path"]);
    }

    /**
     * Test writing image content to storage managed by Flysystem (unhappy path)
     * @return void
     */
    public function testWriteWithoutSuccess()
    {
        $datasetUuid = Uuid::uuid4();
        $imageName = "bgi_logo_new.png";
        $tempName = __DIR__ . "/../_data/" . $imageName;
        $expectedOptions = [ 'visibility' => AdapterInterface::VISIBILITY_PUBLIC ];
        $expectedTargetLocationPattern = "/images\/datasets\/$datasetUuid\/$imageName/"; //use regex so test works on dev and CI environments

        $sut = new Image(); // System Under Test

        // Codeception's mocking mechanism is enough for this object as mocked functions are simple
        $mockDatasetImage = $this->make('CUploadedFile', [
            "getName" => function () use ($imageName) {
                return $imageName;
            },
            "getTempName" => function () use ($tempName) {
                return $tempName;
            }
        ]);

        // Need to resort to PHPUnit's MockBuilder as mocked function take arguments
        $mockStorageTarget = $this->getMockBuilder(creocoder\flysystem\AwsS3Filesystem::class)
            ->setMethods(["put"])
            ->disableOriginalConstructor() // otherwise, it will pester about missing configuration for key, secret and bucket
            ->getMock();
        $mockStorageTarget->expects($this->once())
            ->method("put")
            ->with($this->matchesRegularExpression($expectedTargetLocationPattern), file_get_contents($tempName), $expectedOptions)
            ->willReturn(false);

        $this->assertFalse($sut->write($mockStorageTarget, $datasetUuid, $mockDatasetImage));
    }

    /**
     * Test method for checking URL validity
     *
     * @dataProvider urlsProvider
     */
    public function testCheckURL(?string $url, bool $expectedReturn)
    {
        $sut = new Image(); // System Under Test
        $sut->url = $url;
        $this->assertEquals($expectedReturn, $sut->isUrlValid());
    }

    /**
     * Test method for checking qeueing deletion
     *
     * @dataProvider urlsProvider
     */
    public function testQueueDeleteImage(?string $url, bool $expectedReturn)
    {

        $dbCommand = $this->make('CDbCommand', [
            "insert" => function () {
                return true;
            },
        ]);
        $dbConnection = $this->make('CDbConnection', [
            "createCommand" => function () use ($dbCommand) {
                return $dbCommand;
            },
        ]);
        $sut = new Image(); // System Under Test
        $sut->url = $url;
        $sut->save();
        $this->assertEquals(
            $expectedReturn,
            $sut->deleteFile($dbConnection)
        );
        if (true === $expectedReturn) {
            $this->assertTrue(empty($sut->url));
        } else {
            $this->assertEquals($url, $sut->url);
        }
    }

    public function urlsProvider()
    {
        return [
            "valid url" => [ "https://foo.bar", true, true],
            "invalid url (local path)" => [ "var/somepath", false],
            "invalid url (http)" => [ "http://foo.bar", false],
            "invalid url (empty string)" => [ "", false],
            "invalid url (null)" => [ null, false],
        ];
    }
}
