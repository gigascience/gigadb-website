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
     * functional test that directory are created, tokens generated and set to model class
     * @param FunctionalTester $I
     */
    public function prepareAccountSetFields(FunctionalTester $I)
    {
    	$filedrop = new FiledropAccount();
    	$dockerManager = new DockerManager();
    	$doi = FiledropAccount::generateRandomString(6);

    	$I->assertTrue($filedrop->prepareAccountSetFields($doi));
    }

    /**
     * functional test that ftp accounts are created on the ftpd container
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

    }

    /**
     * functional test that accounts are created and save in database
     *
     * @todo validate directories, token and ftp account exist before saving
     * @param FunctionalTester $I
     */
    public function createAccountsDatabaseRecord(FunctionalTester $I)
    {

    	$filedrop = new FiledropAccount();
    	$dockerManager = new DockerManager();
    	$doi = FiledropAccount::generateRandomString(6);
    	$ftpServer = "ftpd";

    	$uploadLogin = "uploader-$doi";

    	$filedrop->setDOI($doi);
    	$filedrop->setDockerManager($dockerManager);
    	$filedrop->status = "active";
    	$filedrop->save();

    	$accounts = FiledropAccount::find()
    		->where(['doi' => $doi])
    		->all();
    	$I->assertCount(1, $accounts);
    	$I->assertEquals($uploadLogin, $accounts[0]->upload_login);
    }

    /**
     * functional test http post to create account
     *
     * @param FunctionalTester $I
     */
    public function sendRestHttpPostToCreateAccount(FunctionalTester $I)
    {
    	$doi = FiledropAccount::generateRandomString(6);
    	// $I->amBearerAuthenticated("hfafsdadsgv2n887ad5");
    	// $I->sendPOST("/filedrop-accounts",['doi' =>"$doi"]);
    	// $I->seeResponseCodeIs(201);
    }
}
