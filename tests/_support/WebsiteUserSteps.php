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

    /**
     * @Given I have set the page size setting to :pageSize
     */
    public function iHaveSetThePageSizeSettingTo($pageSize)
    {
        $this->I->amOnPage("/dataset/100006");
        $this->I->click("Files");
        $this->I->click("#files_table_settings");
        $this->I->wait(1);
        $this->I->selectOption("form[name=myFilesSettingform] select[name=pageSize]",$pageSize);
        $this->I->click('#save-files-settings');
        $this->I->wait(1);
    }

    /**
     * @Then I should see the files:
     *
     */
    public function iShouldSeeTheFiles(\Behat\Gherkin\Node\TableNode $files)
    {
        foreach ($files->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            $this->I->seeLink($row[0],$row[1]);
        }
    }


}