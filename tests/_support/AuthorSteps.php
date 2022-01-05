<?php

/**
 * Class UserSteps
 * Steps specific to user story for users
 *
 * stubs copied from (after gherkin scenario steps are created):
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept g:snippets acceptance
 */
class AuthorSteps #extends \Codeception\Actor
{
    protected $I;

    function __construct(AcceptanceTester $I)
    {
        $this->I = $I;
    }

    /**
     * @Given I sign in as a user
     */
    public function iSignInAsAUser()
    {
        $this->I->amOnPage('/site/login');
        $this->I->fillField('LoginForm[username]', 'user@gigadb.org');
        $this->I->fillField('LoginForm[password]', 'gigadb');
        $this->I->iPressTheButton('Login');
    }

}