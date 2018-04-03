<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use PHPUnit\Framework\Assert;
use Behat\Behat\Context\Step;

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
    	 PHPUnit_Framework_Assert::assertTrue($result);
    	 $script = "(function(){return $('#author_merge').html();})();"; //capture the html of the modal dialog
    	 $result = $this->getMainContext()->getSession()->evaluateScript($script);
    	 PHPUnit_Framework_Assert::assertEquals(1,preg_match("/$arg1/",$result));

    }


}