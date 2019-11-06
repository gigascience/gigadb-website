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
        $this->assertTrue($response);

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
        $this->assertFalse($success);

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
        $this->assertFalse($success);

    }

    /**
     * test sending email
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
        $filedrop_id = 1;

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
    }

}

?>