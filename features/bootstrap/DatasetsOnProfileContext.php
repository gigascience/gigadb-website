<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class DatasetsOnProfileContext implements Context
{


    /** @var AuthorUserContext */
    private $authorUserContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->authorUserContext = $environment->getContext('AuthorUserContext');
    }

	/**
     * @Given /^I am linked to author "([^"]*)"$/
    */
    public function iAmLinkedToAuthor($author)
    {
        if ("Zhang, G" == $author) {
            // return array(
            //     new Step\Given("author \"3791\" is associated with user \"346\""),
            // );
            $this->authorUserContext->authorIsAssociatedWithUser(3791,346);
        }
        else if("Yue, Z" == $author) {
            // return array(
            //     new Step\Given("author \"3798\" is associated with user \"346\""),
            // );
            $this->authorUserContext->authorIsAssociatedWithUser(3798,346);
        }
        else if("Pan, S" == $author) {
            // return array(
            //     new Step\Given("author \"3794\" is associated with user \"346\""),
            // );
            $this->authorUserContext->authorIsAssociatedWithUser(3794,346);
        }
    }


}