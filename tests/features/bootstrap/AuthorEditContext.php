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
class AuthorEditContext extends BehatContext
{
    private $surname = null;
    private $first_name = null;
    private $middle_name =  null;


    /**
     * @Given /^I sign in as an admin$/
     */
    public function iSignInAsAnAdmin()
    {
         throw new PendingException();
    }



}