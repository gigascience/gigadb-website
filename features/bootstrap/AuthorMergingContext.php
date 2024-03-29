<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * Contains the steps definitions used in merge-authors.feature
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 * @see http://docs.behat.org/en/latest/quick_start.html#defining-steps
 *
 * @uses GigadbWebsiteContext::loadUserData For loading author relationship fixture data
 * @uses GigadbWebsiteContext::iSignInAsAnAdmin For signing in as a admin
 * @uses ClaimDatasetContext For the sleep timer function
 * @uses AuthorUserContext For clicking on a row from Author table in admin view
 * @uses \Behat\MinkExtension\Context\MinkContext For controlling the web browser
 * @uses \PHPUnit_Framework_Assert
 */
class AuthorMergingContext implements Context
{

    /**
     * @var \Behat\MinkExtension\Context\MinkContext
     */
    private $minkContext;

    /**
     * @var GigadbWebsiteContext
     */
    private $gigadbWebsiteContext;

    /**
     * @var ClaimDatasetContext
     */
    private $claimDatasetContext;

    /**
     * @var AuthorUserContext
     */
    private $authorUserContext;

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

        $this->minkContext = $environment->getContext('Behat\MinkExtension\Context\MinkContext');
        $this->gigadbWebsiteContext = $environment->getContext('GigadbWebsiteContext');
        $this->claimDatasetContext = $environment->getContext('ClaimDatasetContext');
        $this->authorUserContext = $environment->getContext('AuthorUserContext');
    }


     /**
     * @Given /^A dialog box reads "([^"]*)"$/
     */
    public function aDialogBoxReads($arg1)
    {
    	//modal dialog are invisible by default until they are toogled by javascript
    	//so we need to get that javascript executed by the javascript-capable headless browser
    	 $script = "(function(){return ($('#author_merge').is(':visible'));})();";//toogle the modal dialog to visible
    	 $result = $this->minkContext->getSession()->evaluateScript($script);
    	 PHPUnit_Framework_Assert::assertTrue($result,"Dialog box is made visible");
    	 $script = "(function(){return $('#author_merge').html();})();"; //capture the html of the modal dialog
    	 $result = $this->minkContext->getSession()->evaluateScript($script);
    	 PHPUnit_Framework_Assert::assertEquals(1,preg_match("/$arg1/",$result), "Dialog box contains {$arg1}");

    }

    /**
     * @Given /^author "([^"]*)" is merged with author "([^"]*)"$/
     */
    public function authorIsMergedWithAuthor($origin_author, $target_author)
    {

        // return array(
        //         // new Step\Given("I sign in as an admin"),
        //         // new Step\Given("I am on \"/adminAuthor/update/id/{$origin_author}\""),
        //         // new Step\Given("I follow \"Merge with an author\""),
        //         // new Step\Given("I wait \"2\" seconds"),
        //         // new Step\When("I click on the row for author id \"{$target_author}\""),
        //         // new Step\Given("I wait \"2\" seconds"),
        //         // new Step\When("A dialog box reads \"Confirm merging these two authors?\""),
        //         // new Step\When("I follow \"Yes, merge authors\""),
        //         // new Step\When("I wait \"1\" seconds"),
        //         // new Step\Then("I should be on \"/adminAuthor/view/id/{$origin_author}\""),
        //         // new Step\Then("I should see \"merging authors completed successfully\""),
        // );

        $this->gigadbWebsiteContext->iSignInAsAnAdmin();
        $this->minkContext->visit("/adminAuthor/update/id/{$origin_author}");
        $this->minkContext->clickLink("Merge with an author");
        $this->claimDatasetContext->iWaitSeconds(2);
        $this->authorUserContext->iClickOnTheRowForAuthorId($target_author);
        $this->claimDatasetContext->iWaitSeconds(2);
        $this->aDialogBoxReads("Confirm merging these two authors?");
        $this->minkContext->clickLink("Yes, merge authors");
        $this->claimDatasetContext->iWaitSeconds(1);
        $this->minkContext->assertPageAddress("/adminAuthor/view/id/{$origin_author}");
        $this->minkContext->assertPageContainsText("merging authors completed successfully");

    }

    /**
     * @Given /^an existing graph of authors$/
    */
    public function anExistingGraphOfAuthors()
    {
        $this->gigadbWebsiteContext->loadUserData("performances-49");
    }

}