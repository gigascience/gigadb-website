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
        true;
    }

    /**
     * @Given /^I don\'t have a Gigadb account$/
     */
    public function iDonTHaveAGigadbAccount()
    {
        true;
    }


    /**
     * @When /^I navigate to \/site\/chooseLogin$/
     */
    public function iNavigateToSiteChooselogin()
    {
        $this->visit( "/site/chooseLogin" ) ;
        $session =  $this->getSession('Goutte');
        echo $session->getCurrentUrl() . PHP_EOL ;
        // PHPUnit_Framework_Assert::assertSame(
        //     "http://localhost/site/chooseLogin",
        //     $session->getCurrentUrl()
        // );
    }

    /**
     * @Given /^I click on the "([^"]*)" button$/
     */
    public function iClickOnTheButton($arg1)
    {
        $this->clickLink($arg1);
        $session=  $this->getSession('Goutte');
        echo $session->getCurrentUrl() . PHP_EOL ;
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

    /**
     * @Given /^I have a Gigadb account$/
     */
    public function iHaveAGigadbAccount()
    {
        throw new PendingException();
    }

    /**
     * @Given /^email addresses for those accounts match$/
     */
    public function emailAddressesForThoseAccountsMatch()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I\'m logged in into my existing account$/
     */
    public function iMLoggedInIntoMyExistingAccount()
    {
        throw new PendingException();
    }

    /**
     * @Given /^no new gigadb account is created$/
     */
    public function noNewGigadbAccountIsCreated()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I click on the "([^"]*)" buttons$/
     */
    public function iClickOnTheButtons($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^email addresses for those accounts do not match$/
     */
    public function emailAddressesForThoseAccountsDoNotMatch()
    {
        throw new PendingException();
    }




}
