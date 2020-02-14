<?php
 /**
 * Test posting data to FilesAnnotateAction
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

class AuthorisedDatasetFilesAnnotateAction extends FunctionalTesting
{
    use BrowserSignInSteps;
    use BrowserPageSteps;
    use CommonDataProviders;
    use DatabaseSteps;

    /** @var string $url url of file upload endpoint to test access control on */
    public $url = "http://gigadb.test/" ;

    /** @var string $userEmail email of logged in user */
    private $userEmail = "user@gigadb.org";

    /** @var string $doi DOI to use for testing */
    private $doi = "100005";

    /** @var PDO $dbh database handle for GigaDB database */
    public $dbh;
    /** @var PDO $dbh database handle for FUW database */
    public $dbhf;

    /** @var int $filedrop_id id of file drop account created for testing */
    private $filedrop_id;


    /** @var array $uploads list of uploaded files */
    private $uploads;

    public function setUp()
    {
        parent::setUp();

        try {

            $this->dbh = new PDO("pgsql:host=".getenv("GIGADB_HOST").";dbname=".getenv("GIGADB_DB"), getenv("GIGADB_USER"), getenv("GIGADB_PASSWORD"));
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); //PHP warnings for SQL errors

            $db_name = getenv("FUW_DB_NAME");
            $db_user = getenv("FUW_DB_USER");
            $db_password = getenv("FUW_DB_PASSWORD");
            $this->dbhf=new CDbConnection("pgsql:host=database;dbname=$db_name",$db_user,$db_password);
            $this->dbhf->active=true;

            // setup file drop account and FUW identity for testing
            $this->filedrop_id = $this->setUpFiledropAccount(
                $this->dbhf->getPdoInstance(), 
                $this->doi
            );
            $this->setUpUserIdentity(
                $this->dbhf->getPdoInstance(), 
                $this->userEmail
            );

            $files =  [
                ["doi" => "{$this->doi}", "name" =>"somefile.txt", "size" => 325352, "status"=> 1, "location" => "ftp://foobar", "description" => "", "extension" => "TEXT", "datatype"=>"Text"],
                ["doi" => "{$this->doi}", "name" =>"anotherfile.png", "size" => 5463434, "status"=> 1, "location" => "ftp://barfoo", "description" => "", "extension" => "PNG", "datatype"=>"Image"],
            ];
            $this->uploads = $this->setUpFileUploads(
                $this->dbhf->getPdoInstance(), $files
            );

        }
        catch (PDOException $e) {
            exit("Failed connecting to database:". $e->getMessage());
        }



    }

    public function tearDown()
    {
        $this->setUpDatasetUploadStatus($this->dbh, $this->doi,"Published"); //restore default
        $this->tearDownFiledropAccount(
            $this->dbhf->getPdoInstance(),
            $this->filedrop_id
        );
        $this->tearDownFileUploads(
            $this->dbhf->getPdoInstance(),
            $this->uploads
        );
        $this->tearDownUserIdentity(
            $this->dbhf->getPdoInstance(),
            $this->userEmail
        );
        $this->dbhf->active=false;
        $this->dbhf = null;
        $this->doi = null;
        $this->userEmail = null;
        $this->url = null;
        $this->filedrop_id = null;
        $this->uploads = null;
        $this->dbh = null;
        parent::tearDown();
    }

    public function testPostUploadsData() {

        // set upload status to the correct UserUploadingData
        $this->setUpDatasetUploadStatus($this->dbh, $this->doi ,"UserUploadingData");


        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);
        $webClient = new Client(['handler' => $stack]);


        //log in as a user
        $jar = new \GuzzleHttp\Cookie\CookieJar;
        $response = $webClient->request('POST', $this->url . 'site/login', [
            'cookies' => $jar,
            form_params => [
                "LoginForm[username]" => "user@gigadb.org",
                "LoginForm[password]" => "gigadb",
                "LoginForm[rememberMe]" => "2592000",
                "yt0" => "Login",
            ]
        ]);

        $this->assertEquals(302, $container[0]['response']->getStatusCode());


        //post  metadata for uploaded files.
        $metadata = [
            "Upload[{$this->uploads[0]}][datatype]" => "Rich Text",
            "Upload[{$this->uploads[0]}][description]" => "The moon",
            "Upload[{$this->uploads[1]}][datatype]" => "Image",
            "Upload[{$this->uploads[1]}][description]" => "The sun",
        ];
        $response = $webClient->request('POST', $this->url . "authorisedDataset/annotateFiles/id/" . $this->doi, [
            'cookies' => $jar,
            form_params => $metadata
        ]);
        $this->assertEquals(302, $container[0]['response']->getStatusCode());

        // check that the change went through
        $this->assertUploadFields($this->dbhf->getPdoInstance(), $this->uploads[0], "Rich Text", "The moon");
        $this->assertUploadFields($this->dbhf->getPdoInstance(), $this->uploads[1], "Image", "The sun");

    }



}

?>