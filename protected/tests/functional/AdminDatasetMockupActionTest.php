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

        $testDOI = "100005";
        $newMockupMessage = "New mockup ready at http://gigadb.test/dataset/mockup/";
        $curationMessage = "Mockup created at http://gigadb.test/dataset/mockup/";

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
        $this->session->getPage()->findAll('css',"a.mockup")[0]->click();
        $this->assertTrue($this->session->getPage()->hasContent($newMockupMessage));
        $this->session->visit($this->url);
        $this->assertTrue($this->session->getPage()->hasContent($curationMessage));
        

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
        $this->assertEquals(0, count($this->session->getPage()->findAll('css',"a.mockup")));   

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