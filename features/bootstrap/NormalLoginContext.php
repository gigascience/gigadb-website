<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * Features context.
 */
class NormalLoginContext implements Context
{


    /** @var GigadbWebsiteContext */
    private $affiliateLoginContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->affiliateLoginContext = $environment->getContext('AffiliateLoginContext');
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
        $nb_ocurrences = $this->affiliateLoginContext->countEmailOccurencesInUserList($email);
        PHPUnit_Framework_Assert::assertTrue(1 == $nb_ocurrences, "there is a user account in db");
    }


}


?>