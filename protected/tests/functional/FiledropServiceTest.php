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

class FiledropServiceTest extends FunctionalTesting
{

    use BrowserSignInSteps;
    use BrowserPageSteps;
    use CommonDataProviders;

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
        $filedrop_id = 435342;
        $db_name = getenv("FUW_DB_NAME");
        $db_user = getenv("FUW_DB_USER");
        $db_password = getenv("FUW_DB_PASSWORD");
        $dbh=new CDbConnection("pgsql:host=database;dbname=$db_name",$db_user,$db_password);
        $dbh->active=true;
        $delete_account = $dbh->createCommand("delete from filedrop_account where id=$filedrop_id");
        $delete_account->execute();
        $dbh->active=false;

    }

    /**
     * test calling to File Upload Wizard for creating dropbox account
     *
     * Happy path
     */
    public function testCreateAccountMakeAuthenticatedCall()
    {
        $api_endpoint = "http://fuw-admin-api/filedrop-accounts";
        $doi = "101001";
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
            "identifier"=> $doi,
            "dataset" => new DatasetDAO(["identifier" => $doi]),
            "dryRunMode"=> true,
            ]);

        // set the right status on the dataset
        Dataset::model()->updateAll(["upload_status" => "AssigningFTPbox"], "identifier = :doi", [":doi" => $doi]);

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
        $dataset = Dataset::model()->findByAttributes(["identifier" => $doi]);
        $this->assertEquals("UserUploadingData",$dataset->upload_status);

        // restore original upload status
        Dataset::model()->updateAll(["upload_status" => "Published"], "identifier = :doi", [":doi" => $doi]);
    }

    /**
     * Newly created filedrop account properties are returned from call
     *
     * Happy path
     */
    public function testCreateAccountReturnsProperties()
    {
        $api_endpoint = "http://fuw-admin-api/filedrop-accounts";
        $doi = "101001";
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
            "identifier"=> $doi,
            "dataset" => new DatasetDAO(["identifier" => $doi]),
            "dryRunMode"=> true,
            ]);

        // set the right status on the dataset
        Dataset::model()->updateAll(["upload_status" => "AssigningFTPbox"], "identifier = :doi", [":doi" => $doi]);

        // invoke the Filedrop Service
        $response = $filedropSrv->createAccount();

        // test the response from the API is successful
        $this->assertEquals(201, $container[0]['response']->getStatusCode());

        // test that createAccount return a value
        $this->assertNotNull($response);
        $this->assertEquals(0, $response["id"]);
        $this->assertEquals($doi, $response["doi"]);

        // test the upload status has been changed
        $dataset = Dataset::model()->findByAttributes(["identifier" => $doi]);
        $this->assertEquals("UserUploadingData",$dataset->upload_status);

        // restore original upload status
        Dataset::model()->updateAll(["upload_status" => "Published"], "identifier = :doi", [":doi" => $doi]);

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
        $doi = "100004";
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
            "identifier"=> $doi,
            "dryRunMode"=>true,
            ]);

        // invoke the Filedrop Service
        $success = $filedropSrv->createAccount();

        // test an authenticated HTTP call was not actually made to the API
        $this->assertTrue(0 == count($container));
        $this->assertNull($success);

    }

    /**
     * test sending email (happy path)
     *
     */
    public function testSendEmail()
    {

        // $this->markTestSkipped('wip, not ready to run yet.');
        $api_endpoint = "http://fuw-admin-api/filedrop-accounts";
        $doi = "100004";
        $jwt_ttl = 31104000 ;

        $subject = "Uploading Instructions";
        $instructions = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo";

        // create a filedrop acccount to update and save email instructions into
        $filedrop_id = 435342;
        $db_name = getenv("FUW_DB_NAME");
        $db_user = getenv("FUW_DB_USER");
        $db_password = getenv("FUW_DB_PASSWORD");
        $dbh=new CDbConnection("pgsql:host=database;dbname=$db_name",$db_user,$db_password);
        $dbh->active=true;
        $insert_account = $dbh->createCommand("insert into filedrop_account(id, doi,status,upload_login,upload_token,download_login,download_token) values($filedrop_id,$doi,1,'a','a','a','a')");
        $insert_account->execute();

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
            "identifier"=> $doi,
            "dryRunMode"=>true,
            ]);

        //invoke function
        $response = $filedropSrv->emailInstructions($filedrop_id, $subject,$instructions);

        // test the response from the API is successful
        $this->assertEquals(200, $container[0]['response']->getStatusCode());

        $this->assertTrue($response);

        //invoke function a second time to ensure token reuse in same session works
        $response2 = $filedropSrv->emailInstructions($filedrop_id, $subject,$instructions);
        $this->assertEquals(200, $container[0]['response']->getStatusCode());

    }

    /**
     * Test retrieving existing filedrop account from the API
     *
     * Happy path
     */
    public function testGetAccount()
    {

        // create a filedrop acccount to retrieve through API
        $filedrop_id = 435342;
        $doi = "100004";
        $db_name = getenv("FUW_DB_NAME");
        $db_user = getenv("FUW_DB_USER");
        $db_password = getenv("FUW_DB_PASSWORD");
        $dbh=new CDbConnection("pgsql:host=database;dbname=$db_name",$db_user,$db_password);
        $dbh->active=true;
        $insert_account = $dbh->createCommand("insert into filedrop_account(id, doi,status,upload_login,upload_token,download_login,download_token) values($filedrop_id,$doi,1,'uploader-$doi','sdafad','downloader-$doi','asdgina')");
        $insert_account->execute();

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
            "identifier"=> $doi,
            "dataset" => new DatasetDAO(["identifier" => $doi]),
            "dryRunMode"=> false,
            ]);

        // invoke the Filedrop Service
        $response = $filedropSrv->getAccount($filedrop_id);

        // test the response from the API is successful
        $this->assertEquals(200, $container[0]['response']->getStatusCode());

        // test that createAccount return a value
        $this->assertNotNull($response);
        $this->assertEquals($filedrop_id, $response["id"]);
        $this->assertEquals($doi, $response["doi"]);
        $this->assertEquals("uploader-$doi", $response["upload_login"]);

        // test we can call getAccount a second time within the same session (token reuse)
        $response2 = $filedropSrv->getAccount($filedrop_id);
        $this->assertEquals(200, $container[0]['response']->getStatusCode());

    }

}

?>