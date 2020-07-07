<?php

 /**
 * Test the AssignFTPBox action in AdminDatasetController.php
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class AdminDatasetAssignFTPBoxActionTest extends FunctionalTesting
{
    use BrowserSignInSteps;
    use DatabaseSteps;
    use FilesystemSteps;

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
        try {
            $this->setUpUsers($this->dbh_gigadb, "Erin","Dot","admin","erin_dot@gigadb.org");
            $this->setUpUserIdentity($this->dbh_fuw, "erin_dot@gigadb.org");
            $this->setUpDatasetUploadStatus($this->dbh_gigadb, "100005","AssigningFTPbox");
            $this->changeUserRole($this->dbh_gigadb, "user","admin@gigadb.org");
            $this->url = "http://gigadb.dev/adminDataset/assignFTPBox/id/100005" ;
        }
        catch(Error $e) {
            throw new Exception($e);
        }

    }

    public function tearDown()
    {
        try {
            $this->tearDownFiledropAccount($this->dbh_fuw);
            $this->tearDownUserIdentity($this->dbh_fuw, "erin_dot@gigadb.org");
            $this->tearDownUsers($this->dbh_gigadb, "erin_dot@gigadb.org");
            $this->setUpDatasetUploadStatus($this->dbh_gigadb, "100005","Published");
            $this->changeUserRole($this->dbh_gigadb, "admin","admin@gigadb.org");
            $this->removeDirectories("100005");
        }
        catch(Error $e) {
            throw new Exception($e);
        }
        $this->dbh_gigadb = null;
        $this->dbh_fuw = null;
        $this->url = null;


        parent::tearDown();
    }


    public function testCanCreateAccount() {

        try{
            $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
            "erin_dot@gigadb.org",
            "gigadb",
            "Admin");

             $this->session->visit($this->url);
            $this->assertFalse($this->session->getPage()->hasContent("An error occured. Drop box not created "));
            $this->assertEquals("http://gigadb.dev/adminDataset/admin/",$this->session->getCurrentUrl());

        }
        catch(Error $e) {
            throw new Exception($e);

        }
        

    }
}