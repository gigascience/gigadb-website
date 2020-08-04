<?php
 /**
 * Test access control and interface states to file uploading GigaDB endpoints
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/


class AuthorisedDatasetFilesUploadAction extends FunctionalTesting
{
    use BrowserSignInSteps;
    use BrowserPageSteps;
    use CommonDataProviders;
    use DatabaseSteps;

    /** @var string $url url of file upload endpoint to test access control on */
    public $url = "http://gigadb.dev/authorisedDataset/uploadFiles/id/100005" ;

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

        }
        catch (PDOException $e) {
            exit("Failed connecting to database:". $e->getMessage());
        }

        $this->setUpUsers($this->dbh, "Joy","Fox","user","joy_fox@gigadb.org");
        // setup file drop account and FUW identity for testing
        $this->filedrop_id = $this->setUpFiledropAccount(
            $this->dbhf->getPdoInstance(), 
            $this->doi
        );
        $this->setUpUserIdentity(
            $this->dbhf->getPdoInstance(), 
            $this->userEmail
        );
    }

    public function tearDown()
    {
        $this->tearDownUsers($this->dbh, "joy_fox@gigadb.org");
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

    public function testSubmitterCanPerformAction() {

        // set upload status to the correct UserUploadingData
        $this->setUpDatasetUploadStatus($this->dbh, "100005","UserUploadingData");

        //regular user who own that dataset is logged to gigadb
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
            "user@gigadb.org",
            "gigadb",
            "John's GigaDB Page");

        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull(
                $this->url, "GigaDB - UploadFiles AuthorisedDataset");

    }

    public function testOtherLoggedUsersCannotPerformAction() {

        // set upload status to the correct UserUploadingData
        $this->setUpDatasetUploadStatus($this->dbh, "100005","UserUploadingData");

        //Joy Fox user is logged to gigadb
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
            "joy_fox@gigadb.org",
            "gigadb",
            "Joy's GigaDB Page");

        $this->session->visit($this->url);
        $this->assertEquals(403, $this->session->getStatusCode());

    }

    public function testWrongStatusNoOneCanPerformAction() {

        // set upload status to something not UserUploadingData
        $this->setUpDatasetUploadStatus($this->dbh, "100005","Published");

        //regular user who own that dataset is logged to gigadb
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
            "user@gigadb.org",
            "gigadb",
            "John's GigaDB Page");

        $this->session->visit($this->url);
        $this->assertEquals(409, $this->session->getStatusCode());

    }

    public function testGuestsAreRedirected() {

        // set upload status to the correct UserUploadingData
        $this->setUpDatasetUploadStatus($this->dbh, "100005","UserUploadingData");

        $this->session->visit($this->url);
        $this->assertEquals("http://gigadb.dev/site/login", $this->session->getCurrentUrl());
    }

    public function testAuthorSeeUploaderAndNoUploadsExist() {
        // set upload status to the correct UserUploadingData
        $this->setUpDatasetUploadStatus($this->dbh, "100005","UserUploadingData");

        //regular user who own that dataset is logged to gigadb
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
            "user@gigadb.org",
            "gigadb",
            "John's GigaDB Page");

        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull(
                $this->url, "GigaDB - UploadFiles AuthorisedDataset");

        $this->assertNotFalse( 
            strstr($this->session->getPage()->getContent(), 
                    '<uploader identifier="100005" endpoint="/fileserver/" />'
            )
        );
        $this->assertNotFalse( 
            strstr($this->session->getPage()->getContent(), 
                    '<pager identifier="100005" uploads-exist="0"/>'
                )
        );        

    }

    public function testReturnAuthorSeeUploaderAndUploadsExist() {
        // setup of file uploads
        $files =  [
            ["doi" => "{$this->doi}", "name" =>"method.txt", "size" => 325352, "status"=> 0, "location" => "ftp://foobar", "description" => "", "extension" => "TEXT", "datatype"=>"Text"],
            ["doi" => "{$this->doi}", "name" =>"someFile.png", "size" => 5463434, "status"=> 0, "location" => "ftp://barfoo", "description" => "", "extension" => "PNG", "datatype"=>"Image"],
        ];
        $this->uploads = $this->setUpFileUploads(
            $this->dbhf->getPdoInstance(), $files, $this->filedrop_id
        );

        // set upload status to the correct UserUploadingData
        $this->setUpDatasetUploadStatus($this->dbh, $this->doi,"UserUploadingData");

        //regular user who own that dataset is logged to gigadb
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
            $this->userEmail,
            "gigadb",
            "John's GigaDB Page");

        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull(
                $this->url, "GigaDB - UploadFiles AuthorisedDataset");

        $this->assertNotFalse( 
            strstr($this->session->getPage()->getContent(), 
                    '<uploader identifier="100005" endpoint="/fileserver/" />'
            )
        );        
        $this->assertNotFalse( 
            strstr($this->session->getPage()->getContent(), 
                    '<pager identifier="100005" uploads-exist="2"/>'
            )
        );
    }

}

?>