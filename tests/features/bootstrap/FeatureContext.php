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
                           ); //TODO: extract this map into a module as it is also going to be used in UserIdentity's revoke_token
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
     * @Given /^The "([^"]*)" account has not authorised login to GigaDB web site$/
     */
    public function theAccountHasNotAuthorisedLoginToGigadbWebSite($arg1)
    {
        if ("Facebook" == $arg1) {
            $facebook = new Guzzle\Http\Client("https://graph.facebook.com");
            $request = $facebook->createRequest("DELETE");
            $request->setPath('/102733567195512/permissions');
             $request->getQuery()
                    ->set('access_token', $_ENV["${arg1}_access_token"]);
            $response = $facebook->send($request);
            $body = json_decode($response->getBody(true),true);
            \PHPUnit\Framework\Assert::assertTrue("true" == $body["success"],'Test users de-authorised');

        }

    }


    /**
     * @Given /^I don\'t have a Gigadb account for my "([^"]*)" account email$/
     */
    public function iDonTHaveAGigadbAccountForMyAccountEmail($arg1)
    {
        $email = $_ENV["${arg1}_tester_email"];
        $expected_nb_occurrences =  0 ;

        $nb_ocurrences = $this->countEmailOccurencesInUserList($email);
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


       $nb_ocurrences = $this->countEmailOccurencesInUserList($email);
        \PHPUnit\Framework\Assert::assertTrue($expected_nb_occurrences == $nb_ocurrences, "I have a gigadb account for $email");

    }

    /**
     * @Given /^I have a Gigadb account for my "([^"]*)" uid$/
     */
    public function iHaveAGigadbAccountForMyUid($arg1)
    {
       $uid = $_ENV["${arg1}_tester_uid"];
       $email = $_ENV["${arg1}_tester_email"];
       $expected_nb_occurrences =  1 ;

       $this->createNewUserAccountForUidAndEmail(strtolower($arg1),$uid,$email);


       $nb_ocurrences = $this->countEmailOccurencesInUserList($email);
        \PHPUnit\Framework\Assert::assertTrue($expected_nb_occurrences == $nb_ocurrences, "I have a gigadb account for $email");

    }

    /**
     * @Given /^I have a Gigadb account with a different email$/
     */
    public function iHaveAGigadbAccountWithADifferentEmail()
    {
       $email = "foo@bar.me";
       $expected_nb_occurrences =  1 ;

       $this->createNewUserAccountForEmail($email);


       $nb_ocurrences = $this->countEmailOccurencesInUserList($email);
        \PHPUnit\Framework\Assert::assertTrue($expected_nb_occurrences == $nb_ocurrences, "I have a gigadb account for $email");

    }



    /**
     * @Given /^I click on the "([^"]*)" button$/
     */
    public function iClickOnTheButton($arg1)
    {
        $this->clickLink($arg1);
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
        else if ($arg1 == "Google") {
            $this->fillField("Email", $login);
            $this->pressButton("Next");
            sleep(5);
            $this->fillField("Passwd", $password);
            $this->pressButton("Sign in");
            
        }
        else if ($arg1 == "LinkedIn") {
            $this->fillField("session_key", $login);
            $this->fillField("session_password", $password);
            $this->pressButton("Allow access");
            
        }
        else if ($arg1 == "ORCID") {
            $this->fillField("userId", $login);
            $this->fillField("password", $password);
            $this->pressButton("Sign into ORCID");
            sleep(15);
            $this->printCurrentUrl();
            
        }
        else {
            throw new Exception();
        }

        // $this->assertResponseStatus(200);
    }

    /**
     * @Then /^I should be redirected from "([^"]*)"$/
     */
    public function iShouldBeRedirectedFrom($arg1)
    {
        $session = $this->getSession();
        $driver = $session->getDriver();

        if ($arg1 == "Twitter") {
            $this->clickLink('click here to continue');
        }
        else if ($arg1 == "Facebook") {
            
            $xpath = '//button[@type="submit" and contains(., "Continue")]' ;
            $elements = $driver->find($xpath) ;

            if( 0 == count($elements) ) {
                throw new \Exception("The element is not found");
            }
            else {
                //print_r("pressing the button ". $driver->getHtml( $elements[0]->getParent()->getXpath() ) );
                $elements[0]->press();
            }

            // sleep(10);
            $this->getSession()->wait(10000, '(typeof jQuery != "undefined" && 0 === jQuery.active)');
            // $this->assertPageContainsText("GigaDB Page");
            


        }
        else if ($arg1 == "Google") {

            $this->getSession()->wait(10000, '(typeof jQuery != "undefined" && 0 === jQuery.active)');
            $this->pressButton("Allow");

        }
        else if ($arg1 == "ORCID") {
            $this->getSession()->wait(15000, '(typeof jQuery != "undefined" && 0 === jQuery.active)');
            \PHPUnit\Framework\Assert::assertTrue($this->getSession()->getPage()->hasField("enablePersistentToken"), "Authorize checkbox");
            \PHPUnit\Framework\Assert::assertTrue($this->getSession()->getPage()->hasButton("authorize"), "Authorize button");
            $the_checkbox = $this->getSession()->getPage()->findField("enablePersistentToken");
            $the_button = $this->getSession()->getPage()->findButton("authorize");
            //var_dump($the_checkbox);
            //var_dump($the_button);
            $the_checkbox->check();
            sleep(5);
            $the_button->press();
            sleep(10);
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

        sleep(2);
        $email = $_ENV["${arg1}_tester_email"];
        if("ORCID" == $arg1) {
            $uid = $_ENV["${arg1}_tester_uid"];
            $email = "${uid}@Orcid";
        }
        
        $expected_nb_occurrences = 1; 

        $nb_ocurrences = $this->countEmailOccurencesInUserList($email);
        \PHPUnit\Framework\Assert::assertTrue($expected_nb_occurrences == $nb_ocurrences, "Success creating a new gigadb account");
        
    }

     /**
     * @Given /^no new gigadb account is created for my "([^"]*)" account email$/
     */
    public function noNewGigadbAccountIsCreatedForMyAccountEmail($arg1)
    {
        $email = $_ENV["${arg1}_tester_email"];
        $expected_nb_occurrences = 1; 

        $nb_ocurrences = $this->countEmailOccurencesInUserList($email);

        if ($expected_nb_occurrences != $nb_ocurrences) {
            throw new \Exception('Found '.$nb_ocurrences.' occurences of "'.$email.'" when expecting '.$expected_nb_occurrences);
        }
    }



    /* -------------------------------------------------------- utility functions and hooks -----------------*/


    private function countEmailOccurencesInUserList($email=null) {
        $nb_ocurrences = 0 ;
        print_r("Querying the database for emails... ");
        exec("vagrant ssh -c 'sudo -Hiu postgres /usr/bin/psql gigadb -qt -c \"select email from gigadb_user\"'", $output, $err);
        $trimmer = function($value) {
            return trim($value);
        };
        $squeezer = function($value) {
            return $value != "" ;
        };
        $trimmed_emails = array_map($trimmer,array_filter($output, $squeezer)) ;
        // var_dump($trimmed_emails);
        $occurrences = array_keys($trimmed_emails, $email);

        $nb_ocurrences =  count($occurrences);
        print_r("Found {$nb_ocurrences} of {$email}");
        return $nb_ocurrences;
    }

    private function createNewUserAccountForEmail($email) {
        $sql = "insert into gigadb_user(id, email,password,first_name, last_name, affiliation,role,is_activated,username) values(1,:'email','12345678','John','Doe','ETH','user',true,'johndoe')" ;
        $psql_command = "sudo -Hiu postgres /usr/bin/psql -v email='$email' gigadb" ;
        file_put_contents("sql/temp_command.sql", $sql);
        print_r("Creating a test user account... ");
        exec("vagrant ssh -c \"$psql_command < /vagrant/sql/temp_command.sql\"",$output);
        // var_dump($output);

    }

    private function createNewUserAccountForUidAndEmail($provider,$uid,$email) {
        $sql = "insert into gigadb_user(id, email,password,first_name, last_name, affiliation,role,is_activated,username,orcid_id) values(1,:'email','12345678','John','Doe','ETH','user',true,'johndoe',:'uid')" ; 
        $psql_command = "sudo -Hiu postgres /usr/bin/psql -v email='$email' -v uid='$uid' gigadb" ;
        file_put_contents("sql/temp_command.sql", $sql);

        print_r("Creating a test user account... ");
        exec("vagrant ssh -c \"$psql_command < /vagrant/sql/temp_command.sql\"",$output);
        // var_dump($output);

    }


    public static function initialize_database()
    {
        print_r("Initializing the database... ");
        exec("vagrant ssh -c \"sudo -Hiu postgres /usr/bin/psql < /vagrant/sql/reset.sql\"",$kill_output);
        sleep(5) ; # pad the adminstrative operations to cater for latency in order to avoid fatal error
    }


    /**
     * @BeforeScenario
    */
    public function initialize_session() { 
        $this->visit("/site/revoke");
        sleep(3);
        Self::initialize_database();
        // $this->assertHomepage();
        clearstatcache() ;
        $session = $this->getSession();
        $driver = $session->getDriver();
        if ($driver instanceof GoutteDriver) {
            print_r("Restarting GoutteDriver");
            $driver->getClient()->restart();
        }
        $session->start();


    }

    /**
     * @AfterScenario
    */
    public function reset_stop_session($event) {

        $this->visit("/site/revoke");
        sleep(3);
        // $this->assertHomepage();
        $sqlfile = "sql/temp_command.sql" ;
        $this->getSession()->reset();
        $this->getSession()->stop();
        if (file_exists($sqlfile) && $event->getResult() == 0) {
            $deleted =unlink($sqlfile);
            if (!$deleted) {
                throw new Exception("${sqlfile} could not be deleted");
            }
        }
    }

    /**
     * @AfterSuite
    */
    public static function reset_db($event) {

        if (true == $event->isCompleted()) {
            Self::initialize_database();
        }
    }



    /**
     * @AfterStep
    */
    public function debugStep($event)
    {
        if ($event->getResult() == 4 ) {

            $this->printCurrentUrl();

            try { # take a snapshot of web page

                $content = $this->getSession()->getDriver()->getContent();
                $file_and_path = sprintf('%s_%s_%s',"content", date('U'), uniqid('', true)) ;
                file_put_contents("/tmp/".$file_and_path.".html", $content);

                if (PHP_OS === "Darwin" && PHP_SAPI === "cli") {
                    // exec('open -a "Preview.app" ' . $file_and_path.".png");
                    exec('open -a "Safari.app" ' . $file_and_path.".html");
                }
            }
            catch (Behat\Mink\Exception\DriverException $e) {
                print_r("Unable to take a snatpshot");
            }

        }
    }


}
