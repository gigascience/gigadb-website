<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;

/**
 * Contains the steps definitions used in dataset-admin.feature
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 * @see http://docs.behat.org/en/latest/quick_start.html#defining-steps
 *
 * @uses GigadbWebsiteContext For loading production like data
 */
class DatasetAdminContext implements Context
{
    /**
     * @var GigadbWebsiteContext
     */
    private $gigadbWebsiteContext;
    private $minkContext;

    /**
     * The method to retrieve needed contexts from the Behat environment
     *
     * @param BeforeScenarioScope $scope parameter needed to retrieve contexts from the environment
     *
     * @BeforeScenario
     *
    */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->gigadbWebsiteContext = $environment->getContext('GigadbWebsiteContext');
        $this->minkContext = $environment->getContext('Behat\MinkExtension\Context\MinkContext');
    }

    /**
     * @Then I should see a form element labelled :arg1
     */
    public function iShouldSeeAFormElementLabelled2($arg1)
    {

        // $this->minkContext->assertSession()->fieldExists($arg1);
        PHPUnit_Framework_Assert::assertTrue(
            $this->minkContext->getSession()->getPage()->hasField($arg1)
        );
    }

    /**
     * @Then I should not see a form element labelled :arg1
     */
    public function iShouldNotSeeAFormElementLabelled($arg1)
    {
        PHPUnit_Framework_Assert::assertFalse(
            $this->minkContext->getSession()->getPage()->hasField($arg1)
        );
    }

    /**
     * @Then I should see a button input :arg1
     */
    public function iShouldSeeAButtonInput($arg1)
    {
        PHPUnit_Framework_Assert::assertTrue(
            $this->minkContext->getSession()->getPage()->hasButton($arg1)
        );
    }

    /**
     * @Then I should not see a button input :arg1
     */
    public function iShouldNotSeeAButtonInput($arg1)
    {
        PHPUnit_Framework_Assert::assertFalse(
            $this->minkContext->getSession()->getPage()->hasButton($arg1)
        );
    }

    /**
     * @Then I should see element :arg1's content changing from :arg2 to :arg3
     */
    public function iShouldSeeElementSContentChangingFromTo($arg1, $arg2, $arg3)
    {
        $this->minkContext->getSession()->wait(10000, "($('$arg1').html() != '$arg2' )");
        PHPUnit_Framework_Assert::assertTrue(
            $this->minkContext->getSession()->getPage()->hasContent($arg3)
        );
    }

    /**
     * @When I fill in the :arg1 field with :arg2
     */
    public function iFillInTheFieldWith($arg1, $arg2)
    {
        $this->minkContext->fillField($arg1, $arg2);
        $this->minkContext->pressButton("Save");
    }

    /**
     * @Then I should not see links to :arg1
     */
    public function iShouldNotSeeLinksTo($arg1, TableNode $table)
    {
        foreach($table as $row) {
            PHPUnit_Framework_Assert::assertFalse( $this->minkContext->getSession()->getPage()->hasLink($row[$arg1]) );
        }
    }

    /**
     * @Then the url should be :arg1
     */
    public function theUrlShouldBe($arg1)
    {
        PHPUnit_Framework_Assert::assertEquals(
            $arg1,parse_url( $this->minkContext->getSession()->getCurrentUrl(), PHP_URL_PATH)
        );
    }

    /**
     * @Then the url should match the pattern :arg1
     */
    public function theUrlShouldMatchThePattern($arg1)
    {
        $currentUrl = parse_url( $this->minkContext->getSession()->getCurrentUrl(), PHP_URL_PATH) ;
        PHPUnit_Framework_Assert::assertTrue(
            (boolean)preg_match("$arg1",$currentUrl)
        );
    }


}
