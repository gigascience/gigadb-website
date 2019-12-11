<?php
 /**
 * Test access control to file uploading GigaDB endpoints
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

    /** @var PDO $dbh database handle */
    public $dbh;

    public function setUp()
    {
        parent::setUp();

        try {
            $this->dbh = new PDO("pgsql:host=".getenv("GIGADB_HOST").";dbname=".getenv("GIGADB_DB"), getenv("GIGADB_USER"), getenv("GIGADB_PASSWORD"));
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); //PHP warnings for SQL errors
        }
        catch (PDOException $e) {
            exit("Failed connecting to database:". $e->getMessage());
        }

        $this->setUpUsers($this->dbh, "Joy","Fox","user","joy_fox@gigadb.org");

    }

    public function tearDown()
    {
        $this->tearDownUsers($this->dbh, "joy_fox@gigadb.org");
        $this->setUpDatasetUploadStatus($this->dbh, "100005","Published");
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


}

?>