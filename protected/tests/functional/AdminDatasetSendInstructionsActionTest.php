<?php
 /**
 * Test the jobs performed by SendInstructionsAction.php
 *
 * Set up required:
 * - filedrop_account record in FUW database
 * - dataset with a UserUploadingData status
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/


class AdminDatasetSendInstructionsActionTest extends FunctionalTesting
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

        $this->url = "http://gigadb.dev/adminDataset/sendInstructions" ;

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

    public function testAddInstructionsToCurationLogAndCreateAuthorisedIdentity() {

        $testDOI = "100142";
        $testInstructions = "foo bar is test insructions";
        // set upload status to the  UserUploadingData
        $this->setUpDatasetUploadStatus($this->dbh_gigadb, "$testDOI","UserUploadingData");
        // ensure there is a filedrop_account
        $filedropAccountId = $this->makeFiledropAccountRecord($this->dbh_fuw,"$testDOI", $testInstructions);
        //admin user logs in
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
            "admin@gigadb.org",
            "gigadb",
            "Admin");
        //and send email instructions
        $this->session->visit($this->url."/id/$testDOI/fid/$filedropAccountId");

        // Check the confirmation message
        $this->assertTrue($this->session->getPage()->hasContent("Instructions sent to test+336@gigasciencejournal.com."));
        
        // check there is a new curation_log entry
        $this->session->visit("http://gigadb.dev/adminDataset/update/id/200");
        $this->assertTrue($this->session->getPage()->hasContent($testInstructions));
        
        // check an identity is created in FUW database to authorize authors workflow
        $this->assertUserIdentity($this->dbh_fuw, "test+336@gigasciencejournal.com");

        $testDOI = null;
        $testInstructions = null;

    }


}

?>