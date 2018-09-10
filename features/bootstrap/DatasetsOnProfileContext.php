<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * DatasetsOnProfileContext
 *
 * Contains the steps definitions used in datasets-on-user-profile-60.feature
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 * @see http://docs.behat.org/en/latest/quick_start.html#defining-steps
 *
 * @uses AuthorUserContext For the step "author x is associated with user y"
 */
class DatasetsOnProfileContext implements Context
{


    /**
     * @var AuthorUserContext
     */
    private $authorUserContext;

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