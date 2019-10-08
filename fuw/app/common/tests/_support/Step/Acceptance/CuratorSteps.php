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
	 * @Given I sign in as an admin
	 */
	public function iSignInAsAnAdmin()
	{
	   // $this->minkContext->visit("/site/login");
    //      $this->minkContext->fillField("LoginForm_username", $this->admin_login);
    //      $this->minkContext->fillField("LoginForm_password", $this->admin_password);
    //      $this->minkContext->pressButton("Login");

    //      $this->minkContext->assertResponseContains("Admin");
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
	   throw new \Codeception\Exception\Incomplete("Step `I go to :arg1` is not defined");
	}

	/**
	 * @When I press :arg1
	 */
	public function iPress($arg1)
	{
	   throw new \Codeception\Exception\Incomplete("Step `I press :arg1` is not defined");
	}

	/**
	 * @Then the response sould contain :arg1
	 */
	public function theResponseSouldContain($arg1)
	{
	   throw new \Codeception\Exception\Incomplete("Step `the response sould contain :arg1` is not defined");
	}

	/**
	 * @Then I should see a :arg1 button
	 */
	public function iShouldSeeAButton($arg1)
	{
	   throw new \Codeception\Exception\Incomplete("Step `I should see a :arg1 button` is not defined");
	}
}