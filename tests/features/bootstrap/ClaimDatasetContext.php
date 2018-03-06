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
        if ( $this->getMainContext()->getSession()->getDriver() instanceof Behat\Mink\Driver\Selenium2Driver ) {
            return array(
                new Step\When("I fill in \"author_id\" with \"3791\""),
            );
        }
        else {
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
     * @Given /^a user has a pending claim for author "([^"]*)"$/
     */
    public function aUserHasAPendingClaimForAuthor($arg1)
    {
        return array(
                new Step\Given("I sign in as a user"),
                new Step\Given("I am on \"/dataset/100002\""),
                new Step\When("I follow \"Are you an author of this dataset? claim your dataset now\""),
                new Step\When("I check the \"Zhang G\" radio button"),
                new Step\When("I follow \"Claim selected author\""),
                new Step\When("I wait \"5\" seconds"),
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
        $dbconn =pg_connect("host=localhost dbname=gigadb user=postgres port=9171") or die('Could not connect: '.pg_last_error());
        pg_query($dbconn, $sql);
        pg_close($dbconn);
    }


    /**
     * @AfterScenario @user-claims-dataset
    */
    public function resetClaimTable() {
        $this->getMainContext()->truncateTable("gigadb","user_command");
    }

}


?>