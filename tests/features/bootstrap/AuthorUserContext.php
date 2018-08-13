<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;
use Behat\YiiExtension\Context\YiiAwareContextInterface;

use Behat\Behat\Context\Step;


/**
 * AuthorWorkflow Features context.
 */
class AuthorUserContext extends BehatContext
{
    private $surname = null;
    private $first_name = null;
    private $middle_name =  null;
    private $login = null;
    private $password = null ;

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


    /**
     * @Given /^author "([^"]*)" is associated with user "([^"]*)"$/
     */
    public function authorIsAssociatedWithUser($author, $user)
    {
        $dbconn = pg_connect("host=database dbname=gigadb user=gigadb port=5432") or die('Could not connect: ' . pg_last_error());
        $query = "update author set gigadb_user_id=${user} where id=${author};";
        pg_query($query) or die('Query failed: ' . pg_last_error());
        pg_close($dbconn);
    }


    /**
     * @Given /^I have initiated the search of an author for Gigadb User with ID "([^"]*)"$/
    */
    public function iHaveInitiatedTheSearchOfAnAuthorForGigadbUserWithId($arg1)
    {
        return array(
            new Step\Given("I am on \"/user/update/id/${arg1}\""),
            new Step\When("I follow \"Link this user to an author\""),
            new Step\When("I wait \"2\" seconds"),
            new Step\Then("I should be on \"/adminAuthor/admin\""),
            new Step\Then("I should see \"Click on a row to proceed with linking that author with user\""),
        );
    }



     /**
     * @Given /^I have linked user "([^"]*)" of id "([^"]*)" to author "([^"]*)"$/
     */
    public function iHaveLinkedUserOfIdToAuthor($user_name, $user_id, $author_id)
    {
        return array(
            new Step\When("I click on the row for author id \"${author_id}\""),
            new Step\When("I wait \"2\" seconds"),
            new Step\When("I follow \"Link user ${user_name} to that author\""),
            new Step\When("I wait \"2\" seconds"),
            new Step\Then("I should be on \"/user/view/id/${user_id}\""),
        );
    }


    /**
     * @When /^I click on the row for author id "([^"]*)"$/
     */
    public function iClickOnTheRowForAuthorId($author_id)
    {
        $this->getMainContext()->getSession()->executeScript("open_controls(" . $author_id . ")");
    }


    /**
     * @When /^I click on the row for user id "([^"]*)"$/
     */
    public function iClickOnTheRowForUserId($user_id)
    {
        $row = $this->findRowByText($user_id);
        $this->getMainContext()->getSession()->executeScript("open_controls(" . $user_id . ")");
    }

     /**
     * @When /^I click "([^"]*)" in the row for author "([^"]*)"$/
     */
    public function iClickInTheRowForAuthor($action, $author)
    {
        $row = $this->findRowByText($author);
        $link = $row->findLink($action);
        PHPUnit_Framework_Assert::assertNotNull($link, 'Cannot find link in row with text '.$action);
        $link->click();
    }

    /**
     * @param $rowText
     * @return \Behat\Mink\Element\NodeElement
     */
    public function findRowByText($rowText)
    {
        $row = $this->getMainContext()->getSession()->getPage()->find('css', sprintf('table tr:contains("%s")', $rowText));
        PHPUnit_Framework_Assert::assertNotNull($row, 'Cannot find a table row with this text!');
        return $row;
    }




}