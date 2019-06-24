<?php namespace backend\tests\functional;
use backend\tests\FunctionalTester;

use backend\models\FiledropAccount;
use backend\models\DockerManager;

class FiledropAccountCest
{
    public function _before(FunctionalTester $I)
    {
    	// make sure the ftpd container is reset
    }

    /**
     * @param FunctionalTester $I
     */
    public function createFTPAccounts(FunctionalTester $I)
    {
    	$filedrop = new FiledropAccount();
    	$dockerManager = new DockerManager();
    	$doi = FiledropAccount::generateRandomString(6);
    	$ftpServer = "ftpd";

    	// create directories
    	$filedrop->createDirectories("$doi");

    	// create tokens
		$result1 = $filedrop->makeToken("$doi",'uploader_token.txt');
		$result1 = $filedrop->makeToken("$doi",'downloader_token.txt');

		// derive logins and tokens
		$uploadLogin = "uploader-$doi";
		$uploadToken = rtrim(file("/var/private/$doi/uploader_token.txt")[0]);

		$downloadLogin = "downloader-$doi";
		$downloadToken = rtrim(file("/var/private/$doi/downloader_token.txt")[0]);

    	// in case no accounts
    	touch("/etc/pure-ftpd/passwd/pureftpd.passwd");

    	// create accounts
    	$status = $filedrop->createFTPAccount( $dockerManager, $doi );
    	$I->assertTrue($status);

    	// we should be able to connect. problem 2 is there is delay in filesytem sync
    	// Problem 1 is ftpd cannot be connected by ftp (because of PUBLICHOST value)
  //   	clearstatcache();
  //   	$password_file = file("/etc/pure-ftpd/passwd/pureftpd.passwd");
  //   	clearstatcache();
  //   	$password_file = file("/etc/pure-ftpd/passwd/pureftpd.passwd");
  //    	clearstatcache();
  //   	$password_file = file("/etc/pure-ftpd/passwd/pureftpd.passwd");
  //    	clearstatcache();
  //   	$password_file = file("/etc/pure-ftpd/passwd/pureftpd.passwd");
  //    	clearstatcache();
  //   	$password_file = file("/etc/pure-ftpd/passwd/pureftpd.passwd");
  //    	clearstatcache();
  //   	$password_file = file("/etc/pure-ftpd/passwd/pureftpd.passwd");
		// $I->assertRegExp("/$downloadLogin/", $password_file[count($password_file) - 1], "size: ".count($password_file));

    	// save account to the database
    	$filedrop->doi = $doi;
    	$filedrop->upload_login = $uploadLogin;
    	$filedrop->upload_token = $uploadToken;
        $filedrop->download_login = $downloadLogin;
    	$filedrop->download_token = $downloadToken;
    	$filedrop->status = "active";
    	$filedrop->save();

    	$accounts = FiledropAccount::find()
    		->where(['doi' => $doi])
    		->all();
    	$I->assertCount(1, $accounts);
    	$I->assertEquals($uploadLogin, $accounts[0]->upload_login);


    }
}
