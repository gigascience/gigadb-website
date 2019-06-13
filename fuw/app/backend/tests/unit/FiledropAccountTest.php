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

        $this->filedrop->createDirectories("100001");

        $this->assertTrue(file_exists("/var/incoming/ftp/100001"));
        $this->assertTrue(file_exists("/var/repo/100001"));
        $this->assertTrue(file_exists("/var/private/100001"));

        $this->assertEquals("0770", substr(sprintf('%o', fileperms('/var/incoming/ftp/100001')), -4) );
        $this->assertEquals("0755", substr(sprintf('%o', fileperms('/var/repo/100001')), -4) );
        $this->assertEquals("0750", substr(sprintf('%o', fileperms('/var/private/100001')), -4) );

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
        $token1 = file_get_contents("/var/private/100001/token_file");

        $result2 = $this->filedrop->makeToken('100001','token_file');
        $this->assertTrue(file_exists("/var/private/100001/token_file"));
        $token2 = file_get_contents("/var/private/100001/token_file");

        $this->assertTrue($result1);
        $this->assertTrue($result2);
        $this->assertNotEquals($token1, $token2);

    }

}