<?php namespace backend\tests\functional;
use backend\tests\FunctionalTester;
use common\fixtures\UserFixture;

use backend\models\FiledropAccount;
use common\models\Upload;
use common\fixtures\UploadFixture;
use backend\models\DockerManager;

use Yii;

class FiledropAccountCest
{
    private $doi;

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
            ],
            'upload' => [
                'class' => UploadFixture::className(),
                'dataFile' => codecept_data_dir() . 'upload.php'
            ],        
        ];
    }

    /**
     * run before each test, it generates a random DOI to be used during the test
     */
    public function _before(FunctionalTester $I)
    {
    	$this->doi = Yii::$app->security->generateRandomString(6);

    }

    /**
     * run after each test, it deletes the DOI-keyed directories created during the test
     */
    public function _after(FunctionalTester $I)
    {
        Yii::$app->fs->deleteDir("incoming/ftp/".$this->doi);
        Yii::$app->fs->deleteDir("private/".$this->doi);
        Yii::$app->fs->deleteDir("repo/".$this->doi);
        Yii::$app->fs->deleteDir("tmp/processing_flag/".$this->doi);

        Yii::$app->fs->deleteDir("incoming/ftp/200001");
        Yii::$app->fs->deleteDir("private/200001");
        Yii::$app->fs->deleteDir("repo/200001");
        Yii::$app->fs->deleteDir("tmp/processing_flag/200001");
    }

	/**
     * functional test that directory are created, tokens generated and set to model class
     * @param FunctionalTester $I
     */
    public function prepareAccountSetFields(FunctionalTester $I)
    {
    	$filedrop = new FiledropAccount();
    	$dockerManager = new DockerManager();
    	$doi = $this->doi;

    	$I->assertTrue($filedrop->prepareAccountSetFields($doi));


    }


    /**
     * functional test that ftp accounts are created on the ftpd container
     * @param FunctionalTester $I
     */
    public function checkingAnFTPAccount(FunctionalTester $I)
    {
        $filedrop = new FiledropAccount();
        $dockerManager = new DockerManager();
        $doi = $this->doi;
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
    	$doi = $this->doi;
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

        $filedrop->removeFTPAccount( $dockerManager, $doi );

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
    	$doi = $this->doi;
    	$ftpServer = "ftpd";

    	$uploadLogin = "uploader-$doi";

    	$filedrop->setDOI($doi);
    	$filedrop->setDockerManager($dockerManager);
    	$filedrop->status = FiledropAccount::STATUS_ACTIVE;
    	$filedrop->save();

    	$accounts = FiledropAccount::find()
    		->where(['doi' => $doi])
    		->all();
    	$I->assertCount(1, $accounts);
    	$I->assertEquals($uploadLogin, $accounts[0]->upload_login);

        $filedrop->removeFTPAccount( $dockerManager, $doi );
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
    	$doi = $this->doi;
    	$I->amBearerAuthenticated("eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJBUEkgQWNjZXNzIHJlcXVlc3QgZnJvbSBjbGllbnQiLCJpc3MiOiJ3d3cuZ2lnYWRiLm9yZyIsImF1ZCI6ImZ1dy5naWdhZGIub3JnIiwiZW1haWwiOiJzZnJpZXNlbkBqZW5raW5zLmluZm8iLCJuYW1lIjoiSm9obiBTbWl0aCIsImFkbWluX3N0YXR1cyI6InRydWUiLCJyb2xlIjoiY3JlYXRlIiwiaWF0IjoiMTU2MTczMDgyMyIsIm5iZiI6IjE1NjE3MzA4MjMiLCJleHAiOiIyNzI5NTEzMjIwIn0.uTZpDB1eCGt3c_23wLaVxpFUw_WFH2Jep_vpzky2o18");
    	$I->sendPOST("/filedrop-accounts",['doi' =>"$doi"]);
    	$I->seeResponseCodeIs(201);
    	$I->seeResponseContainsJson(array('doi' => "$doi"));
    	$I->seeResponseContainsJson(array('upload_login' => "uploader-$doi"));
    	$I->seeResponseContainsJson(array('download_login' => "downloader-$doi"));
    	$I->seeResponseContainsJson(array('status' => FiledropAccount::STATUS_ACTIVE));
    	$I->seeResponseJsonMatchesJsonPath('$.upload_token');
    	$I->seeResponseJsonMatchesJsonPath('$.download_token');

        // clean up directories created by the test
        $filedrop = new FiledropAccount();
        $dockerManager = new DockerManager();
        $filedrop->setDOI($doi);
        $filedrop->setDockerManager($dockerManager);
        $filedrop->removeFTPAccount( $dockerManager, $doi );
    }

    /**
     * functional test http delete to delete account
     *
     * @param FunctionalTester $I
     */
    public function sendRestHttpDeleteToDeleteAccount(FunctionalTester $I)
    {
    	$doi = $this->doi;

    	$filedrop = new FiledropAccount();
    	$dockerManager = new DockerManager();

    	$filedrop->setDOI($doi);
    	$filedrop->setDockerManager($dockerManager);
    	$filedrop->status = FiledropAccount::STATUS_ACTIVE;
    	$filedrop->save();
        $I->assertEquals(FiledropAccount::STATUS_ACTIVE, $filedrop->status);
    	$I->amBearerAuthenticated("eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJBUEkgQWNjZXNzIHJlcXVlc3QgZnJvbSBjbGllbnQiLCJpc3MiOiJ3d3cuZ2lnYWRiLm9yZyIsImF1ZCI6ImZ1dy5naWdhZGIub3JnIiwiZW1haWwiOiJzZnJpZXNlbkBqZW5raW5zLmluZm8iLCJuYW1lIjoiSm9obiBTbWl0aCIsImFkbWluX3N0YXR1cyI6InRydWUiLCJyb2xlIjoiY3JlYXRlIiwiaWF0IjoiMTU2MTczMDgyMyIsIm5iZiI6IjE1NjE3MzA4MjMiLCJleHAiOiIyNzI5NTEzMjIwIn0.uTZpDB1eCGt3c_23wLaVxpFUw_WFH2Jep_vpzky2o18");
    	$I->sendDELETE("/filedrop-accounts/" . $filedrop->id);
    	$I->seeResponseCodeIs(204);
        $filedrop = FiledropAccount::findOne(["doi" => $doi]);
    	$I->assertEquals(FiledropAccount::STATUS_TERMINATED, $filedrop->status, "account set to 'terminated'");

        $I->assertNotTrue(file_exists("/var/incoming/ftp/$doi"), "incoming directory removed");
        $I->assertNotTrue(file_exists("/var/repo/$doi"), "repository removed");
        $I->assertNotTrue(file_exists("/var/private/$doi"), "private directory removed");
    }

    /**
     * Testing PUT on /filedrop-account/ with additional params for email
     *
     */
    public function sendRestHttpPutToUpdateFiledropAccountAndSendEmail(FunctionalTester $I)
    {

        $doi = $this->doi;
        $subject = Yii::$app->security->generateRandomString(32);
        $content = Yii::$app->security->generateRandomString(128);
        $recipient = "user_gigadb3@mailinator.com";

        $filedrop = new FiledropAccount();
        $dockerManager = new DockerManager();
        $filedrop->setDOI($doi);
        $filedrop->setDockerManager($dockerManager);
        $filedrop->status = FiledropAccount::STATUS_ACTIVE;
        $filedrop->save();

        $I->amBearerAuthenticated("eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJBUEkgQWNjZXNzIHJlcXVlc3QgZnJvbSBjbGllbnQiLCJpc3MiOiJ3d3cuZ2lnYWRiLm9yZyIsImF1ZCI6ImZ1dy5naWdhZGIub3JnIiwiZW1haWwiOiJzZnJpZXNlbkBqZW5raW5zLmluZm8iLCJuYW1lIjoiSm9obiBTbWl0aCIsImFkbWluX3N0YXR1cyI6InRydWUiLCJyb2xlIjoiY3JlYXRlIiwiaWF0IjoiMTU2MTczMDgyMyIsIm5iZiI6IjE1NjE3MzA4MjMiLCJleHAiOiIyNzI5NTEzMjIwIn0.uTZpDB1eCGt3c_23wLaVxpFUw_WFH2Jep_vpzky2o18");
        $I->sendPUT("/filedrop-accounts/{$filedrop->id}",[  "instructions" => $content,
                                            "to" => $recipient,
                                            "subject" => $subject,
                                            "send" => 1 ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('id' => $filedrop->id));

        $updated = FiledropAccount::find()->where(['id' => $filedrop->id])->one();
        $I->assertEquals($doi,$updated->doi);
        $I->assertEquals($content,$updated->instructions);
        $I->seeEmailIsSent();
    }

    /**
     * Testing MoveFilesAction create a worker job and post it to the message queue
     *
     */
    public function moveFiles(FunctionalTester $I)
    {

        $doi = "200001"; //we use the DOI from uploads fixture data

        $filedrop = new FiledropAccount();
        $dockerManager = new DockerManager();
        $filedrop->setDOI($doi);
        $filedrop->setDockerManager($dockerManager);
        $filedrop->status = FiledropAccount::STATUS_ACTIVE;
        $filedrop->save();

        $I->amBearerAuthenticated("eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJBUEkgQWNjZXNzIHJlcXVlc3QgZnJvbSBjbGllbnQiLCJpc3MiOiJ3d3cuZ2lnYWRiLm9yZyIsImF1ZCI6ImZ1dy5naWdhZGIub3JnIiwiZW1haWwiOiJzZnJpZXNlbkBqZW5raW5zLmluZm8iLCJuYW1lIjoiSm9obiBTbWl0aCIsImFkbWluX3N0YXR1cyI6InRydWUiLCJyb2xlIjoiY3JlYXRlIiwiaWF0IjoiMTU2MTczMDgyMyIsIm5iZiI6IjE1NjE3MzA4MjMiLCJleHAiOiIyNzI5NTEzMjIwIn0.uTZpDB1eCGt3c_23wLaVxpFUw_WFH2Jep_vpzky2o18");
        $I->sendPOST("/filedrop-accounts/move/{$filedrop->id}");
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->canSeeResponseContains("jobId");
        $I->seeResponseContainsJson(array('doi' => "$doi"));
        $I->seeResponseContainsJson(array('file' => "084.fq"));
        $I->seeResponseContainsJson(array('file' => "085.fq"));
        $I->canSeeResponseJsonMatchesJsonPath("$.jobs[0].file");
        $I->canSeeResponseJsonMatchesJsonPath("$.jobs[0].jobId");
        $I->canSeeResponseJsonMatchesJsonPath("$.jobs[1].file");
        $I->canSeeResponseJsonMatchesJsonPath("$.jobs[1].jobId");


    }
}

