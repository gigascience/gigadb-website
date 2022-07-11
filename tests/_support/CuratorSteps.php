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
     * @Then I should see a file attribute table
     */
    public function iShouldSeeAFileAttributeTable(TableNode $fileAttributes)
    {
        foreach ($fileAttributes->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                codecept_debug($keys);
                continue;
            }
//            $this->I->seeLink($row[0],$row[1]);
        }
        
//        foreach ($table as $row) {
//            PHPUnit_Framework_Assert::assertTrue(
//                $this->minkContext->getSession()->getPage()->hasContent($row['Attribute Name'])
//            );
//            PHPUnit_Framework_Assert::assertTrue(
//                $this->minkContext->getSession()->getPage()->hasContent($row['Value'])
//            );
//            PHPUnit_Framework_Assert::assertTrue(
//                $this->minkContext->getSession()->getPage()->hasContent($row['Unit'])
//            );
//        }
    }

    /**
     * @Then I should see create new file attribute button
     *
     * Used to check button in /adminFile/update/id/$doi pages
     */
    public function iShouldSeeCreateNewFileAttributeButton()
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