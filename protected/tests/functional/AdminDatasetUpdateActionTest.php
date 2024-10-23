<?php
 /**
 * Test the jobs performed by actionUpdate in AdminDatasetController.php
 *
 * Set up required:
 * - filedrop_account record in FUW database
 * - dataset with a UserUploadingData status
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/


class AdminDatasetUpdateActionTest extends FunctionalTesting
{
    use BrowserSignInSteps;
    use BrowserPageSteps;
    use CommonDataProviders;
    use DatabaseSteps;

    /** @var string $url url of endpoint to test */
    public $url;

    /** @var PDO $dbh_gigadb database handle */
    public $dbh_gigadb;
    /** @var PDO $dbh_fuw database handle */
    public $dbh_fuw;

    public function setUp()
    {
        parent::setUp();

        try {
            $this->dbh_gigadb = new PDO("pgsql:host=".getenv("GIGADB_HOST").";dbname=".getenv("GIGADB_DB"), getenv("GIGADB_USER"), getenv("GIGADB_PASSWORD"));
            $this->dbh_gigadb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); //PHP warnings for SQL errors
        }
        catch (PDOException $e) {
            exit("Failed connecting to database gigadb:". $e->getMessage());
        }

        try {
            $this->dbh_fuw = new PDO("pgsql:host=".getenv("FUW_DB_HOST").";dbname=".getenv("FUW_DB_NAME"), getenv("FUW_DB_USER"), getenv("FUW_DB_PASSWORD"));
            $this->dbh_fuw->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); //PHP warnings for SQL errors
        }
        catch (PDOException $e) {
            exit("Failed connecting to database fuw:". $e->getMessage());
        }

        $this->url = "http://gigadb.dev/adminDataset/update/id/200" ;

    }

    public function tearDown()
    {
        $this->setUpDatasetUploadStatus($this->dbh_gigadb, "100142","Published");
        $this->tearDownFiledropAccount($this->dbh_fuw);
        $this->tearDownUserIdentity(
            $this->dbh_fuw,
            "user@gigadb.org"
        );
        $this->dbh_gigadb = null;
        $this->dbh_fuw = null;
        $this->url = null;


        parent::tearDown();
    }

    public function testSetUploadToRejected() {

        $testDOI = "100142";
        $curationEntry = "Status changed to Rejected";
        // set upload status to the  UserUploadingData
        $this->setUpDatasetUploadStatus($this->dbh_gigadb, "$testDOI","DataAvailableForReview");
        // ensure there is a filedrop_account
        $filedropAccountId = $this->makeFiledropAccountRecord($this->dbh_fuw,"$testDOI", $curationEntry);
        //admin user logs in
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
            "admin@gigadb.org",
            "gigadb",
            "Admin");
        
        $this->session->visit($this->url);
        $this->session->getPage()->selectFieldOption("Dataset_upload_status", "Rejected");
        $this->session->getPage()->pressButton("Save");
        $this->session->visit($this->url);
        $this->assertTrue($this->session->getPage()->hasContent($curationEntry));
        

    }

    public function testSetUploadToSubmitted() {

        $testDOI = "100142";
        $curationEntry = "Status changed to Submitted";
        // set upload status to the  UserUploadingData
        $this->setUpDatasetUploadStatus($this->dbh_gigadb, "$testDOI","DataAvailableForReview");
        // ensure there is a filedrop_account
        $filedropAccountId = $this->makeFiledropAccountRecord($this->dbh_fuw,"$testDOI", $curationEntry);
        //admin user logs in
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
            "admin@gigadb.org",
            "gigadb",
            "Admin");
        
        $this->session->visit($this->url);
        $this->session->getPage()->selectFieldOption("Dataset_upload_status", "Submitted");
        $this->session->getPage()->pressButton("Save");
        $this->session->visit($this->url);
        $this->assertTrue($this->session->getPage()->hasContent($curationEntry));
        

    }

    public function testSetUploadToPublished() {

        $testDOI = "100142";
        $curationEntry = "Status changed to Published";
        // set upload status to the  UserUploadingData
        $this->setUpDatasetUploadStatus($this->dbh_gigadb, "$testDOI","Private");
        // ensure there is a filedrop_account
        $filedropAccountId = $this->makeFiledropAccountRecord($this->dbh_fuw,"$testDOI", $curationEntry);
        //admin user logs in
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
            "admin@gigadb.org",
            "gigadb",
            "Admin");
        
        $this->session->visit($this->url);
        $this->session->getPage()->selectFieldOption("Dataset_upload_status", "Published");
        $this->session->getPage()->pressButton("Save");
        $this->session->visit($this->url);
        $this->assertTrue($this->session->getPage()->hasContent($curationEntry));
        

    }


}

?>
