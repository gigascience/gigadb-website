<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use PHPUnit\Framework\Assert;

/**
 * Features context.
 */
class NormalLoginContext extends BehatContext
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
     * @Given /^I have a gigadb account with "([^"]*)" role$/
     */
    public function iHaveAGigadbAccountWithRole($arg1)
    {
    	$email = "user@gigadb.org"; 
        $nb_ocurrences = $this->getMainContext()->getSubcontext('affiliate_login')->countEmailOccurencesInUserList($email);
        PHPUnit_Framework_Assert::assertTrue(1 == $nb_ocurrences, "there is a user account in db");
    }


}


?>