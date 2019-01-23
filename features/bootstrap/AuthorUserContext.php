<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * Contains the steps definitions used in author workflows
 * (merge-authors, admins-attach-author-to-user, admin-validates-dataset-claim)
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 * @see http://docs.behat.org/en/latest/quick_start.html#defining-steps
 *
 * @uses ClaimDatasetContext For the sleep timer function
 * @uses \Behat\MinkExtension\Context\MinkContext For controlling the web browser
 * @uses \PHPUnit_Framework_Assert
 */
class AuthorUserContext implements Context
{

    /**
     * @var \Behat\MinkExtension\Context\MinkContext
     */
    private $minkContext;

    /**
     * @var ClaimDatasetContext
     */
    private $claimDatasetContext;


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
        $this->claimDatasetContext = $environment->getContext('ClaimDatasetContext');
    }


    /**
     * @Given /^author "([^"]*)" is associated with user "([^"]*)"$/
     */
    public function authorIsAssociatedWithUser($author, $user)
    {
        $dbconn = pg_connect("host=database dbname=gigadb user=gigadb port=5432") or die('Could not connect: ' . pg_last_error());
        $query = "update author set gigadb_user_id=${user} where id=${author};";
        pg_query($query) or die('Query failed: ' . pg_last_error());
        pg_close($dbconn);
    }


    /**
     * @Given /^I have initiated the search of an author for Gigadb User with ID "([^"]*)"$/
    */
    public function iHaveInitiatedTheSearchOfAnAuthorForGigadbUserWithId($arg1)
    {
        // return array(
        //     new Step\Given("I am on \"/user/update/id/${arg1}\""),
        //     new Step\When("I follow \"Link this user to an author\""),
        //     new Step\When("I wait \"2\" seconds"),
        //     new Step\Then("I should be on \"/adminAuthor/admin\""),
        //     new Step\Then("I should see \"Click on a row to proceed with linking that author with user\""),
        // );

        $this->minkContext->visit("/user/update/id/${arg1}");
        $this->minkContext->clickLink("Link this user to an author");
        $this->claimDatasetContext->iWaitSeconds(2);
        $this->minkContext->assertPageAddress("/adminAuthor/admin");
        $this->minkContext->assertPageContainsText("Click on a row to proceed with linking that author with user");

    }



     /**
     * @Given /^I have linked user "([^"]*)" of id "([^"]*)" to author "([^"]*)"$/
     */
    public function iHaveLinkedUserOfIdToAuthor($user_name, $user_id, $author_id)
    {
        // return array(
        //     new Step\When("I click on the row for author id \"${author_id}\""),
        //     new Step\When("I wait \"2\" seconds"),
        //     new Step\When("I follow \"Link user ${user_name} to that author\""),
        //     new Step\When("I wait \"2\" seconds"),
        //     new Step\Then("I should be on \"/user/view/id/${user_id}\""),
        // );

        $this->iClickOnTheRowForAuthorId("${author_id}");
        $this->claimDatasetContext->iWaitSeconds(2);
        $this->minkContext->clickLink("Link user ${user_name} to that author");
        $this->claimDatasetContext->iWaitSeconds(2);
        $this->minkContext->assertPageAddress("/user/view/id/${user_id}");
    }


    /**
     * @When /^I click on the row for author id "([^"]*)"$/
     */
    public function iClickOnTheRowForAuthorId($author_id)
    {
        $this->minkContext->getSession()->executeScript("open_controls(" . $author_id . ")");
    }


    /**
     * @When /^I click on the row for user id "([^"]*)"$/
     */
    public function iClickOnTheRowForUserId($user_id)
    {
        $row = $this->findRowByText($user_id);
        $this->minkContext->getSession()->executeScript("open_controls(" . $user_id . ")");
    }

     /**
     * @When /^I click "([^"]*)" in the row for author "([^"]*)"$/
     */
    public function iClickInTheRowForAuthor($action, $author)
    {
        $row = $this->findRowByText($author);
        $link = $row->findLink($action);
        PHPUnit_Framework_Assert::assertNotNull($link, 'Cannot find link in row with text '.$action);
        $link->click();
    }

    /**
     * Find a text in a table and return the row containing the text
     *
     * @param $rowText
     * @return \Behat\Mink\Element\NodeElement
     */
    public function findRowByText($rowText)
    {
        $row = $this->minkContext->getSession()->getPage()->find('css', sprintf('table tr:contains("%s")', $rowText));
        PHPUnit_Framework_Assert::assertNotNull($row, 'Cannot find a table row with this text!');
        return $row;
    }




}