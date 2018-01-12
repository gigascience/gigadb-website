<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;
use Behat\YiiExtension\Context\YiiAwareContextInterface;



/**
 * AuthorWorkflow Features context.
 */
class AuthorWorkflowContext extends Behat\MinkExtension\Context\MinkContext implements Behat\YiiExtension\Context\YiiAwareContextInterface
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
    public function setYiiWebApplication(\CWebApplication $yii)
    {
        $this->yii = $yii ;
    }
    public function getYii()
    {
        if (null === $this->yii) {
            throw new \RuntimeException(
                'Yii instance has not been set on Yii context class. ' .
                'Have you enabled the Yii Extension?'
            );
        }
        return $this->yii ;
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



}