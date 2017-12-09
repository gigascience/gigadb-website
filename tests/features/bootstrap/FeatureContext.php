<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;



//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//


/**
 * Features context.
 */
class FeatureContext extends Behat\MinkExtension\Context\MinkContext
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
     * @Given /^I have a "([^"]*)" account$/
     */
    public function iHaveAAccount($arg1)
    {
        true; //temporary set to true to test the connection to the browser simulator
        // throw new PendingException();
    }

    /**
     * @Given /^I don\'t have a Gigadb account$/
     */
    public function iDonTHaveAGigadbAccount()
    {
        true; //temporary set to true to test the connection to the browser simulator
        // throw new PendingException();
    }


    /**
     * @When /^I navigate to "([^"]*)"$/
     */
    public function iNavigateTo($arg1)
    {
        $this->visit( $arg1 ) ;
        $session =  $this->getSession('Goutte');
        \PHPUnit\Framework\Assert::assertSame(
            200,
            $session->getStatusCode(), "Error while visiting the web site $arg1"
        );
    }


    /**
     * @Given /^I click on the "([^"]*)" button$/
     */
    public function iClickOnTheButton($arg1)
    {
        $this->clickLink($arg1);
        $session=  $this->getSession('Goutte');
    }

    /**
     * @Given /^I authorise Gigadb for "([^"]*)"$/
     */
    public function iAuthoriseGigadbFor($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^a new Gigadb account is created$/
     */
    public function aNewGigadbAccountIsCreated()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I\'m logged in into that account$/
     */
    public function iMLoggedInIntoThatAccount()
    {
        throw new PendingException();
    }

    /**
     * @Given /^the email I used for "([^"]*)" is used for that account$/
     */
    public function theEmailIUsedForIsUsedForThatAccount($arg1)
    {
        throw new PendingException();
    }




}
