<?php

namespace backend\tests;

use backend\models\FiledropAccount;
use backend\models\DockerManager;

use \Docker\API\Model\{
        IdResponse,
        ContainersIdExecPostBody,
        ExecIdStartPostBody,
        ContainerSummaryItem,
    } ;

use \Docker\Docker ;
use \Docker\Stream\DockerRawStream ;

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
     * test can remove directories
     */
    public function testCanRemoveDirectories()
    {
        exec("mkdir -p /var/incoming/ftp/dummydir/some-subdir");
        exec("mkdir -p /var/repo/dummydir/some-subdir");
        exec("mkdir -p /var/private/dummydir");

        $result = $this->filedrop->removeDirectories("dummydir");

        $this->assertNotTrue(file_exists("/var/incoming/ftp/dummydir/some-subdir"));
        $this->assertNotTrue(file_exists("/var/repo/dummydir/some-subdir"));
        $this->assertNotTrue(file_exists("/var/private/dummydir"));
    }

    /**
     * test remoDirectories perform no-op and return true if files to delete don't exist
     */
    public function testNoOpRemoveDirectories()
    {
        $result = $this->filedrop->removeDirectories("dummydir");
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
     * test sending  upload account creation to the ftpd container
     * This test is to specify the internal logic (behaviours), not end-to-end
     * end-to-end testing of the docker interaction will be done in functional tests
     */
    public function testCreateFTPAccount()
    {
        $uploaderCommandArray = ["bash","-c","/usr/bin/pure-pw useradd uploader-100001 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u uploader -d /home/uploader/100001  < /var/private/100001/uploader_token.txt"] ;

        $downloaderCommandArray = ["bash","-c","/usr/bin/pure-pw useradd downloader-100001 -f /etc/pure-ftpd/passwd/pureftpd.passwd -m -u downloader -d /home/downloader/100001  < /var/private/100001/downloader_token.txt"] ;

         $doi = "100001";

        $mockDockerManager = $this->getMockBuilder(\backend\models\DockerManager::class)
                    ->setMethods(['loadAndRunCommand'])
                    ->disableOriginalConstructor()
                    ->getMock();

        $mockDockerManager->expects($this->at(0))
                ->method('loadAndRunCommand')
                ->with(
                    $this->equalTo("ftpd"),
                    $this->equalTo($uploaderCommandArray)
                );

        $mockDockerManager->expects($this->at(1))
                ->method('loadAndRunCommand')
                ->with(
                    $this->equalTo("ftpd"),
                    $this->equalTo($downloaderCommandArray)
                );

        $response = $this->filedrop->createFTPAccount( $mockDockerManager, $doi );
    }

    /**
     * test sending account removal to the ftpd container
     * This test is to specify the internal logic (behaviours), not end-to-end
     * end-to-end testing of the docker interaction will be done in functional tests
     */
    public function testRemoveFTPAccount()
    {
        $uploaderCommandArray = ["bash","-c","/usr/bin/pure-pw userdel uploader-dummydoi -f /etc/pure-ftpd/passwd/pureftpd.passwd -m"] ;

        $downloaderCommandArray = ["bash","-c","/usr/bin/pure-pw userdel downloader-dummydoi -f /etc/pure-ftpd/passwd/pureftpd.passwd -m"] ;

         $doi = "dummydoi";

        $mockDockerManager = $this->getMockBuilder(\backend\models\DockerManager::class)
                    ->setMethods(['loadAndRunCommand'])
                    ->disableOriginalConstructor()
                    ->getMock();

        $mockDockerManager->expects($this->at(0))
                ->method('loadAndRunCommand')
                ->with(
                    $this->equalTo("ftpd"),
                    $this->equalTo($uploaderCommandArray)
                );

        $mockDockerManager->expects($this->at(1))
                ->method('loadAndRunCommand')
                ->with(
                    $this->equalTo("ftpd"),
                    $this->equalTo($downloaderCommandArray)
                );

        $response = $this->filedrop->removeFTPAccount( $mockDockerManager, $doi );
    }

    /**
     * test checkFTPAccount
     */
    public function testCheckFTPAccount()
    {
        $doi = "dummydoi";

        $checkCommandArray = ["bash","-c","cat /etc/pure-ftpd/passwd/pureftpd.passwd | grep $doi"] ;


        $mockDockerManager = $this->getMockBuilder(\backend\models\DockerManager::class)
                    ->setMethods(['loadAndRunCommand'])
                    ->disableOriginalConstructor()
                    ->getMock();

        $mockStream = $this->getMockBuilder(\Docker\Stream\DockerRawStream::class)
                    ->setMethods(['onStdout', 'wait'])
                    ->disableOriginalConstructor()
                    ->getMock();

        $mockDockerManager->expects($this->once())
                ->method('loadAndRunCommand')
                ->with(
                    $this->equalTo("ftpd"),
                    $this->equalTo($checkCommandArray)
                )
                ->willReturn($mockStream);

        $mockStream->expects($this->once())
                ->method('onStdout');

        $mockStream->expects($this->once())
                ->method('wait');

        $response = $this->filedrop->checkFTPAccount( $mockDockerManager, $doi );
    }

    /**
     * test than beforeValidate calls prepareAccount and createFTPAccount
     *
     */
    public function testBeforeValidateCallsAccountMakingFunction()
    {

        // create a stub for dockerManager
        $stubDockerManager = $this->createMock(DockerManager::class);

        // Creating a "partial mock" for FiledropAccount
        // so don't use disableOriginalConstructor() method as we need the real object
        // and don't add to setMethods that are the system under test
        // and only add those that specify expected behaviour
        $filedropAccount = $this->getMockBuilder(FiledropAccount::class)
                 ->setMethods(['getDOI','getDockerManager','prepareAccountSetFields', 'createFTPAccount', 'setStatus' ])
                 ->getMock();

        // preparation
        $doi = "100001";

        // expected behaviours
        $filedropAccount->expects($this->exactly(2))
                ->method('getDOI')
                ->willReturn($doi);

        $filedropAccount->expects($this->once())
                ->method('getDockerManager')
                ->willReturn($stubDockerManager);

        $filedropAccount->expects($this->once())
                ->method('prepareAccountSetFields')
                ->with(
                    $this->equalTo("$doi")
                )
                ->willReturn(true);

        $filedropAccount->expects($this->once())
                ->method('createFTPAccount')
                ->with(
                    $this->identicalTo($stubDockerManager),
                    $this->equalTo("$doi")
                )
                ->willReturn(true);

        $filedropAccount->expects($this->once())
                ->method('setStatus')
                ->with(
                    $this->equalTo("active")
                );

        $filedropAccount->setDockerManager($stubDockerManager);
        $response = $filedropAccount->beforeValidate();
        $this->assertTrue($response);
    }

    /**
     * test than beforeValidate calls prepareAccount and createFTPAccount
     *
     */
    public function testBeforeValidateCallsAccountMakingFunctionPrepsFails()
    {

        // create a stub for dockerManager
        $stubDockerManager = $this->createMock(DockerManager::class);

        // Creating a "partial mock" for FiledropAccount
        // so don't use disableOriginalConstructor() method as we need the real object
        // and don't add to setMethods that are the system under test
        // and only add those that specify expected behaviour
        $filedropAccount = $this->getMockBuilder(FiledropAccount::class)
                 ->setMethods(['getDOI','getDockerManager','prepareAccountSetFields', 'createFTPAccount', 'setStatus' ])
                 ->getMock();

        // preparation
        $doi = "100001";

        // expected behaviours
        $filedropAccount->expects($this->once())
                ->method('getDOI')
                ->willReturn($doi); // now invoked only once

        $filedropAccount->expects($this->never())
                ->method('getDockerManager')
                ->willReturn($stubDockerManager); // this should never be invoked

        $filedropAccount->expects($this->once())
                ->method('prepareAccountSetFields')
                ->with(
                    $this->equalTo("$doi")
                )
                ->willReturn(false);// let's make this one fail

        $filedropAccount->expects($this->never())
                ->method('createFTPAccount')
                ->with(
                    $this->identicalTo($stubDockerManager),
                    $this->equalTo("$doi")
                )
                ->willReturn(true); // this should never be invoked

        $filedropAccount->expects($this->never())
                ->method('setStatus')
                ->with(
                    $this->equalTo("active")
                ); // this should never be invoked

        $filedropAccount->setDockerManager($stubDockerManager);
        $response = $filedropAccount->beforeValidate();
        $this->assertFalse($response);
    }

    /**
     * test than beforeValidate calls prepareAccount and createFTPAccount
     *
     */
    public function testBeforeValidateCallsAccountMakingFunctionFTPdFails()
    {

        // create a stub for dockerManager
        $stubDockerManager = $this->createMock(DockerManager::class);

        // Creating a "partial mock" for FiledropAccount
        // so don't use disableOriginalConstructor() method as we need the real object
        // and don't add to setMethods that are the system under test
        // and only add those that specify expected behaviour
        $filedropAccount = $this->getMockBuilder(FiledropAccount::class)
                 ->setMethods(['getDOI','getDockerManager','prepareAccountSetFields', 'createFTPAccount', 'setStatus' ])
                 ->getMock();

        // preparation
        $doi = "100001";

        // expected behaviours
        $filedropAccount->expects($this->exactly(2))
                ->method('getDOI')
                ->willReturn($doi);

        $filedropAccount->expects($this->once())
                ->method('getDockerManager')
                ->willReturn($stubDockerManager);

        $filedropAccount->expects($this->once())
                ->method('prepareAccountSetFields')
                ->with(
                    $this->equalTo("$doi")
                )
                ->willReturn(true);

        $filedropAccount->expects($this->once())
                ->method('createFTPAccount')
                ->with(
                    $this->identicalTo($stubDockerManager),
                    $this->equalTo("$doi")
                )
                ->willReturn(false); // let's make this one fail

        $filedropAccount->expects($this->never())
                ->method('setStatus')
                ->with(
                    $this->equalTo("active")
                ); // this should never be invoked

        $filedropAccount->setDockerManager($stubDockerManager);
        $response = $filedropAccount->beforeValidate();
        $this->assertFalse($response);
    }

}