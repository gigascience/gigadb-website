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
	    $this->getSession()->wait(5000,
	        "$('.suggestions-results').children().length > 0"
	    );
	}

	/**
	 * @AfterStep
	 */
	 public function takeScreenshotAfterFailedStep($event)
	 {
	   if ($event->getResult() == 4) {
	     if ($this->getSession()->getDriver() instanceof \Behat\Mink\Driver\Selenium2Driver) {
	       $screenshot = $this->getSession()->getDriver()->getScreenshot();
	       file_put_contents('/tmp/screenshot.png', $screenshot);
	     }
	   }
	 }
}