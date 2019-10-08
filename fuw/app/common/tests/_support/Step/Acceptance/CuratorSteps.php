<?php
namespace common\tests\Step\Acceptance;

class CuratorSteps #extends \common\tests\AcceptanceTester
{
	protected $I;


	public function __construct(\common\tests\AcceptanceTester $I)
	{
	    $this->I = $I;
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
		$this->I->amOnUrl('http://gigadb.dev');
		$this->I->amOnPage('/site/login');
		$this->I->fillField(['name' => 'LoginForm[username]'], 'admin@gigadb.org');
		$this->I->fillField(['name' => 'LoginForm[password]'], 'gigadb');
		$this->I->click('Login');
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
	   $this->I->canSee($arg1);
	}
}