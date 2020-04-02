<?php
 /**
 * Test FiledropService to interact with FUW's public REST API
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Ramsey\Uuid\Uuid;

class FileUploadServiceTest extends CTestCase
{
    use DatabaseSteps;

    /** @var PDO $dbhf DB handle to FUW database connection */
    private $dbhf;

    /** @var array $uploads list of uploaded files */
    private $uploads;

    /** @var Object $account file drop account */
    private $account;

    /** @var string $doi DOI to use for testing */
    private $doi;

    /** @var string $url_fragment UUID generated for mockupUrl test data */
    private $url_fragment; 
    private $mockupUrlId;

    /**
     *
     * @uses \BrowserSignInSteps::loginToWebSiteWithSessionAndCredentialsThenAssert()
     */
    public function setUp()
    {
        parent::setUp();
        $dbName = getenv("FUW_DB_NAME");
        $dbUser = getenv("FUW_DB_USER");
        $dbPassword = getenv("FUW_DB_PASSWORD");
        $dbHost = getenv("FUW_DB_HOST");
        $this->dbhf=new CDbConnection(
            "pgsql:host=$dbHost;dbname=$dbName",
            $dbUser,$dbPassword
        );
        $this->dbhf->active=true;

        // setup DOI and file drop account for testing
        $this->doi = "100004";
        // create a filedrop acccount
        $this->account = $this->setUpFiledropAccount(
            $this->dbhf->getPdoInstance(), $this->doi
        );
        // create file uploads associated with that account
        $files =  [
                ["doi" => "{$this->doi}", "name" =>"somefile.txt", "size" => 325352, "status"=> 0, "location" => "ftp://foobar", "description" => "", "extension" => "TEXT", "datatype"=>"Text"],
                ["doi" => "{$this->doi}", "name" =>"anotherfile.png", "size" => 5463434, "status"=> 0, "location" => "ftp://barfoo", "description" => "", "extension" => "PNG", "datatype"=>"Image"],
                ["doi" => "{$this->doi}", "name" =>"shouldnotdisplay.png", "size" => 5463434, "status"=> 2, "location" => "ftp://barfoo", "description" => "", "extension" => "PNG", "datatype"=>"Image"],
            ];
        $this->uploads = $this->setUpFileUploads(
            $this->dbhf->getPdoInstance(), $files
        );

        list($this->mockupUrlId, $this->url_fragment) = $this->setUpMockupUrl(
            $this->dbhf->getPdoInstance(),
            "someone@foobar.test",
            3,
            "000007"
        );

    }

    public function tearDown()
    {
        // remove the account and the files from database
        $this->tearDownFiledropAccount(
            $this->dbhf->getPdoInstance(),
            $this->account
        );

        $this->tearDownFileUploads(
            $this->dbhf->getPdoInstance(),
            $this->uploads
        );

        $this->tearDownAttributes(
            $this->dbhf->getPdoInstance(),
            $this->uploads
        );

        $this->tearDownMockupUrl(
            $this->dbhf->getPdoInstance(),
            $this->url_fragment
        );

        $this->dbhf->active=false;
        $this->dbhf = null;
        $this->doi = null;
        parent::tearDown();
    }

    /**
     * Test retrieving existing uploaded files from the API
     *
     */
    public function testGetUploads()
    {
        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FiledropService
        $filedropSrv = new FileUploadService([
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
        $response = $filedropSrv->getUploads($this->doi);

        // test the response from the API is successful
        $this->assertEquals(200, $container[0]['response']->getStatusCode());
        // test that getUploads return a value
        $this->assertNotNull($response);
        // and that it's an array of files
        $this->assertEquals(2, count($response));

    }
    
    /**
     * Test retrieving existing uploaded files from the API
     *
     * Happy path
     */
    public function testUpdateUpload()
    {
        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FiledropService
        $filedropSrv = new FileUploadService([
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

        // Setup post data
        $postData = [ 
            $this->uploads[0] => [ 'doi' => $this->doi, 'name' =>"somefile.txt",'datatype' => 'Text', 'description' => 'foo bar'],
            $this->uploads[1] => [ 'doi' => $this->doi, 'name' =>"someimage.png",'datatype' => 'Image', 'description' => 'hello world'],
        ];
        // invoke the Filedrop Service
        $response = $filedropSrv->updateUpload($this->uploads[0],$postData[$this->uploads[0]]);

        // test the response from the API is successful
        $this->assertEquals(200, $container[0]['response']->getStatusCode());
        // test that getUploads return a value
        $this->assertTrue($response);

    }


/**
     * Test updating multiple upload records in FUW API
     *
     * Happy path
     */
    public function testUpdateUploadMultiple()
    {
        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FiledropService
        $srv = new FileUploadService([
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

        // Setup post data
        $postData = [ 
            $this->uploads[0] => [ 'doi' => $this->doi, 'name' =>"somefile.txt",'datatype' => 'Text', 'description' => 'foo bar'],
            $this->uploads[1] => [ 'doi' => $this->doi, 'name' =>"anotherfile.png",'datatype' => 'Image', 'description' => 'hello world'],
        ];
        // invoke the Filedrop Service
        $response = $srv->updateUploadMultiple($this->doi,$postData);

        // test the response from the API is successful
        $this->assertEquals(200, $container[0]['response']->getStatusCode());
        // test that getUploads return a value
        $this->assertTrue($response);

    }

    /**
     * Test deleting existing uploaded files from the API
     *
     */
    public function testDeleteUploads()
    {
        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FiledropService
        $srv = new FileUploadService([
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
        $response = $srv->deleteUploads(array_slice($this->uploads,0,2)); //just passing the first two uploads

        // test the response from the API is successful
        $this->assertEquals(200, $container[0]['response']->getStatusCode());
        // test that getUploads return a value
        $this->assertNotNull($response);
        // and that it's an array of files
        $this->assertEquals(2, count($response)); //return only the two passed in the arguments, no more
        $this->assertEquals(2, $response[0]['status']);
        $this->assertEquals(2, $response[1]['status']);

    }

    /**
     * Test setting up email notification on FUW API
     *
     */
    public function testEmailSend()
    {
        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FiledropService
        $fileUploadSrv = new FileUploadService([
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

        // Setup post data
        $postData = [ 
            'sender' => "me@gigadb.test",
            'recipient' => "someone@example.test",
            'subject' => "functional test for message notification",
            'content' => "Lorem ipsum foo bar hellow world something",
        ];
        // invoke the Filedrop Service
        // $response = $fileUploadSrv->emailSend(array_values($postData));
        $response = $fileUploadSrv->emailSend(
            $postData["sender"], 
            $postData["recipient"],
            $postData["subject"],
            $postData["content"]
        );

        // test the response from the API is successful
        $this->assertEquals(200, $container[0]['response']->getStatusCode());
        // test that getUploads return a value
        $this->assertTrue($response);

    }

    /**
     * Test adding attributes of a file uploaded by an author
     *
     */
    public function testSetAttributesFromNone()
    {
        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FiledropService
        $srv = new FileUploadService([
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

        // Setup post data
        $example = [
            $this->uploads[0] => [
                "Attributes" => [
                    0 => ["name" => "Temperature","value" => "45", "unit" => "Celsius"],
                    1 => ["name" => "Humidity", "value" => "75", "unit" => "%"],
                    2 => ["name" => "Age","value" => "33", "unit" => "Years"],
                ]
            ],
            $this->uploads[1] => [
                "Attributes" => [
                        0 => [ "value" => "3000", "unit" => "Nits"],
                ]
            ], 
        ];
        // invoke the Filedrop Service
        $response = $srv->setAttributes($this->uploads[0], $example[$this->uploads[0]]);

        // test the response from the API is successful
        $this->assertEquals(200, $container[0]['response']->getStatusCode());
        // test that setAttributes return a value
        $this->assertTrue($response);
        $this->assertTrue(
            3 === count( 
                    json_decode($container[0]['response']->getBody(), true) 
                ) 
        );


    }


    /**
     * Test adding attributes of a file uploaded by an author
     *
     */
    public function testAddAttributes()
    {
        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FiledropService
        $srv = new FileUploadService([
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

        // Setup post data
        $example = [
            $this->uploads[0] => [
                // "Attributes" => [
                    0 => ["name" => "Temperature","value" => "45", "unit" => "Celsius", "upload_id" => $this->uploads[0] ],
                    1 => ["name" => "Humidity", "value" => "75", "unit" => "%", "upload_id" => $this->uploads[0]],
                    2 => ["name" => "Age","value" => "33", "unit" => "Years", "upload_id" => $this->uploads[0]],
                // ]
            ],
            $this->uploads[1] => [
                // "Attributes" => [
                        0 => [ "value" => "3000", "unit" => "Nits", "upload_id" => $this->uploads[1]],
                // ]
            ], 
        ];
        // invoke the FileUpload Service
        $response = $srv->addAttributes($this->uploads[0], $example[$this->uploads[0]]);

        // test the response from the API is successful
        $this->assertEquals(200, $container[0]['response']->getStatusCode());
        // test that setAttributes return a value
        $this->assertTrue($response);
        $this->assertTrue(
            3 === count( 
                    json_decode($container[0]['response']->getBody(), true) 
                ) 
        );


    }

    /**
     * Test retrieving existing uploaded files from the API
     *
     */
    public function testGetAttributes()
    {
        // set up two attributes on the first upload and return their names
        $attr1 = $this->setupAttributes(
            $this->dbhf->getPdoInstance(), $this->uploads[0]
        );
        $attr2 = $this->setupAttributes(
            $this->dbhf->getPdoInstance(), $this->uploads[0]
        );
        $attr3 = $this->setupAttributes(
            $this->dbhf->getPdoInstance(), $this->uploads[0]
        );

        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FileUploadService
        $srv = new FileUploadService([
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
        $response = $srv->getAttributes($this->uploads[0]);

        // test the response from the API is successful
        $this->assertEquals(200, $container[0]['response']->getStatusCode());
        // test that getUploads return a value
        $this->assertNotNull($response);
        // and that it's an array of files
        $this->assertEquals(3, count($response));

    }


    /**
     * Test retrieving mockupUrl given a url fragment
     *
     */
    public function testGetMockupUrl()
    {

        // setup test data

        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FileUploadService
        $srv = new FileUploadService(["webClient" => $webClient]);

        // invoke the Filedrop Service
        $response = $srv->getMockupUrl($this->url_fragment);

        // test the response from the API is successful
        $this->assertEquals(200, $container[0]['response']->getStatusCode());
        // test that getUploads return a value
        $this->assertNotNull($response);

        $this->assertEquals([
            "reviewerEmail" => "someone@foobar.test", 
            "monthsOfValidity" => 3, 
            "DOI" => "000007", 
        ], $response);

    }

/**
     * Test retrieving mockupUrl when url_fragment does not exist
     *
     */
    public function testGetMockupUrlNonExistentFragment()
    {

        // setup test data

        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FileUploadService
        $srv = new FileUploadService(["webClient" => $webClient]);

        // invoke the Filedrop Service
        $response = $srv->getMockupUrl("1ee9aa1b-6510-4105-92b9-7171bb2f3089");

        // test the response from the API return 404 (Not Found)
        $this->assertEquals(404, $container[0]['response']->getStatusCode());
        // test that response is null
        $this->assertNull($response);


    }
}

?>