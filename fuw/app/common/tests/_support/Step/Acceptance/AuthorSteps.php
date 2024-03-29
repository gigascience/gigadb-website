<?php
namespace common\tests\Step\Acceptance;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use \backend\models\FiledropAccount;
use \Facebook\WebDriver\WebDriverElement;
use \Behat\Gherkin\Node\TableNode;
use \Codeception\Util\ActionSequence;
use Yii;

class AuthorSteps #extends \common\tests\AcceptanceTester
{
	protected $I;


	public function __construct(\common\tests\AcceptanceTester $I)
	{
	    $this->I = $I;
	}

	 /**
     * @Given filedrop account for DOI :doi does exist
     */
     public function filedropAccountForDOIDoesExist($doi)
     {

        // Database record
     	$this->I->amConnectedToDatabase('fuwdb');
        $this->I->haveInDatabase('filedrop_account', [
			  'doi' => $doi,
			  'upload_login' => Yii::$app->security->generateRandomString(6),
			  'upload_token' => Yii::$app->security->generateRandomString(6),
			  'download_login' => Yii::$app->security->generateRandomString(6),
			  'download_token' => Yii::$app->security->generateRandomString(6),
			  'status' => FiledropAccount::STATUS_ACTIVE,
			]);
       	$this->I->amConnectedToDatabase(\Codeception\Module\Db::DEFAULT_DATABASE);

        //Filesystem objects
        $filedrop = new FiledropAccount();
        $filedrop->doi = $doi;
        $filedrop->prepareAccountSetFields($doi);
     }

 	/**
     * @Given I sign in as the user :firstname :lastname
     */
     public function iSignInAsTheUser($firstname, $lastname)
	{
		$this->I->amOnUrl('http://gigadb.test');
		$this->I->amOnPage('/site/login');
		$this->I->fillField(['name' => 'LoginForm[username]'], strtolower("${firstname}_${lastname}@gigadb.org"));
		$this->I->fillField(['name' => 'LoginForm[password]'], 'gigadb');
		$this->I->click('Login');
	}

    /**
     * @Given The user :firstname :lastname is registered as authorised user in the API
     */
     public function theUserIsRegisteredAsAuthorisedUserInTheAPI($firstname, $lastname)
     {
        // Database record
        $this->I->amConnectedToDatabase('fuwdb');
        $this->I->haveInDatabase('public.user', [
              'username' => "{$firstname}_{$lastname}",
              'auth_key' => Yii::$app->security->generateRandomString(6),
              'password_hash' => Yii::$app->security->generateRandomString(6),
              'email' => strtolower("${firstname}_${lastname}@gigadb.org"),
              'created_at' => date("U"),
              'updated_at' => date("U"),
            ]);
        $this->I->amConnectedToDatabase(\Codeception\Module\Db::DEFAULT_DATABASE);
     }

 	/**
     * @Then the :arg1 tab is active
     */
     public function theTabIsActive($arg1)
     {
        $this->I->seeInSource('<a href="#submitted" aria-controls="submitted" role="tab" data-toggle="tab" aria-expanded="true">Your Uploaded Datasets</a>');
     }

	/**
	 * @Given I go to :arg1
	 */
	public function iGoTo($arg1)
	{
	   $this->I->amOnPage($arg1);
	}


    /**
     * @Given there are files uploaded by ftp
     */
     public function thereAreFilesUploadedByFtp(TableNode $files)
     {
        foreach ($files->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            // row[0] -> file name
            // row[1] -> doi
            $this->I->writeToFile("/var/incoming/ftp/{$row[1]}/{$row[0]}", Yii::$app->security->generateRandomString(3));
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
     * @Then I should see a :arg1 icon button
     */
    public function iShouldSeeAIconButton($arg1)
    {
        $this->I->seeElement("//img[@alt='$arg1']");
    }

    /**
     * @Then I should see a :arg1 button
     */
    public function iShouldSeeAButton($arg1)
    {
        $this->I->seeElement("//form/*[@type='submit']");
    }

    /**
     * @Then I should see a :arg1 link
     */
     public function iShouldSeeALink($arg1)
     {
        $this->I->canSeeLink($arg1);
     }

    /**
     * @Then I should see a :arg1 link to :arg2
     */
     public function iShouldSeeALinkTo($arg1, $arg2)
     {
        $this->I->canSeeLink($arg1, $arg2);
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
     * @When I attach the file :arg in the file drop panel
     */
     public function iAttachTheFileInTheFileDropPanel($arg1)
     {
        // $this->I->waitForElementClickable('/html/body/div[3]/div[1]/div/div[2]/div/div[2]/input', 30);
        $this->I->waitForElement('.uppy-Dashboard-input', 30);
        $this->I->resizeWindow(1440,900);
        $this->I->attachFile('.uppy-Dashboard-input',$arg1);
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
     * @Then I should see the file upload completed
     */
     public function iShouldSeeTheFileUploadCompleted()
     {
        $this->I->waitForElementChange('.uppy-StatusBar-progress',function(WebDriverElement $el) {
            return "100" === $el->getAttribute("aria-valuenow");
        }, 30);
     }

    /**
     * @Then I should see the files in the database
     */
     public function iShouldSeeTheFilesInTheDatabase(TableNode $files)
     {
        foreach ($files->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            $this->I->performInDatabase('fuwdb', ActionSequence::build()->seeInDatabase('upload', array_combine($keys, $row))
            );
        }
     }




    /**
     * @Given I have uploaded files for dataset
     */
     // public function iHaveUploadedFilesForDataset(TableNode $files)
     // {
     //    $this->I->amOnUrl('http://gigadb.test/user/view_profile#submitted');
     //    $this->I->seeInSource('<a href="#submitted" aria-controls="submitted" role="tab" data-toggle="tab" aria-expanded="true">Your Uploaded Datasets</a>');
     //    $this->I->click('Upload Dataset Files');
     //    $this->I->resizeWindow(1440,900);
     //    foreach ($files->getRows() as $index => $row) {
     //        if ($index === 0) { // first row to define fields
     //            $keys = $row;
     //            continue;
     //        }
     //        if ($index > 1) {
     //            $this->I->click('Add more');
     //        }
     //        $this->I->waitForElement('.uppy-Dashboard-input', 30);
     //        $this->I->attachFile('.uppy-Dashboard-input',$row[1]);
     //    }
     //    $this->I->click('Upload '+$index+' files');

     // }

    /**
     * @Then I should see list of files
     */
     public function iShouldSeeListOfFiles(TableNode $files)
     {
        foreach ($files->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            $this->I->seeInSource("<td>{$row[1]}</td>");
            $this->I->seeInSource("<option>{$row[2]}</option>");
            $this->I->seeInSource("<td>{$row[3]}</td>");
            $this->I->seeInSource("<td>{$row[4]}</td>");
        }
     }

    /**
     * @Then I should see when hovering file names
     */
     public function iShouldSeeWhenHoveringFileNames(TableNode $files)
     {
        foreach ($files->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            $this->I->seeInSource('<td><span data-toggle="tooltip" data-placement="bottom" title="md5:'.$row[1].'">'.$row[0].'</span>');
        }

     }

    /**
     * @When I add new sample :arg1
     */
     public function iAddNewSample($arg1)
     {
        $this->I->click("New Sample");
        $this->I->fillField(['id' => "new-sample-field"], $arg1);
        $this->I->pressKey("#new-sample-field",\Facebook\WebDriver\WebDriverKeys::RETURN_KEY);
     }

    /**
     * @When I press the close button
     */
     public function iPressTheCloseButton()
     {
        $this->I->click(".el-dialog__close");
     }

}