<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;

/**
 * Features context.
 */
class DemoFeatureContext extends MinkContext
{
	/**
	 * @When /^I wait for the suggestion box to appear$/
	 */
	public function iWaitForTheSuggestionBoxToAppear()
	{
	    $this->getSession()->wait(10000,
	        "$('.suggestions-results').children().length > 0"
	    );
	}

	/**
     * @Then /^I should see the suggestion "([^"]*)"$/
     */
    public function iShouldSeeTheSuggestion($arg1)
    {
       
        //$page = $this->getSession()->getPage();
        $this->assertSession()->pageTextContains($this->fixStepArgument($arg1));
    }


	/**
	 * @AfterStep
	 */
	 public function takeSnapshotAfterFailedStep($event)
	 {
	   if ($event->getResult() == 4) {
	     if ($this->getSession()->getDriver() instanceof \Behat\Mink\Driver\Selenium2Driver) {
	       $screenshot = $this->getSession()->getDriver()->getScreenshot();
	       $content = $this->getSession()->getDriver()->getContent();
	       file_put_contents('/tmp/test.png', $screenshot);
	       file_put_contents('/tmp/test.html', $content);
	     }
	   }
	 }
}