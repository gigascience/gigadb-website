<?php
 /**
 * Test FiledropService to invoke on FUW REST APU the creation of filedrop account
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Ramsey\Uuid\Uuid;

class FiledropServiceTest extends FunctionalTesting
{

    use BrowserSignInSteps;
    use BrowserPageSteps;
    use CommonDataProviders;
    use DatabaseSteps;

    /** @var PDO $dbhf DB handle to FUW database connection */
    private $dbhf;

    /** @var int $filedrop_id id of file drop account created for testing */
    private $filedrop_id;

    /** @var string $doi DOI to use for testing */
    private $doi;

    /**
     *
     * @uses \BrowserSignInSteps::loginToWebSiteWithSessionAndCredentialsThenAssert()
     */
    public function setUp()
    {
        parent::setUp();

        //admin user is logged to gigadb
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org","gigadb","Admin");

        //test filedrop account doesn't exist in db
        $db_name = getenv("FUW_DB_NAME");
        $db_user = getenv("FUW_DB_USER");
        $db_password = getenv("FUW_DB_PASSWORD");
        $this->dbhf=new CDbConnection("pgsql:host=database;dbname=$db_name",$db_user,$db_password);
        $this->dbhf->active=true;

        // setup DOI and file drop account for testing
        $this->doi = "100004";
        $this->filedrop_id = $this->setUpFiledropAccount(
            $this->dbhf->getPdoInstance(), $this->doi
        );

    }

    public function tearDown()
    {
        $datasetDAO = new DatasetDAO(["identifier" => '100004']) ;
        $this->tearDownUserIdentity(
            $this->dbhf->pdoInstance,
            $datasetDAO->getSubmitter()->email
        );
        $this->tearDownFiledropAccount(
            $this->dbhf->getPdoInstance(),
            $this->filedrop_id
        );
        $this->dbhf->active=false;
        $this->dbhf = null;
        $this->doi = null;
        $this->filedrop_id = null;
        $datasetDAO = null;
        parent::tearDown();
    }

    /**
     * test calling to File Upload Wizard for creating dropbox account
     *
     * Happy path
     */
    public function testCreateAccountMakeAuthenticatedCall()
    {
        $api_endpoint = "http://fuw-admin-api/filedrop-accounts";
        $jwt_ttl = 31104000 ;

        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FiledropService
        $filedropSrv = new FiledropService([
            "tokenSrv" => new TokenService([
                                  'jwtTTL' => $jwt_ttl,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => new UserDAO(),
                                  'dt' => new DateTime(),
                                ]),
            "webClient" => $webClient,
            "requester" => \User::model()->findByPk(344), //admin user
            "identifier"=> $this->doi,
            "dataset" => new DatasetDAO(["identifier" => $this->doi]),
            "dryRunMode"=> true,
            ]);

        // set the right status on the dataset
        Dataset::model()->updateAll(["upload_status" => "AssigningFTPbox"], "identifier = :doi", [":doi" => $this->doi]);

        // invoke the Filedrop Service
        $response = $filedropSrv->createAccount();

        // test an authenticated HTTP call was actually made to the API
        $this->assertTrue(1 == count($container));
        $this->assertTrue("POST" == $container[0]['request']->getMethod());
        $this->assertTrue($api_endpoint == $container[0]['request']->getUri());
        $this->assertFalse(401 == $container[0]['response']->getStatusCode());
        $this->assertFalse(403 == $container[0]['response']->getStatusCode());

        // test the response from the API is successful
        $this->assertEquals(201, $container[0]['response']->getStatusCode());

        // test that createAccount return a value
        $this->assertNotNull($response);

        // test the upload status has been changed
        $dataset = Dataset::model()->findByAttributes(["identifier" => $this->doi]);
        $this->assertEquals("UserUploadingData",$dataset->upload_status);

        // restore original upload status
        Dataset::model()->updateAll(["upload_status" => "Published"], "identifier = :doi", [":doi" => $this->doi]);
    }

    /**
     * Newly created filedrop account properties are returned from call
     *
     * Happy path
     */
    public function testCreateAccountReturnsProperties()
    {
        $api_endpoint = "http://fuw-admin-api/filedrop-accounts";
        $jwt_ttl = 31104000 ;

        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FiledropService
        $filedropSrv = new FiledropService([
            "tokenSrv" => new TokenService([
                                  'jwtTTL' => $jwt_ttl,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => new UserDAO(),
                                  'dt' => new DateTime(),
                                ]),
            "webClient" => $webClient,
            "requester" => \User::model()->findByPk(344), //admin user
            "identifier"=> $this->doi,
            "dataset" => new DatasetDAO(["identifier" => $this->doi]),
            "dryRunMode"=> true,
            ]);

        // set the right status on the dataset
        Dataset::model()->updateAll(["upload_status" => "AssigningFTPbox"], "identifier = :doi", [":doi" => $this->doi]);

        // invoke the Filedrop Service
        $response = $filedropSrv->createAccount();

        // test the response from the API is successful
        $this->assertEquals(201, $container[0]['response']->getStatusCode());

        // test that createAccount return a value
        $this->assertNotNull($response);
        $this->assertEquals(0, $response["id"]);
        $this->assertEquals($this->doi, $response["doi"]);

        // test the upload status has been changed
        $dataset = Dataset::model()->findByAttributes(["identifier" => $this->doi]);
        $this->assertEquals("UserUploadingData",$dataset->upload_status);

        // restore original upload status
        Dataset::model()->updateAll(["upload_status" => "Published"], "identifier = :doi", [":doi" => $this->doi]);

    }

    /**
     * test calling to File Upload Wizard for creating dropbox account
     *
     * Test non-admin user failure mode
     */
    public function testCreateAccountWithNonAdminUser()
    {
        $api_endpoint = "http://fuw-admin-api/filedrop-accounts";
        $doi = "000009";
        $jwt_ttl = 31104000 ;

        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FiledropService
        $filedropSrv = new FiledropService([
            "tokenSrv" => new TokenService([
                                  'jwtTTL' => $jwt_ttl,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => new UserDAO(),
                                  'dt' => new DateTime(),
                                ]),
            "webClient" => $webClient,
            "requester" => \User::model()->findByPk(345),
            "identifier"=> $doi,
            "dryRunMode"=>true,
            ]);

        // invoke the Filedrop Service
        try {

            $success = $filedropSrv->createAccount();
        }
        catch(Exception $e) {
            // echo \GuzzleHttp\Psr7\str($e->getRequest());
        }

        // test an authenticated HTTP call was not actually made to the API
        $this->assertTrue(0 == count($container));
        $this->assertNull($success);

    }

    /**
     * test calling to File Upload Wizard for creating dropbox account
     *
     * Test wrong upload status user failure mode
     */
    public function testCreateAccountWithWrongStatus()
    {
        $api_endpoint = "http://fuw-admin-api/filedrop-accounts";
        $jwt_ttl = 31104000 ;

        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FiledropService
        $filedropSrv = new FiledropService([
            "tokenSrv" => new TokenService([
                                  'jwtTTL' => $jwt_ttl,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => new UserDAO(),
                                  'dt' => new DateTime(),
                                ]),
            "webClient" => $webClient,
            "requester" => \User::model()->findByPk(344),
            "identifier"=> $this->doi,
            "dryRunMode"=>true,
            ]);

        // invoke the Filedrop Service
        $success = $filedropSrv->createAccount();

        // test an authenticated HTTP call was not actually made to the API
        $this->assertTrue(0 == count($container));
        $this->assertNull($success);

    }


/**
     * test save instructions
     *
     */
    public function testSaveInstructions()
    {

        // $this->markTestSkipped('wip, not ready to run yet.');
        $api_endpoint = "http://fuw-admin-api/filedrop-accounts";
        $jwt_ttl = 31104000 ;

        $instructions = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo";

        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);
        $webClient = new Client(['handler' => $stack]);

        // Instantiate FiledropService
        $filedropSrv = new FiledropService([
            "tokenSrv" => new TokenService([
                                  'jwtTTL' => $jwt_ttl,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => new UserDAO(),
                                  'dt' => new DateTime(),
                                ]),
            "webClient" => $webClient,
            "requester" => \User::model()->findByPk(344),
            "identifier"=> $this->doi,
            "dryRunMode"=>true,
            ]);

        //invoke function
        $response = $filedropSrv->saveInstructions($this->filedrop_id,$instructions);

        // test the response from the API is successful
        $this->assertEquals(200, $container[0]['response']->getStatusCode());

        $this->assertTrue($response);

        //invoke function a second time to ensure token reuse in same session works
        $response2 = $filedropSrv->saveInstructions($this->filedrop_id,$instructions);
        $this->assertEquals(200, $container[0]['response']->getStatusCode());

    }

    /**
     * test sending email (happy path)
     *
     */
    public function testSendEmail()
    {

        // $this->markTestSkipped('wip, not ready to run yet.');
        $api_endpoint = "http://fuw-admin-api/filedrop-accounts";
        $jwt_ttl = 31104000 ;

        $recipient = "foo@bar.com";
        $subject = "Uploading Instructions";
        $instructions = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo";

        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);
        $webClient = new Client(['handler' => $stack]);

        // Dataset DAO is required to be passed to the service
        $datasetDAO = new DatasetDAO(["identifier" => $this->doi]) ;
        // var_dump($datasetDAO->getTitleAndStatus());
        // var_dump(Dataset::model()->findAll());

        // Instantiate FiledropService
        $filedropSrv = new FiledropService([
            "tokenSrv" => new TokenService([
                                  'jwtTTL' => $jwt_ttl,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => new UserDAO(),
                                  'dt' => new DateTime(),
                                ]),
            "webClient" => $webClient,
            "requester" => \User::model()->findByPk(344),
            "identifier"=> $this->doi,
            "dataset" => $datasetDAO,
            "dryRunMode"=>true,
            ]);

        //invoke function
        $response = $filedropSrv->emailInstructions($this->filedrop_id, $recipient ,$subject,$instructions);

        // test the response from the API is successful
        $this->assertEquals(200, $container[0]['response']->getStatusCode());

        $this->assertNotNull($response);

        // test that a new user record is created in File Upload Wizard for the author
        $this->assertNotNull($response['authorUserId'],'authorUserId');
        $this->assertNotNull($response['authorUserName'], 'authorUserName');
        $this->assertNotNull($response['authorUserEmail'],'authorUserEmail');

        //invoke function a second time to ensure token reuse in same session works
        $this->tearDownUserIdentity( //remove created user first
            $this->dbhf->pdoInstance,
            $datasetDAO->getSubmitter()->email
        );
        $response2 = $filedropSrv->emailInstructions($this->filedrop_id, $recipient, $subject,$instructions);
        $this->assertEquals(200, $container[0]['response']->getStatusCode());

    }

    /**
     * Test retrieving existing filedrop account from the API
     *
     * Happy path
     */
    public function testGetAccount()
    {

        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FiledropService
        $filedropSrv = new FiledropService([
            "tokenSrv" => new TokenService([
                                  'jwtTTL' => 31104000,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => new UserDAO(),
                                  'dt' => new DateTime(),
                                ]),
            "webClient" => $webClient,
            "requester" => \User::model()->findByPk(344), //admin user
            "identifier"=> $this->doi,
            "dataset" => new DatasetDAO(["identifier" => $this->doi]),
            "dryRunMode"=> false,
            ]);

        // invoke the Filedrop Service
        $response = $filedropSrv->getAccount($this->filedrop_id);

        // test the response from the API is successful
        $this->assertEquals(200, $container[0]['response']->getStatusCode());

        // test that createAccount return a value
        $this->assertNotNull($response);
        $this->assertEquals($this->filedrop_id, $response["id"]);
        $this->assertEquals($this->doi, $response["doi"]);
        $this->assertEquals("uploader-{$this->doi}", $response["upload_login"]);

        // test we can call getAccount a second time within the same session (token reuse)
        $response2 = $filedropSrv->getAccount($this->filedrop_id);
        $this->assertEquals(200, $container[0]['response']->getStatusCode());

    }

    /**
     * Test making a mockup url associated to a filedrop account
     *
     */
    public function testMakeMockupUrl()
    {
        try{
            $reviewerEmail = "reviewer2@gigadb.org";
            $monthsOfValidity = 1;

            // Prepare the http client to be traceable for testing
            $container = [];
            $history = Middleware::history($container);

            $stack = HandlerStack::create();
            // Add the history middleware to the handler stack.
            $stack->push($history);

            $webClient = new Client(['handler' => $stack]);

            // Instantiate FiledropService
            $srv = new FileDropService([
                "tokenSrv" => new TokenService([
                                      'jwtTTL' => 31104000,
                                      'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                      'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                      'users' => new UserDAO(),
                                      'dt' => new DateTime(),
                                    ]),
                "webClient" => $webClient,
                "requester" => \User::model()->findByPk(344), //admin user
                "identifier"=> $this->doi,
                "dataset" => new DatasetDAO(["identifier" => $this->doi]),
                "dryRunMode"=> false,
                ]);

            // Another token service to create mockup token
            $mockupTokenService = new TokenService([
                                      'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                      'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                      'dt' => new DateTime(),
                                    ]);

            // invoke the FileUpload Service
            $url_fragment = $srv->makeMockupUrl($mockupTokenService, $reviewerEmail,$monthsOfValidity);

            // test the response from the API is successful
            $this->assertEquals(201, $container[0]['response']->getStatusCode());
            // test that setAttributes return a value
            $this->assertNotNull($url_fragment);
            $this->assertTrue(Uuid::isValid($url_fragment));
        }
        catch(Error $e) {
            throw new Exception($e);
        }


    }

}

?>