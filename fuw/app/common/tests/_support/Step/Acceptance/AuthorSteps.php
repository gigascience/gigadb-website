<?php
namespace common\tests\Step\Acceptance;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use \backend\models\FiledropAccount;
use \Facebook\WebDriver\WebDriverElement;

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

     	$this->I->amConnectedToDatabase('fuwdb');
        $this->I->haveInDatabase('filedrop_account', [
			  'doi' => $doi,
			  'upload_login' => FiledropAccount::generateRandomString(6),
			  'upload_token' => FiledropAccount::generateRandomString(6),
			  'download_login' => FiledropAccount::generateRandomString(6),
			  'download_token' => FiledropAccount::generateRandomString(6),
			  'status' => FiledropAccount::STATUS_ACTIVE,
			]);
       	$this->I->amConnectedToDatabase(\Codeception\Module\Db::DEFAULT_DATABASE);

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
     * @When I attach the file :arg in the file drop panel
     */
     public function iAttachTheFileInTheFileDropPanel($arg1)
     {
        $this->I->waitForElementClickable('/html/body/div[3]/div[1]/div/div[2]/div/div[2]/input', 30);
        $this->I->resizeWindow(1440,900);
        $this->I->attachFile('/html/body/div[3]/div[1]/div/div[2]/div/div[2]/input',$arg1);
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

}