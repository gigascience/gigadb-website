<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * NormalLoginContext
 *
 * Contains steps definitions used by login.feature
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 * @see http://docs.behat.org/en/latest/quick_start.html#defining-steps
 *
 * @uses AffiliateLoginContext For checking user exists in database from email
 * @uses \PHPUnit_Framework_Assert
 */
class NormalLoginContext implements Context
{


    /**
     * @var AffiliateLoginContext
     */
    private $affiliateLoginContext;

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

        $this->affiliateLoginContext = $environment->getContext('AffiliateLoginContext');
    }


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