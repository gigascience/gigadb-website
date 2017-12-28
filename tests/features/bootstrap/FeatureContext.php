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
     * @Given /^the Gigadb database has only the default users$/
     */
    public function theGigadbDatabaseHasOnlyTheDefaultUsers()
    {
        exec("vagrant ssh -c \"sudo -u postgres /usr/bin/psql -c 'drop database gigadb'\"");
        exec("vagrant ssh -c \"sudo -u postgres /usr/bin/psql -c 'create database gigadb owner gigadb'\"");
        exec("vagrant ssh -c \"psql -U gigadb -h localhost gigadb < /vagrant/sql/gigadb_testdata.sql\"");
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
        $email = $_ENV["${arg1}_tester_email"];
        $expected_nb_occurrences =  0 ;
        $nb_ocurrences = $this->countEmailOccurencesInUserList($email,$expected_nb_occurrences);
        \PHPUnit\Framework\Assert::assertTrue($expected_nb_occurrences == $nb_ocurrences, "I don't have a gigadb account for $email");

    }


    /**
     * @Given /^I have a Gigadb account for my "([^"]*)" account email$/
     */
    public function iHaveAGigadbAccountForMyAccountEmail($arg1)
    {
       $email = $_ENV["${arg1}_tester_email"];
       $expected_nb_occurrences =  1 ;

       $this->createNewUserAccountForEmail($email);

       $nb_ocurrences = $this->countEmailOccurencesInUserList($email,$expected_nb_occurrences);
        \PHPUnit\Framework\Assert::assertTrue($expected_nb_occurrences == $nb_ocurrences, "I have a gigadb account for $email");

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

        if ($arg1 == "Twitter") {        
            $this->fillField("username_or_email", $login);
            $this->fillField("password", $password);

            $this->pressButton("Sign In"); 
        }
        else if ($arg1 == "Facebook") {
            $this->fillField("email", $login);
            $this->fillField("pass", $password);

            $this->pressButton("loginbutton");

        }

        $this->assertResponseStatus(200);
    }

    /**
     * @Then /^I should be redirected from "([^"]*)"$/
     */
    public function iShouldBeRedirectedFrom($arg1)
    {
        if ($arg1 == "Twitter") {
            $this->clickLink('click here to continue');
        }
        else if ($arg1 == "Facebook") {
            $text = "Continue as Elizabeth";
            // $xpath = "//button[@type='submit']" ;
            // $xpath = '//*[@id="u_0_s"]/div[2]/div[1]/div[1]/button' ;
            // $xpath = '//div[2]/div[1]/div[1]/button' ;
            // $xpath = '//button[@type="submit" and contains(., "Continue")]' ;
            //$xpath = '//div[2]/div[1]/div[1]/button' ;
            //$xpath = '//button' ;
            //$element = $this->getSession()->getPage()->find('xpath', $this->getSession()->getSelectorsHandler()->selectorToXpath('xpath', $xpath) );
            //$element = $this->getSession()->getPage()->find('xpath', $xpath );
            // $element->find();
            $selectorsHandler = $this->getSession()->getSelectorsHandler();
            $element = $this->getSession()->getPage()->find(
                'named',
                array(
                    'button',
                    $selectorsHandler->xpathLiteral($text)
                )
            );

            if (null === $element) {
                throw new \Exception("The element is not found");
            }
            else {
                // var_dump($element->getHtml());
                $element->press();
            }

        }
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

     /**
     * @Given /^no new gigadb account is created for my "([^"]*)" account email$/
     */
    public function noNewGigadbAccountIsCreatedForMyAccountEmail($arg1)
    {
        $email = $_ENV["${arg1}_tester_email"];
        $expected_nb_occurrences = 1; 

        $nb_ocurrences = $this->countEmailOccurencesInUserList($email,$expected_nb_occurrences);

        if ($expected_nb_occurrences != $nb_ocurrences) {
            throw new \Exception('Found '.$nb_ocurrences.' occurences of "'.$email.'" when expecting '.$expected_nb_occurrences);
        }
    }



    /* -------------------------------------------------------- utility functions and hooks -----------------*/


    private function countEmailOccurencesInUserList($email, $expected_nb_occurrences) {
        $this->visit('/site/logout');
        $this->visit('/site/login');
        $this->getSession()->getPage()->fillField("LoginForm_username", "admin@gigadb.org");
        $this->getSession()->getPage()->fillField("LoginForm_password", "gigadb");
        $this->getSession()->getPage()->pressButton("Login");
        $this->assertResponseStatus(200);
        $this->visit('/user/');
        $this->assertResponseStatus(200);
        $content = $this->getSession()->getPage()->getText();
        $nb_ocurrences = substr_count($content, $email);

        $this->getSession()->visit('/site/logout');
        return $nb_ocurrences;
    }

    private function createNewUserAccountForEmail($email) {
        $this->visit('/user/create');
        $this->getSession()->getPage()->fillField("User_email", $email);
        $this->getSession()->getPage()->fillField("User_first_name", "First");
        $this->getSession()->getPage()->fillField("User_last_name", "Last");
        $this->getSession()->getPage()->fillField("User_password", "1234");
        $this->getSession()->getPage()->fillField("User_password_repeat", "1234");
        $this->getSession()->getPage()->fillField("User_affiliation", "Testing");
        $this->getSession()->getPage()->fillField("User_preferred_link", "EBI");
        $this->getSession()->getPage()->fillField("User_newsletter", "1");

        $this->getSession()->getPage()->pressButton("Register");

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
                $file_and_path = "/tmp/behat_page.png" ;
                file_put_contents($file_and_path, $screenshot);

                if (PHP_OS === "Darwin" && PHP_SAPI === "cli") {
                    exec('open -a "Preview.app" ' . $file_and_path);
                }


            }else if ($this->getSession()->getDriver() instanceof \Behat\Mink\Driver\GoutteDriver) {

                $html_data = $this->getSession()->getDriver()->getContent();
                $file_and_path = '/tmp/behat_page.html';
                file_put_contents($file_and_path, $html_data);

                if (PHP_OS === "Darwin" && PHP_SAPI === "cli") {
                    exec('open -a "Safari.app" ' . $file_and_path);
                };

            }
        }
    }






}
