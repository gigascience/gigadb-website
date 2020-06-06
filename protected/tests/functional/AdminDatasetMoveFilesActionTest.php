<?php

/**
 * Test the MoveFilesAction
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class AdminDatasetMoveFilesActionTest extends FunctionalTesting
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

	public function setUp()
	{
		parent::setUp();

		$this->doi = "100005";

		try {
            $this->dbh_gigadb = new PDO("pgsql:host=".getenv("GIGADB_HOST").";dbname=".getenv("GIGADB_DB"), getenv("GIGADB_USER"), getenv("GIGADB_PASSWORD"));
            $this->dbh_gigadb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); //PHP warnings for SQL errors

            $this->dbh_fuw = new PDO("pgsql:host=".getenv("FUW_DB_HOST").";dbname=".getenv("FUW_DB_NAME"), getenv("FUW_DB_USER"), getenv("FUW_DB_PASSWORD"));
            $this->dbh_fuw->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); //PHP warnings for SQL errors
        }
        catch (PDOException $e) {
            exit("Failed connecting to database gigadb:". $e->getMessage());
        }

	}

	public function testMoveFiles()
	{
        $testInstructions = "instructions";
        // set upload status to the  Curation
        $this->setUpDatasetUploadStatus($this->dbh_gigadb, $this->doi,"Curation");
        // ensure there is a filedrop_account
        $filedropAccountId = $this->makeFiledropAccountRecord($this->dbh_fuw, $this->doi, $testInstructions);
        // create file uploads associated with that account
        $files =  [
                ["doi" => "{$this->doi}", "name" =>"somefile.txt", "size" => 325352, "status"=> 0, "location" => "ftp://foobar", "description" => "", "extension" => "TEXT", "datatype"=>"Text"],
                ["doi" => "{$this->doi}", "name" =>"anotherfile.png", "size" => 5463434, "status"=> 0, "location" => "ftp://barfoo", "description" => "", "extension" => "PNG", "datatype"=>"Image"],
                ["doi" => "{$this->doi}", "name" =>"shouldnotdisplay.png", "size" => 5463434, "status"=> 2, "location" => "ftp://barfoo", "description" => "", "extension" => "PNG", "datatype"=>"Image"],
            ];
        $this->uploads = $this->setUpFileUploads(
            $this->dbh_fuw, $files, $filedropAccountId
        );
        // setup URL
        $endpointUrl = "http://gigadb.dev/adminDataset/moveFiles/doi/{$this->doi}" ;

		//admin user logs in
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
            "admin@gigadb.org",
            "gigadb",
            "Admin");
        
        $this->session->visit($endpointUrl);
        $this->assertTrue($this->session->getPage()->hasContent("2 files are being moved to public ftp. It may take a moment"));
	}

    public function testMoveFilesWhenNoActiveFiles()
    {
        $testInstructions = "instructions";
        // set upload status to the  Curation
        $this->setUpDatasetUploadStatus($this->dbh_gigadb, $this->doi,"Curation");
        // ensure there is a filedrop_account
        $filedropAccountId = $this->makeFiledropAccountRecord($this->dbh_fuw, $this->doi, $testInstructions);
        // create file uploads associated with that account (archived, not active)
        $files =  [
                ["doi" => "{$this->doi}", "name" =>"somefile.txt", "size" => 325352, "status"=> 2, "location" => "ftp://foobar", "description" => "", "extension" => "TEXT", "datatype"=>"Text"],
                ["doi" => "{$this->doi}", "name" =>"anotherfile.png", "size" => 5463434, "status"=> 2, "location" => "ftp://barfoo", "description" => "", "extension" => "PNG", "datatype"=>"Image"],
                ["doi" => "{$this->doi}", "name" =>"shouldnotdisplay.png", "size" => 5463434, "status"=> 2, "location" => "ftp://barfoo", "description" => "", "extension" => "PNG", "datatype"=>"Image"],
            ];
        $this->uploads = $this->setUpFileUploads(
            $this->dbh_fuw, $files, $filedropAccountId
        );
        // setup URL
        $endpointUrl = "http://gigadb.dev/adminDataset/moveFiles/doi/{$this->doi}" ;

        //admin user logs in
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert(
            "admin@gigadb.org",
            "gigadb",
            "Admin");
        
        $this->session->visit($endpointUrl);
        $this->assertTrue($this->session->getPage()->hasContent("No files found to move"));
    }

	public function tearDown()
	{
   		$this->setUpDatasetUploadStatus($this->dbh_gigadb, $this->doi,"Published");
        $this->tearDownFileUploads(
            $this->dbh_fuw,
            $this->uploads
        );
        $this->tearDownFiledropAccount($this->dbh_fuw);
        $this->tearDownUserIdentity(
            $this->dbh_fuw,
            "user@gigadb.org"
        );
        $this->dbh_gigadb = null;
        $this->dbh_fuw = null;
        $this->doi = null;

		parent::tearDown();
	}
}
?>