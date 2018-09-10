<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * AuthorWorkflow Features context.
 */
class DatasetViewContext implements Context
{
    private $surname = null;
    private $first_name = null;
    private $middle_name =  null;


    /** @var GigadbWebsiteContext */
    private $gigadbWebsiteContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->gigadbWebsiteContext = $environment->getContext('GigadbWebsiteContext');
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
     * @Given /^Gigadb web site is loaded with production-like data$/
     */
    public function gigadbWebSiteIsLoadedWithProductionLikeData()
    {
        $sqlfile = "production_like.pgdmp";
        // return array(
        //     new Step\Given("Gigadb web site is loaded with \"${sqlfile}\" data"),
        // );
        $this->gigadbWebsiteContext->gigadbWebSiteIsLoadedWithData($sqlfile);
    }

    /**
     * @Given /^author has surname "([^"]*)"$/
     */
    public function authorHasSurname($arg1)
    {
        $this->surname = $arg1 ;
    }

    /**
     * @Given /^author has first name "([^"]*)"$/
     */
    public function authorHasFirstName($arg1)
    {
        $this->first_name = $arg1 ;
    }

    /**
     * @Given /^author has middle name "([^"]*)"$/
     */
    public function authorHasMiddleName($arg1)
    {
        $this->middle_name = $arg1 ;
    }



    /**
     * @BeforeScenario @author-names-display&&@edit-display-name
     */
    public function reset()
    {
        $this->surname = null;
        $this->first_name = null;
        $this->middle_name = null;
    }




}
