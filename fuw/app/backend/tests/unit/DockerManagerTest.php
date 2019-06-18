<?php

namespace backend\tests;

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
}