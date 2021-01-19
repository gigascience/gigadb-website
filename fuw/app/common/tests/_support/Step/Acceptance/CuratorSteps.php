<?php
namespace common\tests\Step\Acceptance;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use \FileUploadServicer;
use \Email\Parse;
use \Behat\Gherkin\Node\TableNode;
use common\models\Upload;
use Yii;

class CuratorSteps #extends \common\tests\AcceptanceTester
{
	protected $I;


	public function __construct(\common\tests\AcceptanceTester $I)
	{
	    $this->I = $I;
	}

	/**
     * @Given filedrop account for DOI :arg1 doesn't exist
     */
     public function filedropAccountForDOIDoesntExist($arg1)
     {

     	$adapter = new Local("/var");
		$fs = new Filesystem($adapter);

     	$fs->deleteDir("incoming/ftp/$arg1");
     	$fs->deleteDir("repo/$arg1");
     	$fs->deleteDir("private/$arg1");
     }

	/**
     * @Given there is a user :firstname :lastname
     */
   	public function thereIsAUser($firstname, $lastname)
     {
        $this->I->haveInDatabase('gigadb_user', [
			  'email' => "${firstname}_${lastname}@gigadb.org",
			  'password' => 'foobar',
			  'first_name' => "$firstname",
			  'last_name' => "$lastname",
			  'role' => 'user',
			  'is_activated' => true,
			  'newsletter' => false,
			  'previous_newsletter_state' => false,
			  'username' => "${firstname}_${lastname}",
			]);
    }
    /**
     * @Given a dataset with DOI :doi owned by user :firstname :lastname has status :status
     */
    public function aDatasetWithDOIOwnedByUserHasStatus($doi, $firstname, $lastname, $status)
    {
    	$submitter_id = $this->I->grabFromDatabase('gigadb_user', 'id', array('username' => "${firstname}_${lastname}"));
         $this->I->haveInDatabase('dataset', [
			  'submitter_id' => $submitter_id,
			  'identifier' => "$doi",
			  'title' => "Dataset Fantastic",
			  'description' => "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo",
			  'dataset_size' => 3453534634,
			  'ftp_site' => 'ftp://data.org',
			  'upload_status' => "$status",
			]);
    }


	/**
	 * @Given I sign in as an admin
	 */
	public function iSignInAsAnAdmin()
	{
		$this->I->amOnUrl('http://gigadb.test');
		$this->I->amOnPage('/site/login');
		$this->I->fillField(['name' => 'LoginForm[username]'], 'Ben_Hur@gigadb.org');
		$this->I->fillField(['name' => 'LoginForm[password]'], 'gigadb');
		$this->I->click('Login');
        $this->I->waitForText("Home",10);
	}

	/**
	 * @Given a dataset has been uploaded with temporary DOI :arg1 by user :arg2
	 */
	public function aDatasetHasBeenUploadedWithTemporaryDOIByUser($arg1, $arg2)
	{
	   throw new \Codeception\Exception\Incomplete("Step `a dataset has been uploaded with temporary DOI :arg1 by user :arg2` is not defined");
	}

	/**
	 * @Given the uploaded dataset has status :arg1
	 */
	public function theUploadedDatasetHasStatus($arg1)
	{
	   throw new \Codeception\Exception\Incomplete("Step `the uploaded dataset has status :arg1` is not defined");
	}

	/**
	 * @Given I go to :arg1
	 */
	public function iGoTo($arg1)
	{
	   $this->I->amOnPage($arg1);
	}


/**
     * @Given reference data for Attribute for Unit is created for
     */
     public function referenceDataForAttributeForUnitIsCreatedFor(TableNode $files)
     {
        $this->I->amConnectedToDatabase('default');
        foreach ($files->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }

            if("attribute" === $row[0]) {
                if (!$this->I->grabFromDatabase("public.{$row[0]}",'id',['attribute_name' => $row[1]])) { 
                $this->I->haveInDatabase("public.{$row[0]}",["attribute_name" => $row[1] ] );
                }
            }
            // elseif("sample" === $row[0]) {
            //     if ( !$this->I->grabFromDatabase('public.species', 'id', array('id' => 1128856)) ) {
            //         $this->I->haveInDatabase("public.species", ["id" => 1128856, "tax_id" => "-1", "scientific_name" => "None assigned" ]);
            //     }
            //     // $this->I->haveInDatabase("public.{$row[0]}",["species_id" => 1128856 ,"name" => $row[1] ] );

            // }            
            elseif("unit" === $row[0])  {
                if(!$this->I->grabFromDatabase("public.{$row[0]}",'id',['name' => $row[1]])) {
                    $this->I->haveInDatabase("public.{$row[0]}",["id" => $row[2], "name" => $row[1] ] );
                }
            }
        }
     }


	/**
	 * @When I press :arg1
	 */
	public function iPress($arg1)
	{
	   $this->I->click($arg1);
	}

	/**
	 * @Then the response sould contain :arg1
	 */
	public function theResponseSouldContain($arg1)
	{
	   $this->I->canSee($arg1);
	}

	/**
	 * @Then I should see a :arg1 button
	 */
	public function iShouldSeeAButton($arg1)
	{
		$this->I->seeElement("//img[@alt='$arg1']");
	}

    /**
     * @Then I should see a :arg1 link
     */
     public function iShouldSeeALink($arg1)
     {
        $this->I->canSeeLink($arg1);
     }

	/**
     * @Then I should see a :arg1 form text area
     */
     public function iShouldSeeAFormTextArea($arg1)
     {
        $this->I->seeElement('textarea',["name" => $arg1]);
     }

	/**
     * @When I wait for modal window :arg1
     */
     public function iWaitForModalWindow($arg1)
     {
        $this->I->waitForElement('#editInstructions', 10);
     }

	/**
     * @When I wait :arg1 seconds
     */
     public function iWaitSeconds($arg1)
     {
         $this->I->wait($arg1);
     }

     /**
     * @When I fill in :arg1 text area with :arg2
     */
     public function iFillInTextAreaWith($arg1, $arg2)
     {
        $this->I->fillField(['name' => $arg1], $arg2);
     }

     /**
     * @Then I should see :arg1
     */
     public function iShouldSee($arg1)
     {
        $this->I->canSee($arg1);
     }

	/**
     * @Then I should not see a :arg1 link
     */
     public function iShouldNotSeeALink($arg1)
     {
        $this->I->cantSeeLink($arg1);
     }

     /**
     * @Then I am on :arg1
     */
     public function iAmOn($arg1)
     {
        $this->I->amOnUrl("http://gigadb.test".$arg1);
     }

    /**
     * @Given the status of the dataset with DOI :arg1 has changed to :arg2
     */
     public function theStatusOfTheDatasetWithDOIHasChangedTo($doi, $status)
     {
     	$webClient = new \GuzzleHttp\Client();
        $fileUploadSrv = new \FileUploadService([
            "tokenSrv" => new \TokenService([
                                  'jwtTTL' => 3600,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => new \UserDAO(),
                                  'dt' => new \DateTime(),
                                ]),
            "webClient" => $webClient,
            "requester" => \User::model()->findByAttribute(["email"=>"joy_fox@gigadb.org"]),
            "identifier"=> $doi,
            "dataset" => new \DatasetDAO(["identifier" => $doi]),
            "dryRunMode"=>false,
            ]);

        $datasetUpload = new \DatasetUpload(
            $fileUploadSrv->dataset, 
            $fileUploadSrv, 
            Yii::$app->params['dataset_upload']
        );
        $datasetUpload->setStatusToDataAvailableForReview("changed from test scenario");
     }

    /**
     * @When all files have been moved to the public ftp repository
     *
     * this step need to exercice the backend job triggered by completion of files move
     */
     public function allFilesHaveBeenMovedToThePublicFtpRepository()
     {
        // All the jobs should be marked as done
        $this->I->amOnUrl("http://fuw-admin-api/monitor/jobs");
        $this->I->amOnPage('/monitor/jobs');
        $this->I->cantSeeInPageSource('<div class="job-status">Buried</div>');
        $this->I->canSeeInPageSource('<div class="job-status">Done</div>');
     }


	/**
     * @When I press :arg1 for dataset :arg2
     */
     public function iPressForDataset($arg1, $arg2)
     {
        $this->I->click("tr.dataset-$arg2 td.button-column a[title='$arg1']");
     }

	/**
     * @When change the status to :arg1
     */
    public function changeTheStatusTo($status)
    {
    	/* //*[@id="Dataset_upload_status"] */
        $this->I->selectOption('//*[@id="Dataset_upload_status"]', $status);
        $this->I->click("Save");
    }

	/**
     * @Then An email is sent to :arg1
     */
     public function anEmailIsSentTo($recipient)
     {
        if("Curators" === $recipient) {
            $email = Yii::$app->params["dataset_upload"]["curators_email"];
        }
        else {
            $email = $recipient;
        }
     	exec("ls -1rt /app/frontend/runtime/mail", $output, $error);
     	if(count($output) > 0 ) {
     		$lastEmail = array_pop($output);
     		$parser = new \Phemail\MessageParser();
			$message = $parser->parse("/app/frontend/runtime/mail/$lastEmail");
			$emailDate = $message->getHeaderValue('date');
			$emailTimestamp = strtotime($emailDate);
			$currentDate = new \DateTime('NOW');
			$currentTimestamp = $currentDate->format('U');
			//test that email was received within the last 10 secs
			$this->I->assertTrue(abs($emailTimestamp-$currentTimestamp)<30);
			$addresses = Parse::getInstance()->parse($message->getHeaderValue('to'));
			$this->I->assertEquals($email, $addresses["email_addresses"][0]["address"]);
     	}

     }

    /**
     * @When I select :arg1 for :arg2
     */
     public function iSelectFor($arg1, $arg2)
     {
        $this->I->selectOption("input[name=$arg2]", $arg1);
     }

    /**
     * @Then files exist at new location :dest
     */
     public function filesExistAtNewLocation(string $dest, TableNode $files)
     {
        foreach ($files->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }

            $this->I->assertTrue( \Yii::$app->fs->has("$dest/{$row[0]}/{$row[1]}") );
        }
     }

      /**
     * @Then uploads are flagged as :arg1
     */
     public function uploadsAreFlaggedAs($arg1, TableNode $files)
     {
        $this->I->amConnectedToDatabase('fuwdb');
        foreach ($files->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            $this->I->seeInDatabase("public.upload",["doi" => $row[0], "name" => $row[1], "status" => Upload::STATUS_SYNCHRONIZED ] );
        }
        $this->I->amConnectedToDatabase(\Codeception\Module\Db::DEFAULT_DATABASE);
     }



}