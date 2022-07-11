<?php

/**
 * Class CuratorSteps
 * steps specific to user story for curators
 *
 * stubs copied from (after gherkin scenario steps are created):
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept g:snippets acceptance
 */
class CuratorSteps extends \Codeception\Actor
{
    protected $I;
    protected $module;


    public function __construct(AcceptanceTester $I)
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
     * @Then I should see a view file table
     */
    public function iShouldSeeAViewFileTable(\Behat\Gherkin\Node\TableNode $viewFileTable)
    {
        foreach ($viewFileTable->getRows() as $index => $row) {
            // Check page contains expected View File table values
            $this->I->see($row[0], 'th');
            $this->I->see($row[1], 'td');
        }
    }

    /**
     * @Then I should see a file attribute table
     */
    public function iShouldSeeAFileAttributeTable(\Behat\Gherkin\Node\TableNode $fileAttributes)
    {
        foreach ($fileAttributes->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                $this->I->assertEquals($keys, ["Attribute Name", "Value", "Unit"], "File attributes table contain unexpected column names");
                continue;
            }
            // Check Attribute Name table data cell
            $this->I->see($row[0], 'td');
            // Check Value table cell data cell
            $this->I->see($row[1], 'td');
            // Check unit table cell data cell
            $this->I->see($row[2], 'td');
        }
    }

    /**
     * @Then I should see delete file attribute link button
     *
     * Used to check button in /adminFile/update/id/$doi pages
     */
    public function iShouldSeeDeleteFileAttributeLinkButton()
    {
        $actualButton = $this->I->grabTextFrom("//a[contains(@class, 'btn js-delete')]");
        $this->I->assertEquals($actualButton, "Delete");
    }

    /**
     * @Then I should not see delete file attribute link button
     *
     * Used to check button in /adminFile/update/id/$doi pages
     */
    public function iShouldNotSeeDeleteFileAttributeLinkButton()
    {
        $this->I->dontSeeLink('Delete');
    }

    /**
     * @Then I should see edit file attribute link button
     *
     * Used to check button in /adminFile/update/id/$doi pages
     */
    public function iShouldSeeEditFileAttributeLinkButton()
    {
        $actualButton = $this->I->grabTextFrom("//a[contains(@class, 'btn btn-edit js-edit')]");
        $this->I->assertEquals($actualButton, "Edit");
    }

    /**
     * @Then I should see create new file attribute link button
     *
     * Used to check button in /adminFile/update/id/$doi pages
     */
    public function iShouldSeeCreateNewFileAttributeLinkButton()
    {
        $actualButton = $this->I->grabTextFrom("//a[contains(@class, 'btn btn-attr')]");
        $this->I->assertEquals($actualButton, "New Attribute");
    }

    /**
     * @Then I should see the files:
     *4
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

    /**
     * @Given there is no user with email :email
     */
    public function thereIsNoUserWithEmail($email)
    {
        $dbConfig = json_decode(file_get_contents(dirname(__FILE__).'/../../protected/config/db.json'), true);
        shell_exec("psql -h {$dbConfig['host']} -U {$dbConfig['user']} -d {$dbConfig['user']} -c \"DELETE FROM gigadb_user WHERE email='$email'\"");
    }

    /**
     * @Given I have signed in as admin
     */
    public function iHaveSignedInAsAdmin()
    {
        $this->I->amOnUrl('http://gigadb.test');
        $this->I->amOnPage('/site/login');
        $this->I->fillField(['name' => 'LoginForm[username]'], 'admin@gigadb.org');
        $this->I->fillField(['name' => 'LoginForm[password]'], 'gigadb');
        $this->I->click('Login');
        $this->I->waitForText("Home",10);
    }


}