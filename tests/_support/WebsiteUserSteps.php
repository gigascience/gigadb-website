<?php

/**
 * Class WebsiteUserSteps
 * Steps specific to user story for general public
 *
 * stubs copied from (after gherkin scenario steps are created):
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept g:snippets acceptance
 */
class WebsiteUserSteps extends \Codeception\Actor
{
    protected $I;

    function __construct(AcceptanceTester $I)
    {
        $this->I = $I;
    }

    /**
     * @Given I have not signed in
     */
    public function iHaveNotSignedIn()
    {
        $this->I->amOnPage('site/logout');
    }
}