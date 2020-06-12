<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * Contains the steps definitions used in affiliate-login.feature
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 * @see http://docs.behat.org/en/latest/quick_start.html#defining-steps
 *
 * @uses \GigadbWebsiteContext For resetting the database
 * @uses \Behat\MinkExtension\Context\MinkContext For controlling the web browser
 * @uses \PHPUnit_Framework_Assert
 */
class AffiliateLoginContext implements Context
{
    /**
     * @var array $keys_map 2-dimensional associate array to access en variables for API credentials
     *
     * @todo extract this map into a module as it is also going to be used in UserIdentity's revoke_token
    */
    private $keys_map = array('Facebook' => array('api_key' => 'FACEBOOK_APP_ID', 'client_key' => 'FACEBOOK_APP_SECRET'),
                               'Google' => array('api_key' => 'GOOGLE_CLIENT_ID', 'client_key' => 'GOOGLE_SECRET'),
                               'Twitter' => array('api_key' => 'TWITTER_KEY', 'client_key' => 'TWITTER_SECRET'),
                               'LinkedIn' => array('api_key' => 'LINKEDIN_API_KEY', 'client_key' => 'LINKEDIN_SECRET_KEY'),
                               'Orcid' => array('api_key' => 'ORCID_CLIENT_ID', 'client_key' => 'ORCID_CLIENT_SECRET'),
                           );

    /**
     * @var \Behat\MinkExtension\Context\MinkContext
     */
    private $minkContext;

    /**
     * @var GigadbWebsiteContext
     */
    private $gigadbWebsiteContext;

    /**
     * The method to retrieve needed contexts from the Behat environment for non-login features
     *
     * @param BeforeScenarioScope $scope parameter needed to retrieve contexts from the environment
     *
     * @BeforeScenario ~@login
     *
    */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->minkContext = $environment->getContext('Behat\MinkExtension\Context\MinkContext');
        $this->gigadbWebsiteContext = $environment->getContext('GigadbWebsiteContext');
    }


    /**
     * @Given /^test users are loaded$/
     */
    public function testUsersAreLoaded()
    {
        foreach ($this->keys_map as $key => $value) {
            PHPUnit_Framework_Assert::assertTrue(null != getenv("${key}_tester_email"),"null != getenv('${key}_tester_email')");
        }
    }


    /**
     * @Given /^Gigadb has a "([^"]*)" API keys$/
     */
    public function gigadbHasAApiKeys($arg1)
    {
        $_SERVER['REQUEST_URI'] = 'foobar';
        $_SERVER['HTTP_HOST'] = 'foobar';

        $api_key = getenv($this->keys_map[$arg1]['api_key']);
        $client_key = getenv($this->keys_map[$arg1]['client_key']);

        PHPUnit_Framework_Assert::assertTrue('' != $api_key, "api_key for $arg1 is not empty");
        PHPUnit_Framework_Assert::assertTrue('' != $client_key, "client_key for $arg1 is not empty");

    }




    /**
     * @Given /^I have a "([^"]*)" account$/
     */
    public function iHaveAAccount($arg1)
    {
        // throw new PendingException();
        PHPUnit_Framework_Assert::assertTrue(null != $_ENV["${arg1}_tester_email"], "${arg1}_tester_email  is not empty");
        PHPUnit_Framework_Assert::assertTrue(null != $_ENV["${arg1}_tester_password"], "${arg1}_tester_password is not empty");


    }


    /**
     * @Given /^The "([^"]*)" account has not authorised login to GigaDB web site$/
     */
    public function theAccountHasNotAuthorisedLoginToGigadbWebSite($arg1)
    {
        if ("Facebook" == $arg1) {
            $client = new \GuzzleHttp\Client([
                // Base URI is used with relative requests
                'base_uri' => 'https://graph.facebook.com',
                // You can set any number of default request options.
                'timeout'  => 2.0,
            ]);
            $response = $client->request("DELETE", "/102733567195512/permissions", ['query' => ['access_token' => getenv("${arg1}_access_token")]]);
            // $request->setPath('/102733567195512/permissions');
            //  $request->getQuery()
            //         ->set('access_token', $_ENV["${arg1}_access_token"]);
            // $response = $facebook->send($request);
            $body = json_decode($response->getBody(true),true);
            PHPUnit_Framework_Assert::assertTrue("true" == $body["success"],'Test users de-authorised');

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
        PHPUnit_Framework_Assert::assertTrue($expected_nb_occurrences == $nb_ocurrences, "I don't have a gigadb account for $email");

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
        PHPUnit_Framework_Assert::assertTrue($expected_nb_occurrences == $nb_ocurrences, "I have a gigadb account for $email");

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
        PHPUnit_Framework_Assert::assertTrue($expected_nb_occurrences == $nb_ocurrences, "I have a gigadb account for $email");

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
        PHPUnit_Framework_Assert::assertTrue($expected_nb_occurrences == $nb_ocurrences, "I have a gigadb account for $email");

    }



    /**
     * @When /^I click on the "([^"]*)" button$/
     */
    public function iClickOnTheButton($arg1)
    {
        $this->minkContext->clickLink($arg1);
    }

     /**
     * @When /^I sign in to "([^"]*)"$/
     */
    public function iSignInTo($arg1)
    {
        $login = $_ENV["{$arg1}_tester_email"];
        $password = $_ENV["${arg1}_tester_password"];

        if ($arg1 == "Twitter") {
            $this->minkContext->fillField("username_or_email", $login);
            $this->minkContext->fillField("password", $password);

            $this->minkContext->pressButton("Sign In");
        }
        else if ($arg1 == "Facebook") {
            $this->minkContext->fillField("email", $login);
            $this->minkContext->fillField("pass", $password);

            $this->minkContext->pressButton("login");
            sleep(5);

        }
        else if ($arg1 == "Google") {
            $this->minkContext->fillField("Email", $login);
            if( $this->minkContext->getSession()->getPage()->findButton("下一個") ) {
                $this->minkContext->pressButton("下一個");
            }
            elseif( $this->minkContext->getSession()->getPage()->findButton("Next") ) {
                $this->minkContext->pressButton("Next");
            }
            sleep(5);
            $this->minkContext->fillField("Passwd", $password);
            if( $this->minkContext->getSession()->getPage()->findButton("登入") ) {
                $this->minkContext->pressButton("登入");
            }
            elseif( $this->minkContext->getSession()->getPage()->findButton("Sign in") ) {
                $this->minkContext->pressButton("Sign in");
            }

        }
        else if ($arg1 == "LinkedIn") {
            $this->minkContext->fillField("session_key", $login);
            $this->minkContext->fillField("session_password", $password);
            if ( $this->minkContext->getSession()->getPage()->hasButton("Sign In") ) {
                $this->minkContext->pressButton("Sign In");
            }
            elseif( $this->minkContext->getSession()->getPage()->hasButton("Sign in")) {
                $this->minkContext->pressButton("Sign in");
            }

        }
        else if ($arg1 == "Orcid") {
            $this->minkContext->fillField("userId", $login);
            $this->minkContext->fillField("password", $password);
            $this->minkContext->pressButton("Sign into ORCID");
            sleep(15);

        }
        else {
            throw new Exception();
        }

        // $this->assertResponseStatus(200);
    }

    /**
     * @When /^I authorise gigadb for "([^"]*)"$/
    */
    public function iAuthoriseGigadbFor($arg1)
    {
        $session = $this->minkContext->getSession();
        $driver = $session->getDriver();

        if ($arg1 == "Twitter") {
            $this->minkContext->clickLink('click here to continue');
        }
        else if ($arg1 == "Facebook") {

            // $xpath = '//input[@type="submit" and @value="Continue as Elizabeth"]' ;
            // $elements = $driver->find($xpath) ;

            // if( 0 == count($elements) ) {
            //     throw new \Exception("The element is not found");
            // }
            // else {
            //     //print_r("pressing the button ". $driver->getHtml( $elements[0]->getParent()->getXpath() ) );
            //     $elements[0]->press();
            // }

            // sleep(5);
            $this->minkContext->getSession()->wait(10000, '(typeof jQuery != "undefined" && 0 === jQuery.active)');

            $csspath = 'html body input[value="Continue as Elizabeth"]';
            $authorize_button = $session->getPage()->find('css',$csspath);
            if ($authorize_button) {
                echo PHP_EOL."found authorize button!".PHP_EOL;
                $authorize_button->press();
                sleep(5);
            }
            else {
                echo PHP_EOL."authorize button not found".PHP_EOL;
            }


        }
        else if ($arg1 == "Google") {

            $this->minkContext->getSession()->wait(10000, '(typeof jQuery != "undefined" && 0 === jQuery.active)');
            if( $this->minkContext->getSession()->getPage()->findButton("Allow") ) {
                $this->minkContext->pressButton("Allow");
            }
        }
        else if ($arg1 == "LinkedIn") {
            $this->minkContext->getSession()->wait(15000, '(typeof jQuery != "undefined" && 0 === jQuery.active)');
            if( $this->minkContext->getSession()->getPage()->findButton("Allow") ) {
                $this->minkContext->pressButton("Allow");
            }
        }
        else if ($arg1 == "Orcid") {
            $this->minkContext->getSession()->wait(15000, '(typeof jQuery != "undefined" && 0 === jQuery.active)');
            // PHPUnit_Framework_Assert::assertTrue($this->minkContext->getSession()->getPage()->hasField("enablePersistentToken"), "Authorize checkbox");
            // PHPUnit_Framework_Assert::assertTrue($this->minkContext->getSession()->getPage()->hasButton("authorize"), "Authorize button");
            $the_checkbox = $this->minkContext->getSession()->getPage()->findField("enablePersistentToken");
            $the_button = $this->minkContext->getSession()->getPage()->findButton("authorize");
            //var_dump($the_checkbox);
            //var_dump($the_button);
            // $the_checkbox->check();
            if ($the_button) {
                sleep(5);
                $the_button->press();
                sleep(5);
            }
        }
    }



    /**
     * @Then /^I\'m logged in into the Gigadb web site$/
     */
    public function iMLoggedInIntoTheGigadbWebSite()
    {
        $this->minkContext->assertPageContainsText("GigaDB Page");

    }

    /**
     * @Given /^a new Gigadb account is created with my "([^"]*)" details$/
     */
    public function aNewGigadbAccountIsCreatedWithMyDetails($arg1)

    {

        sleep(2);
        $email = $_ENV["${arg1}_tester_email"];
        if("Orcid" == $arg1) {
            $uid = $_ENV["${arg1}_tester_uid"];
            $email = "${uid}@Orcid";
        }

        $expected_nb_occurrences = 1;

        $nb_ocurrences = $this->countEmailOccurencesInUserList($email);
        PHPUnit_Framework_Assert::assertTrue($expected_nb_occurrences == $nb_ocurrences, "Success creating a new gigadb account");

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


    public function countEmailOccurencesInUserList($email=null) {

        $dbconn = pg_connect("host=database dbname=gigadb user=gigadb password=vagrant port=5432") or die('Could not connect: ' . pg_last_error());
        $query = "select email from gigadb_user where email='${email}';";
        $result = pg_query($query) or die('Query failed: ' . pg_last_error());

        $arr = pg_fetch_all($result);
        pg_free_result($result);
        pg_close($dbconn);
        if (false == $arr ) {
            return 0;
        }
        else {
            return count($arr);
        }

    }

    private function createNewUserAccountForEmail($email) {
        $sql = "insert into gigadb_user(id, email,password,first_name, last_name, affiliation,role,is_activated,username) values(1,'${email}','12345678','John','Doe','ETH','user',true,'johndoe')" ;

        $dbconn = pg_connect("host=database dbname=gigadb user=gigadb password=vagrant port=5432") or die('Could not connect: ' . pg_last_error());
        pg_query($dbconn, $sql);
        pg_close($dbconn);

    }

    private function createNewUserAccountForUidAndEmail($provider,$uid,$email) {
        $sql = "insert into gigadb_user(id, email,password,first_name, last_name, affiliation,role,is_activated,username,orcid_id) values(1,'${email}','12345678','John','Doe','ETH','user',true,'johndoe','${uid}')" ;

        $dbconn = pg_connect("host=database dbname=gigadb user=gigadb password=vagrant port=5432") or die('Could not connect: ' . pg_last_error());
        pg_query($dbconn, $sql);
        pg_close($dbconn);

    }


    /**
     * Initialize the database when running tests for affiliate-login.feature and login.feature
     *
     * @BeforeScenario @login
     *
     * @param BeforeScenarioScope $scope parameter needed to retrieve contexts from the environment
     *
     * @uses GigadbWebsiteContext::terminateDbBackend
     * @uses GigadbWebsiteContext::truncateTable
     * @uses GigadbWebsiteContext::loadUserData
     * @uses GigadbWebsiteContext::restartPhp
    */
    public function initialize_session(BeforeScenarioScope $scope) {

        $environment = $scope->getEnvironment();
        $this->minkContext = $environment->getContext('Behat\MinkExtension\Context\MinkContext');
        $this->gigadbWebsiteContext = $environment->getContext('GigadbWebsiteContext');

        $this->minkContext->visit("/site/revoke");
        sleep(3);
        $this->gigadbWebsiteContext->terminateDbBackend("gigadb");
        $this->gigadbWebsiteContext->removeCreatedUsers();
        $this->gigadbWebsiteContext->restartPhp();
    }

    /**
     * @AfterScenario @login
    */
    public function reset_stop_session($event) {

        $this->minkContext->visit("/site/revoke");
        sleep(3);
        $this->minkContext->getSession()->stop();

    }



}
