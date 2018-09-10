<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * Contains the steps definitions used in dataset claim workflow
 * (admin-validates-dataset-claim-57.feature, admins-attach-author-to-user.feature, user-claims-dataset-57.feature)
 *
 * Also used by other Context classes for the sleep timer function
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 * @see http://docs.behat.org/en/latest/quick_start.html#defining-steps
 *
 * @uses GigadbWebsiteContext For resetting the database
 * @uses GigadbWebsiteContext::iSignInAsAUser For signing in as a user
 * @uses GigadbWebsiteContext::iSignInAsAnAdmin For signing in as a admin
 * @uses AuthorUserContext For clicking on a row from Author table in admin view
 * @uses \Behat\MinkExtension\Context\MinkContext For controlling the web browser
 * @uses \PHPUnit_Framework_Assert
 */
class ClaimDatasetContext implements Context
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
        $this->authorUserContext = $environment->getContext('AuthorUserContext');
    }

    /**
     * @Given /^I am not logged in to Gigadb web site$/
     */
    public function iAmNotLoggedInToGigadbWebSite()
    {
        $this->minkContext->visit("/site/logout");
    }

    /**
     * @When /^I check the "([^"]*)" radio button$/
     */
    public function iCheckTheRadioButton($labelText)
    {
        sleep(10);
        // TODO: problem with selectin g radio button with Selenium2Driver
        // See: https://github.com/Behat/Behat/issues/973
        // for now I "cheat" and make sure there radio buttons are set as "checked" on html side
        // and in the scenario, select the last option (Wang J).
        if ( $this->minkContext->getSession()->getDriver() instanceof Behat\Mink\Driver\Selenium2Driver ) {
            return array(
                new Step\When("I fill in \"author_id\" with \"3791\""),
            );
        }
        else { //this branch (that use GoutteDriver works but we cannot use as the feature needs ajax)
            foreach ($this->minkContext->getSession()->getPage()->findAll('css', 'label') as $label) {
                if ($labelText === $label->getText() && $label->has('css', 'input[type="radio"]')) {
                    $this->minkContext->fillField($label->find('css', 'input[type="radio"]')->getAttribute('name'), $label->find('css', 'input[type="radio"]')->getAttribute('value'));
                    return;
                }
            }
        }
        throw new \Exception('Radio button not found');
    }

    /**
     * @Given /^I click on button for author id "([^"]*)"$/
     */
    public function iClickOnButtonForAuthorId($author_id)
    {
        // return array(
        //         new Step\When("I follow \"claim_button_".$author_id."\""),
        // );
        $this->minkContext->clickLink("claim_button_".$author_id);
    }

    /**
     * @Given /^a user has a pending claim for author "([^"]*)"$/
     */
    public function aUserHasAPendingClaimForAuthor($author_id)
    {

        $this->gigadbWebsiteContext->iSignInAsAUser();
        $this->minkContext->visit("/dataset/100002");
        $this->minkContext->clickLink("Your dataset?");
        $this->iWaitSeconds(2);
        $this->iClickOnButtonForAuthorId($author_id);
        $this->iWaitSeconds(2);

        // return array(
        //     // new Step\Given("I sign in as a user"),
        //     // new Step\Given("I am on \"/dataset/100002\""),
        //     // new Step\When("I follow \"Your dataset?\""),
        //     // new Step\When("I wait \"2\" seconds"),
        //     // new Step\When("I click on button for author id \"".$author_id."\""),
        //     // new Step\When("I wait \"2\" seconds"),
        // );

    }

    /**
     * The method to generate a waiting period using php sleep function
     *
     * @param int $number_of_seconds number of seconds to wait
     *
     *
     * @Given /^I wait "([^"]*)" seconds$/
     */
    public function iWaitSeconds($number_of_seconds)
    {
        sleep((int)$number_of_seconds);
    }



    /**
     * @Given /^author "([^"]*)" is associated with a user$/
     */
    public function authorIsAssociatedWithAUser($author_id)
    {
        $sql = "update author set gigadb_user_id=346 where id=${author_id}";
        $dbconn =pg_connect("host=database dbname=gigadb user=gigadb password=vagrant port=5432") or die('Could not connect: '.pg_last_error());
        pg_query($dbconn, $sql);
        pg_close($dbconn);
    }

    /**
     * @When /^I click "([^"]*)" in the row for claim from "([^"]*)"$/
     */
    public function iClickInTheRowForClaimFrom($action, $requester_name)
    {
        $row = $this->authorUserContext->findRowByText($requester_name);
        if ("delete" == $action) { # TODO: deleting claim not tested has I haven't figured out yet how to test JS confirm with phantomjs
            $this->minkContext->getSession()->getDriver()->executeScript("window.confirm = function(msg){return true;};");
            $link = $row->findLink('');
        }
        else {
            $link = $row->findById($action);
        }
        PHPUnit_Framework_Assert::assertNotNull($link, 'Cannot find link in row with text '.$action);
        $link->click();
    }



    /**
     * @Given /^an admin rejected the claim for author "([^"]*)"$/
     */
    public function anAdminRejectedTheClaimForAuthor($arg1)
    {
        // return array(
        //         new Step\Given("I sign in as an admin"),
        //         new Step\When("I go to \"/user/update/id/346/\""),
        //         new Step\When("I follow \"Reject\""),
        //         new Step\When("I wait \"2\" seconds"),
        //     );

        $this->gigadbWebsiteContext->iSignInAsAnAdmin();
        $this->minkContext->visit("/user/update/id/346/");
        $this->minkContext->clickLink("Reject");
        $this->iWaitSeconds(2);
    }

     /**
     * @Given /^a user has a "([^"]*)" claim for author "([^"]*)"$/
     */
    public function aUserHasAClaimForAuthor($status, $author)
    {
        // return array(
        //         // new Step\Given("a user has a pending claim for author \"$author\""),
        //         // new Step\Given("an admin $status the claim for author \"$author\""),
        //         // new Step\When("I go to \"/AdminAuthor/view/id/$author\""),
        //         // new Step\Then("I should not see \"346\""),
        //     );

    // Given a user has a pending claim for author "3791"
        $this->gigadbWebsiteContext->iSignInAsAUser();
        $this->minkContext->visit("/dataset/100002");
        $this->minkContext->clickLink("Your dataset?");
        $this->iWaitSeconds(2);
        $this->iClickOnButtonForAuthorId($author);
        $this->iWaitSeconds(2);

    // And an admin approved the claim for author "3791"
    // And an admin rejected the claim for author "3791"
        if("rejected" == $status) {
            $this->anAdminRejectedTheClaimForAuthor($author);
        }

    // When I go to "/AdminAuthor/view/id/3791"
        $this->minkContext->visit("/AdminAuthor/view/id/$author");

    // Then I should not see "346"
        $this->minkContext->assertPageNotContainsText("346");
    }


    /**
     * Make sure the claims table is reset after scenario run
     *
     * @AfterScenario @user-claims-dataset
    */
    public function resetClaimTable() {
        $this->gigadbWebsiteContext->truncateTable("gigadb","user_command");
    }

}


?>