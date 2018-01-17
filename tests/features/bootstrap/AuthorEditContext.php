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
        $this->login = $_ENV["GIGADB_tester_email"];
        $this->password = $_ENV["GIGADB_tester_password"] ;
    }

    /**
     * @Given /^I sign in as an admin$/
     */
    public function iSignInAsAnAdmin()
    {
         $this->getMainContext()->visit("/site/login");
         $this->getMainContext()->fillField("LoginForm_username", $this->login);
         $this->getMainContext()->fillField("LoginForm_password", $this->password);
         $this->getMainContext()->pressButton("Login"); 
    }



}