<?php namespace backend\tests\functional;
use backend\tests\FunctionalTester;
use common\fixtures\UserFixture;

use backend\models\FiledropAccount;
use backend\models\DockerManager;

use Yii;

class FiledropAccountCest
{
	/**
     * Load fixtures before db transaction begin
     * Called in _before()
     * @see \Codeception\Module\Yii2::_before()
     * @see \Codeception\Module\Yii2::loadFixtures()
     * @return array
     */
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'login_data.php'
            ]
        ];
    }

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

        // clean up directories created by the test
        $filedrop->removeDirectories("$doi");
    }


    /**
     * functional test that ftp accounts are created on the ftpd container
     * @param FunctionalTester $I
     */
    public function checkingAnFTPAccount(FunctionalTester $I)
    {
        $filedrop = new FiledropAccount();
        $dockerManager = new DockerManager();
        $doi = FiledropAccount::generateRandomString(6);
        $ftpServer = "ftpd";


        $filedrop->setDOI($doi);
        $filedrop->setDockerManager($dockerManager);

        $response = $filedrop->checkFTPAccount( $dockerManager, $doi );
        $I->assertNotTrue($response);

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

    	// create accounts
    	$status = $filedrop->createFTPAccount( $dockerManager, $doi );
    	$I->assertTrue($status);

        // check success (TODO)
        // $status = $filedrop->checkFTPAccount( $dockerManager, $doi );
        // $I->assertTrue($status, "account created for $doi, returned $status");


        // clean up directories created by the test
        $filedrop->removeDirectories("$doi");

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

        // clean up directories created by the test
        $filedrop->removeDirectories("$doi");
    }

    /**
     * functional test http post to create account
     *
     * @param FunctionalTester $I
     */
    public function sendRestHttpPostToCreateAccount(FunctionalTester $I)
    {

    	// what's in the token:
  		// {
		//   "sub":"API Access request from client",
		//   "iss": "www.gigadb.org",
		//   "aud": "fuw.gigadb.org",
		//   "email": "sfriesen@jenkins.info",
		//   "name": "John Smith",
		//   "admin_status": "true",
		//   "role": "create",
		//   "iat" : "1561730823",
		//   "nbf" : "1561730823",
		//   "exp" : "2729513220"
		// }
    	$doi = FiledropAccount::generateRandomString(6);
    	$I->amBearerAuthenticated("eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJBUEkgQWNjZXNzIHJlcXVlc3QgZnJvbSBjbGllbnQiLCJpc3MiOiJ3d3cuZ2lnYWRiLm9yZyIsImF1ZCI6ImZ1dy5naWdhZGIub3JnIiwiZW1haWwiOiJzZnJpZXNlbkBqZW5raW5zLmluZm8iLCJuYW1lIjoiSm9obiBTbWl0aCIsImFkbWluX3N0YXR1cyI6InRydWUiLCJyb2xlIjoiY3JlYXRlIiwiaWF0IjoiMTU2MTczMDgyMyIsIm5iZiI6IjE1NjE3MzA4MjMiLCJleHAiOiIyNzI5NTEzMjIwIn0.uTZpDB1eCGt3c_23wLaVxpFUw_WFH2Jep_vpzky2o18");
    	$I->sendPOST("/filedrop-accounts",['doi' =>"$doi"]);
    	$I->seeResponseCodeIs(201);
    	$I->seeResponseContainsJson(array('doi' => "$doi"));
    	$I->seeResponseContainsJson(array('upload_login' => "uploader-$doi"));
    	$I->seeResponseContainsJson(array('download_login' => "downloader-$doi"));
    	$I->seeResponseContainsJson(array('status' => "active"));
    	$I->seeResponseJsonMatchesJsonPath('$.upload_token');
    	$I->seeResponseJsonMatchesJsonPath('$.download_token');

        // clean up directories created by the test
        $filedrop = new FiledropAccount();
        $filedrop->setDOI($doi);
        $filedrop->removeDirectories("$doi");
    }

    /**
     * functional test http delete to delete account
     *
     * @param FunctionalTester $I
     */
    public function sendRestHttpDeleteToDeleteAccount(FunctionalTester $I)
    {
    	$doi = FiledropAccount::generateRandomString(6);

    	$filedrop = new FiledropAccount();
    	$dockerManager = new DockerManager();

    	$filedrop->setDOI($doi);
    	$filedrop->setDockerManager($dockerManager);
    	$filedrop->status = "active";
    	$filedrop->save();
        $I->assertEquals("active", $filedrop->status);
    	$I->amBearerAuthenticated("eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJBUEkgQWNjZXNzIHJlcXVlc3QgZnJvbSBjbGllbnQiLCJpc3MiOiJ3d3cuZ2lnYWRiLm9yZyIsImF1ZCI6ImZ1dy5naWdhZGIub3JnIiwiZW1haWwiOiJzZnJpZXNlbkBqZW5raW5zLmluZm8iLCJuYW1lIjoiSm9obiBTbWl0aCIsImFkbWluX3N0YXR1cyI6InRydWUiLCJyb2xlIjoiY3JlYXRlIiwiaWF0IjoiMTU2MTczMDgyMyIsIm5iZiI6IjE1NjE3MzA4MjMiLCJleHAiOiIyNzI5NTEzMjIwIn0.uTZpDB1eCGt3c_23wLaVxpFUw_WFH2Jep_vpzky2o18");
    	$I->sendDELETE("/filedrop-accounts/" . $filedrop->id);
    	$I->seeResponseCodeIs(204);
        $filedrop = FiledropAccount::findOne(["doi" => $doi]);
    	$I->assertEquals("terminated", $filedrop->status, "account set to 'terminated'");

        $I->assertNotTrue(file_exists("/var/incoming/ftp/$doi"), "incoming directory removed");
        $I->assertNotTrue(file_exists("/var/repo/$doi"), "repository removed");
        $I->assertNotTrue(file_exists("/var/private/$doi"), "private directory removed");
    }
}
