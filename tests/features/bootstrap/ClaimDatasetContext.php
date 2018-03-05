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
     * @Given /^I wait "([^"]*)" seconds$/
     */
    public function iWaitSeconds($number_of_seconds)
    {
        sleep((int)$number_of_seconds);
    }


    /**
     * @AfterScenario @user-claims-dataset
    */
    public function resetClaimTable() {
        $this->getMainContext()->truncateTable("gigadb","user_command");
    }

}


?>