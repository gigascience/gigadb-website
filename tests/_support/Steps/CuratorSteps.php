<?php

namespace Steps;

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


    public function __construct(\AcceptanceTester $I)
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
        $actualButton = $this->I->grabTextFrom("//button[contains(@data-test, 'delete-attr-btn')]");
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
        $actualButton = $this->I->grabTextFrom("//button[contains(@data-test, 'edit-attr-btn')]");
        $this->I->assertEquals($actualButton, "Edit");
    }

    /**
     * @Then I should see create new file attribute link button
     *
     * Used to check button in /adminFile/update/id/$doi pages
     */
    public function iShouldSeeCreateNewFileAttributeLinkButton()
    {
        $actualButton = $this->I->grabTextFrom("//button[contains(@data-test, 'new-attr-btn')]");
        $this->I->assertEquals($actualButton, "Show New Attribute Fields");
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
            $this->I->seeLink($row[0], $row[1]);
        }
    }

    /**
     * @Given there is no user with email :email
     */
    public function thereIsNoUserWithEmail($email)
    {
        $dbConfig = json_decode(file_get_contents(dirname(__FILE__) . '/../../../protected/config/db.json'), true);
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
        $this->I->waitForText("Home", 10);
    }

    /**
     * @When I click on keywords field
     */
    public function iClickOnKeywordsField()
    {
//        $this->I->click('keywords');
        $this->I->click(['css' => '.placeholder']);
    }

    /**
     * @When I fill in keywords fields with :keyword
     */
    public function iFillInKeywordsFieldsWith($keyword)
    {
        $this->I->type($keyword);
        $this->I->clickWithLeftButton(['css' => '#urltoredirect']);
        $this->I->waitForText($keyword, 5, ".tag-editor-tag");
    }

    /**
     * @Then I should see the application version
     */
     public function iShouldApplicationVersion()
     {
        $versionText = $this->I->grabTextFrom("/html/body/footer/div/div/div[2]/ul/li/a");
        $this->I->assertStringContainsString("Version: ", $versionText);
        $semVerPattern = "/^Version: (.*)$/";
        $this->I->assertRegExp($semVerPattern,$versionText);
     }

    /**
     * @Given I make an update to the non-public dataset :doi's :changeType in the admin pages
     */
    public function iMakeAnUpdateToTheNonpublicDatasetsInTheAdminPages($doi, $changeType)
    {
        $dataset_id = $this->I->grabFromDatabase('dataset', 'id', array('identifier' => $doi));
        switch ($changeType) {
            case "dataset metadata":
                $this->I->updateInDatabase('dataset', ['description' => "lorem ipsum from automated tests"], ['id' => $dataset_id]);
                break;
            case "sample metadata":
                $this->I->updateInDatabase('sample_attribute', ['value' => 'value from automated tests'],['sample_id' => 154,'attribute_id' => 376 ]);
                break;
            case "file metadata":
                $this->I->updateInDatabase('file', ['description' => 'description from automated tests'],['id' => 95366]);
                break;
            case "author metadata":
                $this->I->haveInDatabase('dataset_author', ['dataset_id' => $dataset_id,'author_id' => 3325, 'rank' => 1]);
                break;
            default:
                throw new \PHPUnit\Framework\IncompleteTestError("Step `I make an update to the non-public dataset :arg1's :arg2 in the admin pages` is not defined");
        }
    }

    /**
     * @Given sample :sample_id is associated with dataset :doi
     */
    public function sampleIsAssociatedWithDataset($sample_id, $doi)
    {
        $dataset_id = $this->I->grabFromDatabase('dataset', 'id', array('identifier' => '200070'));
        $this->I->haveInDatabase('dataset_sample', ['dataset_id' => $dataset_id,'sample_id' => $sample_id ]);
    }

    /**
     * @Given file :file_id is associated with dataset :doi
     */
    public function fileIsAssociatedWithDataset($file_id, $doi)
    {
        $dataset_id = $this->I->grabFromDatabase('dataset', 'id', array('identifier' => '200070'));
        $this->I->updateInDatabase('file', ['dataset_id' => $dataset_id],['id' => $file_id]);
    }
    /**
     * @Then I can see the changes to the :changeType displayed
     */
    public function iCanSeeTheChangesToTheDisplayed($changeType)
    {
        switch ($changeType) {
            case "dataset metadata":
                $this->I->canSee("lorem ipsum from automated tests");
                break;
            case "sample metadata":
                $this->I->cantSeeInSource("1.32");
                $this->I->canSeeInSource("value from automated tests");
                break;
            case "file metadata":
                $this->I->canSee("description from automated tests");
                break;
            case "author metadata":
                $this->I->canSee("Zhang");
                break;
            default:
                throw new \PHPUnit\Framework\IncompleteTestError("Step `I can see the changes to the :arg1 displayed` is not defined");
        }

    }

}
