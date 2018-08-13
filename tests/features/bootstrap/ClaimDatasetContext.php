<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use PHPUnit\Framework\Assert;
use Behat\Behat\Context\Step;

/**
 * Features context.
 */
class ClaimDatasetContext extends BehatContext
{


    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }


//
// Place your definition and hook methods here:
//
//    /**
//     * @Given /^I have done something with "([^"]*)"$/
//     */
//    public function iHaveDoneSomethingWith($argument)
//    {
//        doSomethingWith($argument);
//    }
//

    /**
     * @Given /^I am not logged in to Gigadb web site$/
     */
    public function iAmNotLoggedInToGigadbWebSite()
    {
        $this->getMainContext()->visit("/site/logout");
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
        if ( $this->getMainContext()->getSession()->getDriver() instanceof Behat\Mink\Driver\Selenium2Driver ) {
            return array(
                new Step\When("I fill in \"author_id\" with \"3791\""),
            );
        }
        else { //this branch (that use GoutteDriver works but we cannot use as the feature needs ajax)
            foreach ($this->getMainContext()->getSession()->getPage()->findAll('css', 'label') as $label) {
                if ($labelText === $label->getText() && $label->has('css', 'input[type="radio"]')) {
                    $this->getMainContext()->fillField($label->find('css', 'input[type="radio"]')->getAttribute('name'), $label->find('css', 'input[type="radio"]')->getAttribute('value'));
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
        return array(
                new Step\When("I follow \"claim_button_".$author_id."\""),
        );
    }

    /**
     * @Given /^a user has a pending claim for author "([^"]*)"$/
     */
    public function aUserHasAPendingClaimForAuthor($author_id)
    {
        return array(
                new Step\Given("I sign in as a user"),
                new Step\Given("I am on \"/dataset/100002\""),
                new Step\When("I follow \"Your dataset?\""),
                new Step\When("I wait \"2\" seconds"),
                new Step\When("I click on button for author id \"".$author_id."\""),
                new Step\When("I wait \"2\" seconds"),
            );
    }

    /**
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
        $row = $this->getMainContext()->getSubContext("admins_attach_author_user")->findRowByText($requester_name);
        if ("delete" == $action) { # TODO: deleting claim not tested has I haven't figured out yet how to test JS confirm with phantomjs
            $this->getMainContext()->getSession()->getDriver()->executeScript("window.confirm = function(msg){return true;};");
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
        return array(
                new Step\Given("I sign in as an admin"),
                new Step\When("I go to \"/user/update/id/346/\""),
                new Step\When("I follow \"Reject\""),
                new Step\When("I wait \"2\" seconds"),
            );
    }

     /**
     * @Given /^a user has a "([^"]*)" claim for author "([^"]*)"$/
     */
    public function aUserHasAClaimForAuthor($status, $author)
    {
        
    // Given a user has a pending claim for author "3791"
    // And an admin approved the claim for author "3791"
    // And an admin rejected the claim for author "3791"
    // When I go to "/AdminAuthor/view/id/3791"
    // Then I should not see "346"
        return array(
                new Step\Given("a user has a pending claim for author \"$author\""),
                new Step\Given("an admin $status the claim for author \"$author\""),
                new Step\When("I go to \"/AdminAuthor/view/id/$author\""),
                new Step\Then("I should not see \"346\""),
            );
    }


    /**
     * @AfterScenario @user-claims-dataset
    */
    public function resetClaimTable() {
        $this->getMainContext()->truncateTable("gigadb","user_command");
    }

}


?>