<?php
use Behat\Behat\Context\BehatContext;

use Behat\Behat\Context\Step;


class DatasetsOnProfileContext extends BehatContext
{
    public function __construct(array $parameters)
    {
    }

	/**
     * @Given /^I am linked to author "([^"]*)"$/
    */
    public function iAmLinkedToAuthor($author)
    {
        if ("Zhang, G" == $author) {
            return array(
                new Step\Given("author \"3791\" is associated with user \"346\""),
            );
        }
        else if("Yue, Z" == $author) {
            return array(
                new Step\Given("author \"3798\" is associated with user \"346\""),
            );
        }
        else if("Pan, S" == $author) {
            return array(
                new Step\Given("author \"3794\" is associated with user \"346\""),
            );
        }
    }


}