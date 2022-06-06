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


    /**
     * @Then I should see :tab tab with table on user view profile
     */
    public function iShouldSeeTabWithTableOnUserViewProfile($tab, \Behat\Gherkin\Node\TableNode $table)
    {
        if ("Your Uploaded Datasets" == $tab) {
            $colnames = array("DOI", "Title", "Subject", "Dataset Type", "Status", "Publication Date", "Modification Date", "File Count", "Operation");

            $this->I->iFollow($tab);
            foreach ($table as $row) {
                foreach ($colnames as $colname) {
                    codecept_debug("Doing: " . $colname);
                    if ($row[$colname] != "" & $colname != "Operation")
                        $this->I->seeInPageSource($row[$colname]);
                    elseif ($colname === "Operation") {
                        $this->I->seeLink("Update");
                        $this->I->seeLink("Delete");
                    }
                }
            }
        }
    }
}