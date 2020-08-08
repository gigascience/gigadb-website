<?php

 /**
 * Test the actionMockup action in AdminDatasetController.php
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class AdminDatasetMockupActionTest extends FunctionalTesting
{
    use BrowserSignInSteps;
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

        $this->url = "http://gigadb.dev/adminDataset/update/id/213" ;

    }

    public function tearDown()
    {
        $this->setUpDatasetUploadStatus($this->dbh_gigadb, "100005","Published");
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

    /** 
     * test that the following happen:
     * - dataset editing form has a mockup creation button
     * - random token created and saved
     * - curation log entry added
     *
     */
    public function testCreateNewMockupAccess() {

        try{
            $testDOI = "100005";
            $reviewerEmail = "reviewer@gigadb.dev";
            $monthsOfValidity = 6 ;
            $newMockupPreMessage = "Unique ($reviewerEmail), time-limited ($monthsOfValidity months) mockup url ready";
            $newMockupPostMessage = "/dataset/mockup/uuid";
            $curationMessage = "Mockup url created for $reviewerEmail for $monthsOfValidity months";

            // set upload status to the  Submitted
            $this->setUpDatasetUploadStatus($this->dbh_gigadb, "$testDOI","Submitted");
            // ensure there is a filedrop_account
            $filedropAccountId = $this->makeFiledropAccountRecord($this->dbh_fuw,"$testDOI", "");
            //admin user logs in
            $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
                "admin@gigadb.org",
                "gigadb",
                "Admin");
            
            $this->session->visit($this->url);
            $this->session->getPage()->clickLink("Generate mockup for reviewers");
            $this->session->getPage()->fillField("revieweremail", $reviewerEmail);
            $this->session->getPage()->selectFieldOption("monthsofvalidity", $monthsOfValidity);
            $this->session->getPage()->pressButton("Generate mockup");
            $this->assertTrue($this->session->getPage()->hasContent($newMockupPreMessage));
            $this->assertTrue($this->session->getPage()->hasContent($newMockupPostMessage));
            $this->session->visit($this->url);
            $this->assertTrue($this->session->getPage()->hasContent($curationMessage));

        }
        catch(Error $e) {
            throw new Exception($e);

        }
        

    }

    public function testNoMockupButtonIfWrongUploadStatus() {

        $testDOI = "100005";

        // set upload status to the  Submitted
        $this->setUpDatasetUploadStatus($this->dbh_gigadb, "$testDOI","Rejected");
        // ensure there is a filedrop_account
        $filedropAccountId = $this->makeFiledropAccountRecord($this->dbh_fuw,"$testDOI", "");
        //admin user logs in
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
            "admin@gigadb.org",
            "gigadb",
            "Admin");
        
        $this->session->visit($this->url);
        $this->assertNotTrue( $this->session->getPage()->hasLink("Generate mockup for reviewers") );
    }

    public function testCreateNewMockupInvalid() {

        $testDOI = "100005";

        // set upload status to the  Submitted
        $this->setUpDatasetUploadStatus($this->dbh_gigadb, "$testDOI","Rejected");
        // ensure there is a filedrop_account
        $filedropAccountId = $this->makeFiledropAccountRecord($this->dbh_fuw,"$testDOI", "");
        //admin user logs in
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
            "admin@gigadb.org",
            "gigadb",
            "Admin");
        $this->session->visit("http://gigadb.dev/adminDataset/mockup/id/789");
        $this->assertEquals("http://gigadb.dev/site/index",$this->session->getCurrentUrl());
    }

    public function testBackToUpdateFormIfPublished() {

        $testDOI = "100005";

        // set upload status to the  Submitted
        $this->setUpDatasetUploadStatus($this->dbh_gigadb, "$testDOI","Published");
        // ensure there is a filedrop_account
        $filedropAccountId = $this->makeFiledropAccountRecord($this->dbh_fuw,"$testDOI", "");
        //admin user logs in
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
            "admin@gigadb.org",
            "gigadb",
            "Admin");
        $this->session->visit("http://gigadb.dev/adminDataset/mockup/id/213");
        $this->assertEquals($this->url,$this->session->getCurrentUrl());
    }
}

?>