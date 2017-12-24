<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;
use Behat\YiiExtension\Context\YiiAwareContextInterface;



//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//


/**
 * Features context.
 */
class FeatureContext extends Behat\MinkExtension\Context\MinkContext implements Behat\YiiExtension\Context\YiiAwareContextInterface
{
    private $yii;
    private $keys_map = array('Facebook' => array('api_key' => 'app_id', 'client_key' => 'app_secret'),
                               'Google' => array('api_key' => 'client_id', 'client_key' => 'client_secret'),
                               'Twitter' => array('api_key' => 'key', 'client_key' => 'secret'),
                               'LinkedIn' => array('api_key' => 'api_key', 'client_key' => 'secret_key'),
                               'Orcid' => array('api_key' => 'client_id', 'client_key' => 'client_secret'),
                           );
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

    /**
     * @Given /^Gigadb has a "([^"]*)" API keys$/
     */
    public function gigadbHasAApiKeys($arg1)
    {
        $_SERVER['REQUEST_URI'] = 'foobar';
        $_SERVER['HTTP_HOST'] = 'foobar';
        $opauthModule = $this->getYii()->getModules()['opauth'];
        $api_key = $opauthModule['opauthParams']["Strategy"][$arg1][$this->keys_map[$arg1]['api_key']] ;
        $client_key = $opauthModule['opauthParams']["Strategy"][$arg1][$this->keys_map[$arg1]['client_key']] ;


        \PHPUnit\Framework\Assert::assertTrue('' != $api_key, "api_key for $arg1 is not empty");
        \PHPUnit\Framework\Assert::assertTrue('' != $client_key, "client_key for $arg1 is not empty");

    }


/**
     * @Given /^I have a "([^"]*)" account$/
     */
    public function iHaveAAccount($arg1)
    {
        // throw new PendingException();
        \PHPUnit\Framework\Assert::assertTrue(null != $_ENV["${arg1}_tester_email"], "${arg1}_tester_email  is not empty");
        \PHPUnit\Framework\Assert::assertTrue(null != $_ENV["${arg1}_tester_password"], "${arg1}_tester_password is not empty");


    }

  /**
     * @Given /^I don\'t have a Gigadb account for my "([^"]*)" account email$/
     */
    public function iDonTHaveAGigadbAccountForMyAccountEmail($arg1)
    {
        $tester_email = $_ENV["${arg1}_tester_email"];
        \PHPUnit\Framework\Assert::assertTrue('user@gigadb.org' != $tester_email, "$tester_email is not the default user account");
        \PHPUnit\Framework\Assert::assertTrue('admin@gigadb.org' != $tester_email, "$tester_email is not the default admin account");
    }


    /**
     * @Given /^I login to Gigadb as an admin$/
     */
    public function iLoginToGigadbAsAnAdmin()
    {
      
        $this->getSession()->getPage()->fillField("LoginForm_username", "admin@gigadb.org");
        $this->getSession()->getPage()->fillField("LoginForm_password", "gigadb");
        $this->getSession()->getPage()->pressButton("Login");
    }


    /**
     * @Given /^I have a Gigadb account for my "([^"]*)" account email$/
     */
    public function iHaveAGigadbAccountForMyAccountEmail($arg1)
    {
       $tester_email = $_ENV["${arg1}_tester_email"];
       $this->assertPageContainsText($tester_email);

    }



    /**
     * @Given /^I click on the "([^"]*)" button$/
     */
    public function iClickOnTheButton($arg1)
    {
        $this->clickLink($arg1);
        $session=  $this->getSession('Goutte');
    }

    /**
     * @Given /^I authorise Gigadb for "([^"]*)"$/
     */
    public function iAuthoriseGigadbFor($arg1)
    {
        $login = $_ENV["{$arg1}_tester_email"];
        $password = $_ENV["${arg1}_tester_password"];
        $this->fillField("username_or_email", $login);
        $this->fillField("password", $password);

        $this->pressButton("Sign In");

        $this->assertResponseStatus(200);
    }

    /**
     * @Then /^I should be redirected$/
     */
    public function iShouldBeRedirected()
    {
        $this->clickLink('click here to continue');
    }



    /**
     * @Then /^I\'m logged in into the Gigadb web site$/
     */
    public function iMLoggedInIntoTheGigadbWebSite()
    {
        $this->assertPageContainsText("GigaDB Page");
    }

    /**
     * @Given /^a new Gigadb account is created with my "([^"]*)" details$/
     */
    public function aNewGigadbAccountIsCreatedWithMyDetails($arg1)

    {

        $email = $_ENV["${arg1}_tester_email"];
        $first_name = $_ENV["${arg1}_tester_first_name"];
        $last_name = $_ENV["${arg1}_tester_last_name"];
        $this->visit('/user/view_profile');
        $this->assertPageContainsText($arg1);
        $this->assertPageContainsText($email);
        $this->assertPageContainsText($first_name);
        $this->assertPageContainsText($last_name);
        
    }



    /* -------------------------------------------------------- utility functions and hooks -----------------*/

    /**
     * @AfterStep
    */
    public function takeSnapshotAfterFailedStep($event)
    {
        if ($event->getResult() == 4) {
            if ($this->getSession()->getDriver() instanceof \Behat\Mink\Driver\Selenium2Driver) {
                $screenshot = $this->getSession()->getDriver()->getScreenshot();
                $content = $this->getSession()->getDriver()->getContent();
                file_put_contents('/tmp/bgi.png', $screenshot);
                file_put_contents('/tmp/bgi.html', $content);
            }
        }
    }





}
