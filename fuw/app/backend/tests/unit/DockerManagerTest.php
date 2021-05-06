<?php

namespace backend\tests;

use \Docker\Docker;

use backend\models\DockerManager;

class DockerManagerTest extends \Codeception\Test\Unit
{
    /**
     * @var \backend\tests\UnitTester
     */
    protected $tester;

    /**
     * @var \backend\models\DockerManager
     */
    protected $dockerManager;

    protected function _before()
    {
        $this->dockerManager = new DockerManager();
        $this->dockerManager->setClient(Docker::create());
    }

    protected function _after()
    {
    }

    /**
     * test can retrieve matching ftpd container
     */
    public function testCanFindMatchingContainer()
    {
        $containerPattern = "/ftpd_1/";
        $container = $this->dockerManager->getContainer($containerPattern);
        $this->assertNotNull($container);
        $this->assertRegexp($containerPattern,$container->getNames()[0]);
    }

    /**
     * test null is returned when pattern don't match
     */
    public function testCannotFindContainer()
    {
        $containerPattern = "/foo_bar/";
        $container = $this->dockerManager->getContainer($containerPattern);
        $this->assertNull($container);
    }

    /**
     * test null is returned when pattern match a forbidden container
     */
    public function testCannotSeeForbiddenContainer()
    {
        $containerPattern = "/console_1/";
        $container = $this->dockerManager->getContainer($containerPattern);
        $this->assertNull($container);
    }

    /**
     * Test of factory function that make PostBody for Docker PHP
     */
    public function testCanMakePostBody()
    {
        $execConfig = $this->dockerManager->makePostBodyFor("execConfig", ["foo", "bar"]);
        $this->assertNotNull($execConfig);
        $this->assertInstanceOf("\Docker\API\Model\ContainersIdExecPostBody", $execConfig);
        $this->assertEquals(["foo", "bar"], $execConfig->getCmd());

        $execStartConfig = $this->dockerManager->makePostBodyFor("execStartConfig");
        $this->assertNotNull($execStartConfig);
        $this->assertInstanceOf("\Docker\API\Model\ExecIdStartPostBody", $execStartConfig);

        $nullResponse = $this->dockerManager->makePostBodyFor("");
        $this->assertNull($nullResponse);
    }

    /**
     * Test it can load and run command on remote docker container
     */
    public function testCanLoadAndRunCommand()
    {
        // ------------------------- stubs configuration ----------------------

        // create a stub for retrieving id of configured exec resource
        $stubResponse = $this->createMock(\Docker\API\Model\IdResponse::class);
        $stubResponse->method('getId')
            ->willReturn("fdsf45dsf");

        // Create a stub for the container
        $stubContainer = $this->createMock(\Docker\API\Model\ContainerSummaryItem::class);
        $stubContainer->method('getId')
            ->willReturn("xhiauidnfa4");
        $stubContainer->method('getNames')
            ->willReturn(["/test_foobar_1"]);

        // ------------------------- mocks configuration ----------------------

        // mock Docker client to expect call to containerList, containerExec and ExecStart
        $mockDockerClient = $this->getMockBuilder(\Docker\Docker::class)
                    ->setMethods(['containerList','containerExec','execStart'])
                    ->disableOriginalConstructor()
                    ->getMock();

        $mockDockerClient->expects($this->once())
                ->method('containerList')
                ->willReturn([$stubContainer]);

        $mockDockerClient->expects($this->once())
                ->method('containerExec')
                ->with(
                    $this->isType("string"),
                    $this->isInstanceOf("\Docker\API\Model\ContainersIdExecPostBody")
                )
                ->willReturn($stubResponse);

        $mockDockerClient->expects($this->once())
                ->method('execStart')
                ->with(
                    $this->isType("string"),
                    $this->isInstanceOf("\Docker\API\Model\ExecIdStartPostBody")
                );

        // ------------------------- execute system under test ----------------------

        $this->dockerManager->setClient($mockDockerClient);
        $response = $this->dockerManager->loadAndRunCommand("foobar",["echo","hello world"]);
    }

    /**
     * Test it can load and run command on remote docker container
     */
    public function testCanRestartContainer()
    {
        // ------------------------- stubs configuration ----------------------

        // Create a stub for the container
        $stubContainer = $this->createMock(\Docker\API\Model\ContainerSummaryItem::class);
        $stubContainer->method('getId')
            ->willReturn("xhiauidnfa4");
        $stubContainer->method('getNames')
            ->willReturn(["/test_foobar_1"]);

        // ------------------------- mocks configuration ----------------------

        // mock Docker client to expect call to containerList, containerRestart
        $mockDockerClient = $this->getMockBuilder(\Docker\Docker::class)
                    ->setMethods(['containerList','containerRestart'])
                    ->disableOriginalConstructor()
                    ->getMock();


        $mockDockerClient->expects($this->once())
                ->method('containerList')
                ->willReturn([$stubContainer]);

        $mockDockerClient->expects($this->once())
                ->method('containerRestart')
                ->with(
                   "/test_foobar_1"
                );

        // ------------------------- execute system under test ----------------------

        $this->dockerManager->setClient($mockDockerClient);
        $this->dockerManager->restartContainer("foobar");
    }
}