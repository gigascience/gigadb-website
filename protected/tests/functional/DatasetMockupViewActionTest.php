<?php

 /**
 * Test the MockupView action in DatasetController.php
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class DatasetMockupViewActionTest extends FunctionalTesting
{
    use BrowserSignInSteps;
    use DatabaseSteps;

    /** @var string $url url of endpoint to test */
    public $url;

    /** @var PDO $dbh_gigadb database handle */
    public $dbh_gigadb;
    /** @var PDO $dbh_fuw database handle */
    public $dbh_fuw;

    /** @var string $doi DOI to use for testing */
    private $doi;

    /** @var string $url_fragment UUID generated for mockupUrl test data */
    private $url_fragment; 
    private $mockupUrlId;

    public function setUp()
    {
        parent::setUp();

        $this->url = "http://gigadb.dev/dataset/mockup" ;
        $this->doi = "100005";

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

            list($this->mockupUrlId, $this->url_fragment) = $this->setUpMockupUrl(
                $this->dbh_fuw,
                "someone@foobar.test",
                3,
                $this->doi
            );
            $this->setUpUserIdentity(
            $this->dbh_fuw,
            "someone@foobar.test"
        );

        }
        catch (PDOException $e) {
            exit("Failed connecting to database fuw:". $e->getMessage());
        }

        

    }

    public function tearDown()
    {
        $this->setUpDatasetUploadStatus($this->dbh_gigadb, $this->doi,"Published");
        $this->tearDownFiledropAccount($this->dbh_fuw);
        $this->tearDownUserIdentity(
            $this->dbh_fuw,
            "user@gigadb.org"
        );
         $this->tearDownUserIdentity(
            $this->dbh_fuw,
            "someone@foobar.test"
        );
        $this->tearDownMockupUrl(
            $this->dbh_fuw,
            $this->url_fragment
        );
        $this->dbh_gigadb = null;
        $this->dbh_fuw = null;
        $this->url = null;


        parent::tearDown();
    }

    /**
     * If url fragment exists but the status is incorrect, we redirect to the dataset page
     */
    public function testRightFragmentIncorrectdStatus() {

        // set upload status to the  Submitted
        $this->setUpDatasetUploadStatus($this->dbh_gigadb, $this->doi,"Rejected");
        // ensure there is a filedrop_account
        $filedropAccountId = $this->makeFiledropAccountRecord($this->dbh_fuw, $this->doi, "");

        $this->session->visit($this->url."/uuid/".$this->url_fragment);
        $this->assertEquals("http://gigadb.dev/dataset/".$this->doi,$this->session->getCurrentUrl());
    }

    /**
     * If url fragment does not exist, redirect to error page
     */
    public function testIncorrectFragment() {

        // set upload status to the  Submitted
        $this->setUpDatasetUploadStatus($this->dbh_gigadb, $this->doi,"Rejected");
        // ensure there is a filedrop_account
        $filedropAccountId = $this->makeFiledropAccountRecord($this->dbh_fuw, $this->doi, "");

        $this->session->visit($this->url."/uuid/1ee9aa1b-6510-4105-92b9-7171bb2f3089");
        $this->assertEquals(404,$this->session->getStatusCode());
    }  

    /**
     * If url fragment exists and dataset status is "Submitted", show the mockup trhe page
     */
    public function testShowMockup() {

        // set upload status to the  Submitted
        $this->setUpDatasetUploadStatus($this->dbh_gigadb, $this->doi,"Submitted");
        // ensure there is a filedrop_account
        $filedropAccountId = $this->makeFiledropAccountRecord($this->dbh_fuw, $this->doi, "");

        $this->session->visit($this->url."/uuid/".$this->url_fragment);
        $this->assertEquals(200,$this->session->getStatusCode());
        $this->assertEquals($this->url."/uuid/".$this->url_fragment,$this->session->getCurrentUrl());
    } 

}

?>