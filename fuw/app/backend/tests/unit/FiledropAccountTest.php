<?php

namespace backend\tests;

use backend\models\FiledropAccount;

class FiledropAccountTest extends \Codeception\Test\Unit
{
    /**
     * @var \backend\tests\UnitTester
     */
    protected $tester;

    /**
     * @var \backend\models\FiledropAccount
     */
    protected $filedrop;

    protected function _before()
    {
        $this->cleanUpDirectories();
        $this->filedrop = new FiledropAccount();
    }

    protected function _after()
    {
        $this->cleanUpDirectories();
    }

    private function cleanUpDirectories()
    {
        if ( file_exists("/var/incoming/ftp/100001") ) {
            exec("rm -rf /var/incoming/ftp/100001");
        }

        if ( file_exists("/var/repo/100001") ) {
            exec ("rm -rf /var/repo/100001");
        }

        if ( file_exists("/var/private/100001") ) {
            exec("rm -rf /var/private/100001");
        }

    }
    /**
     * test FileDrop can create directory for file upload pipeline
     */
    public function testCanCreateWritableDirectories()
    {



        $this->assertFalse(file_exists("/var/incoming/ftp/100001"));
        $this->assertFalse(file_exists("/var/repo/100001"));
        $this->assertFalse(file_exists("/var/private/100001"));

        $result = $this->filedrop->createDirectories("100001");

        $this->assertTrue(file_exists("/var/incoming/ftp/100001"));
        $this->assertTrue(file_exists("/var/repo/100001"));
        $this->assertTrue(file_exists("/var/private/100001"));

        $this->assertEquals("0770", substr(sprintf('%o', fileperms('/var/incoming/ftp/100001')), -4) );
        $this->assertEquals("0755", substr(sprintf('%o', fileperms('/var/repo/100001')), -4) );
        $this->assertEquals("0750", substr(sprintf('%o', fileperms('/var/private/100001')), -4) );

        $this->assertTrue($result);

    }

    /**
     * test FileDrop can create create a token file
     */
    public function testCanCreateTokens()
    {
        $this->assertFalse(file_exists("/var/private/100001/token_file"));
        mkdir("/var/private/100001");
        chmod("/var/private/100001", 0770);

        $result1 = $this->filedrop->makeToken('100001','token_file');
        $this->assertTrue(file_exists("/var/private/100001/token_file"));
        $token1 = file("/var/private/100001/token_file");
        $this->assertEquals($token1[0],$token1[1]);

        $result2 = $this->filedrop->makeToken('100001','token_file');
        $this->assertTrue(file_exists("/var/private/100001/token_file"));
        $token2 = file("/var/private/100001/token_file");
        $this->assertEquals($token2[0],$token2[1]);

        $this->assertTrue($result1);
        $this->assertTrue($result2);
        $this->assertNotEquals($token1[0], $token2[0]);

    }

    /**
     * test can retrieve matching ftpd container
     */
    public function testCanFindMatchingContainer()
    {
        $containerPattern = "/ftpd_1/";
        $container = $this->filedrop->getContainer($containerPattern);
        $this->assertNotNull($container);
        $this->assertRegexp($containerPattern,$container->getNames()[0]);
    }

    /**
     * test null is returned when pattern don't match
     */
    public function testCannotFindContainer()
    {
        $containerPattern = "/foo_bar/";
        $container = $this->filedrop->getContainer($containerPattern);
        $this->assertNull($container);
    }

    /**
     * test null is returned when pattern match a forbidden container
     */
    public function testCannotSeeForbiddenContainer()
    {
        $containerPattern = "/console_1/";
        $container = $this->filedrop->getContainer($containerPattern);
        $this->assertNull($container);
    }

    /**
     * Test of factory function that make PostBody for Docker PHP
     */
    public function testCanMakePostBody()
    {
        $execConfig = $this->filedrop->makePostBodyFor("execConfig", ["foo", "bar"]);
        $this->assertNotNull($execConfig);
        $this->assertInstanceOf("\Docker\API\Model\ContainersIdExecPostBody", $execConfig);
        $this->assertEquals(["foo", "bar"], $execConfig->getCmd());

        $execStartConfig = $this->filedrop->makePostBodyFor("execStartConfig");
        $this->assertNotNull($execStartConfig);
        $this->assertInstanceOf("\Docker\API\Model\ExecIdStartPostBody", $execStartConfig);

        $nullResponse = $this->filedrop->makePostBodyFor("");
        $this->assertNull($nullResponse);
    }



}