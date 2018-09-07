<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use PHPUnit\Framework\Assert;
//use Behat\Behat\Context\Step;

/**
 * Features context.
 */
class AuthorMergingContext extends BehatContext
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
     * @Given /^A dialog box reads "([^"]*)"$/
     */
    public function aDialogBoxReads($arg1)
    {
    	//modal dialog are invisible by default until they are toogled by javascript
    	//so we need to get that javascript executed by the javascript-capable headless browser
    	 $script = "(function(){return ($('#author_merge').is(':visible'));})();";//toogle the modal dialog to visible
    	 $result = $this->getMainContext()->getSession()->evaluateScript($script);
    	 PHPUnit_Framework_Assert::assertTrue($result,"Dialog box is made visible");
    	 $script = "(function(){return $('#author_merge').html();})();"; //capture the html of the modal dialog
    	 $result = $this->getMainContext()->getSession()->evaluateScript($script);
    	 PHPUnit_Framework_Assert::assertEquals(1,preg_match("/$arg1/",$result), "Dialog box contains {$arg1}");

    }

    /**
     * @Given /^author "([^"]*)" is merged with author "([^"]*)"$/
     */
    public function authorIsMergedWithAuthor($origin_author, $target_author)
    {

        // return array(
        //         // new Step\Given("I sign in as an admin"),
        //         // new Step\Given("I am on \"/adminAuthor/update/id/{$origin_author}\""),
        //         // new Step\Given("I follow \"Merge with an author\""),
        //         // new Step\Given("I wait \"2\" seconds"),
        //         // new Step\When("I click on the row for author id \"{$target_author}\""),
        //         // new Step\Given("I wait \"2\" seconds"),
        //         // new Step\When("A dialog box reads \"Confirm merging these two authors?\""),
        //         // new Step\When("I follow \"Yes, merge authors\""),
        //         // new Step\When("I wait \"1\" seconds"),
        //         // new Step\Then("I should be on \"/adminAuthor/view/id/{$origin_author}\""),
        //         // new Step\Then("I should see \"merging authors completed successfully\""),
        // );

        $this->getMainContext()->iSignInAsAnAdmin();
        $this->getMainContext()->visit("/adminAuthor/update/id/{$origin_author}");
        $this->getMainContext()->clickLink("Merge with an author");
        $this->getMainContext()->getSubContext("claim_dataset")->iWaitSeconds(2);
        $this->getMainContext()->getSubContext("admins_attach_author_user")->iClickOnTheRowForAuthorId($target_author);
        $this->getMainContext()->getSubContext("claim_dataset")->iWaitSeconds(2);
        $this->aDialogBoxReads("Confirm merging these two authors?");
        $this->getMainContext()->clickLink("Yes, merge authors");
        $this->getMainContext()->getSubContext("claim_dataset")->iWaitSeconds(1);
        $this->getMainContext()->assertPageAddress("/adminAuthor/view/id/{$origin_author}");
        $this->getMainContext()->assertPageContainsText("merging authors completed successfully");

    }

    /**
     * @Given /^an existing graph of authors$/
    */
    public function anExistingGraphOfAuthors()
    {
        $this->getMainContext()->loadUserData("performances-49");
    }

}