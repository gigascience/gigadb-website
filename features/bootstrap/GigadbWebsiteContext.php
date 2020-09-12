<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;

/**
 * Contains steps definitions and utility functions to be used in all Context classes
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 * @see http://docs.behat.org/en/latest/quick_start.html#defining-steps
 *
 * @uses AffiliateLoginContext For checking user exists in database from email
 * @uses \Behat\MinkExtension\Context\MinkContext For controlling the web browser
 * @uses \PHPUnit_Framework_Assert
 */
class GigadbWebsiteContext implements Context
{
    /**
     * @var string $admin_login login of a valid admin user, by default from env "GIGADB_admin_tester_email"
     */
    private $admin_login;

    /**
     * @var string $admin_password passsword of a valid admin user, by default from env "GIGADB_admin_tester_password"
    */
    private $admin_password;

    /**
     * @var string $user_login login of a valid user, no default
     */
    private $user_login;

    /**
     * @var string $user_password password of a valid user, no default
     */
    private $user_password;

    /**
     * @var int $time_start used by the timer steps
     */
    private $time_start;

    /**
     * @var \Behat\MinkExtension\Context\MinkContext
     */
    private $minkContext;

    /**
     * @var AffiliateLoginContext
     */
    private $affiliateLoginContext;

    /**
     * @var Array $dbConf configuration data for database
     */
    public $dbConf;

    public function __construct()
    {
        $this->admin_login = getenv("GIGADB_admin_tester_email");
        $this->admin_password = getenv("GIGADB_admin_tester_password") ;
        $this->dbConf['host'] = getenv('GIGADB_HOST');
        $this->dbConf['db'] = getenv('GIGADB_DB');
        $this->dbConf['user'] = getenv('GIGADB_USER');
        $this->dbConf['password'] = getenv('GIGADB_PASSWORD');
    }


    /**
     * The method to retrieve needed contexts from the Behat environment
     *
     * @param BeforeScenarioScope $scope parameter needed to retrieve contexts from the environment
     *
     * @BeforeScenario
     *
    */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->minkContext = $environment->getContext('Behat\MinkExtension\Context\MinkContext');
        $this->affiliateLoginContext = $environment->getContext('AffiliateLoginContext');
    }

    /**
     * debugStep
     *
     * utility hook that create a screenshot of the browser screen when a step fails
     *
     * @param AfterStepScope $scope parameter needed to retrieve step result
     *
     * @AfterStep
    */
    public function debugStep(AfterStepScope $scope)
    {
        if ( 99 === $scope->getTestResult()->getResultCode() ) {
            try { # take a snapshot of web page
                $this->minkContext->printCurrentUrl();
                $content = $this->minkContext->getSession()->getDriver()->getContent();
                $file_and_path = sprintf('%s_%s_%s',"content", date('U'), uniqid('', true)) ;
                file_put_contents("/tmp/".$file_and_path.".html", $content);
                // if (PHP_OS === "Darwin" && PHP_SAPI === "cli") {
                //     // exec('open -a "Preview.app" ' . $file_and_path.".png");
                //     exec('open -a "Safari.app" ' . $file_and_path.".html");
                // }

                // $driver = $this->getSession()->getDriver();
                // if ($driver instanceof Behat\Mink\Driver\Selenium2Driver) {
                //     file_put_contents('/tmp/latest.png', $this->getSession()->getDriver()->getScreenshot());
                // }
                // else {
                //     print_r("cannot take screenshot with this driver");
                //     print_r(var_dump($driver));
                // }
            }
            catch (Behat\Mink\Exception\DriverException $e) {
                print_r("Unable to take a snatpshot");
            }
        }
    }


     /**
     * @Given /^an admin user exists$/
     */
    public function anAdminUserExists()
    {
        if ( null != $this->admin_login ) {
            $nb_ocurrences = $this->affiliateLoginContext->countEmailOccurencesInUserList( $this->admin_login );
            PHPUnit_Framework_Assert::assertTrue(1 == $nb_ocurrences, "admin email exists in database");
        }
        else {
            throw new Exception("No admin user set up");
        }
    }

    /**
     * @Given /^default admin user exists$/
     */
    public function defaultAdminUserExists()
    {
        $nb_ocurrences = $this->affiliateLoginContext->countEmailOccurencesInUserList( "admin@gigadb.org");
        PHPUnit_Framework_Assert::assertTrue(1 == $nb_ocurrences, "default admin email exists in database");
        if ( 1 == $nb_ocurrences  )  {
            $this->admin_login = "admin@gigadb.org" ;
            $this->admin_password = "gigadb";
        }
    }

    /**
     * @Given /^default user exists$/
     */
    public function defaultUserExists()
    {
       $nb_ocurrences = $this->affiliateLoginContext->countEmailOccurencesInUserList( "user@gigadb.org");
        PHPUnit_Framework_Assert::assertTrue(1 == $nb_ocurrences, "default user email exists in database");
        if ( 1 == $nb_ocurrences  )  {
            $this->user_login = "user@gigadb.org" ;
            $this->user_password = "gigadb";
        }
    }

     /**
     * @Given /^user "([^"]*)" is loaded$/
     */
    public function userIsLoaded($user)
    {
        $this->loadUserData($user);
        $this->user_login = "${user}@gigadb.org" ;
        $this->user_password = "gigadb";
    }

    /**
     * @Given /^dataset author "([^"]*)" is loaded$/
     */
    public function datasetAuthorIsLoaded($author)
    {
        $this->loadUserData($author);
    }


     /**
     * @Given /^I sign in as an admin$/
     */
    public function iSignInAsAnAdmin()
    {
         $this->minkContext->visit("/site/login");
         $this->minkContext->fillField("LoginForm_username", $this->admin_login);
         $this->minkContext->fillField("LoginForm_password", $this->admin_password);
         $this->minkContext->pressButton("Login");

         $this->minkContext->assertResponseContains("Admin");
    }

     /**
     * @Given /^I sign in as a user$/
     */
    public function iSignInAsAUser()
    {
        $this->minkContext->visit("/site/login");
        $this->minkContext->fillField("LoginForm_username", $this->user_login);
        $this->minkContext->fillField("LoginForm_password", $this->user_password);
        $this->minkContext->pressButton("Login");

        $this->minkContext->assertResponseNotContains("Administration");
        $this->minkContext->assertResponseContains("'s GigaDB Page");
    }

    /**
     * @Given /^Gigadb web site is loaded with "([^"]*)" data$/
     */
    public function gigadbWebSiteIsLoadedWithData($arg1)
    {
        print_r("Initializing the database with ${arg1}... ");
        $this->terminateDbBackend("gigadb");
        $this->dropCreateDb("gigadb");
        if ( preg_match("/\.pgdmp$/", $arg1) ) {
            exec("pg_restore -h database -p 5432 -U gigadb -d gigadb -v /var/www/sql/${arg1} 2>&1",$output);
            $this->restartPhp();
        }
        else {
            throw new Exception("cannot load database file ${arg1}");
        }


    }


    /**
     * @Given dataset :arg1 exists
     */
    public function datasetExists($arg1)
    {
        $sql = 'select identifier from dataset where identifier = $1';
        $dbconn = pg_connect("host={$this->dbConf['host']} dbname={$this->dbConf['db']} user={$this->dbConf['user']} password={$this->dbConf['password']} port=5432") or die('Could not connect: ' . pg_last_error());
        $resultRes = pg_query_params($dbconn, $sql, [$arg1]);
        $result = pg_fetch_array($resultRes, NULL, PGSQL_ASSOC);
        PHPUnit_Framework_Assert::assertNotNull($result);
        PHPUnit_Framework_Assert::assertEquals($arg1, $result['identifier']);
        pg_free_result($resultRes);
        pg_close($dbconn);
    }

    /**
     * @Given /^I take a screenshot named "([^"]*)"$/
     */
    public function itakeAScreenshot($name) {
        $driver = $this->minkContext->getSession()->getDriver();
        if ($driver instanceof Behat\Mink\Driver\Selenium2Driver) {
            file_put_contents("/tmp/screenshot_".$name.".png", $this->minkContext->getSession()->getDriver()->getScreenshot());
        }
        else {
            print_r("cannot take screenshot with this driver");
            print_r(var_dump($driver));
        }
    }

    /**
     * @Given /^I should see "([^"]*)" (\d+) time$/
     */
    public function iShouldSeeTime($text, $occurence)
    {
        $element = $this->minkContext->getSession()->getPage();
        $result = $element->findAll('xpath', "//*[contains(text(), '$text')]");

        if(count($result) == $occurence) {
            return;
        }
        else {
            throw  new Exception('"' . $text . '" was supposed to appear ' . $occurence . ' times, got ' . count($result) . ' instead');
        }
    }




    // ---  utility functions

    /**
     * Kill backend processes connected to the PostgreSQL database.
     *
     * This is necessary, because we cannot restore when processes are still connected to the database.
     * This function requires the pgsql PHP extension to be installed.
     *
     * @param string $dbname name of database to operate on
     *
    */
    public static function call_pg_terminate_backend($dbname) {
        print_r("Terminating DB Backend...".PHP_EOL);
        $sql = "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname='${dbname}' and pid <> pg_backend_pid()";
        $dbconn = pg_connect("host=database dbname=postgres user=gigadb password=vagrant port=5432") or die('Could not connect: ' . pg_last_error());
        pg_query($dbconn, $sql);
        pg_close($dbconn);

    }

    public function terminateDbBackend($dbname) {
        GigadbWebsiteContext::call_pg_terminate_backend($dbname);
    }

    /**
     * Drop the database and recreate it.
     *
     * This function requires the pgsql PHP extension to be installed.
     *
     * @param string $dbname name of database to operate on
     *
    */
    public static function recreateDB($dbname) {
        echo "Recreating database ${dbname}...".PHP_EOL;
        $sql_to_fence ="ALTER DATABASE $dbname WITH CONNECTION LIMIT 0;"; //avoid new connection during this process
        $sql_to_drop = "DROP DATABASE ${dbname}";
        $sql_to_create = "CREATE DATABASE ${dbname} OWNER gigadb";
        $dbconn = pg_connect("host=database dbname=postgres user=gigadb password=vagrant port=5432") or die('Could not connect: ' . pg_last_error());
        pg_query($dbconn, $sql_to_fence);
        pg_query($dbconn, $sql_to_drop);
        pg_query($dbconn, $sql_to_create);
        pg_close($dbconn);

    }

    public function dropCreateDb($dbname) {
        GigadbWebsiteContext::recreateDB($dbname);
    }

    /**
     * truncate a table. Used by some Context class to reset the gigadb_user table.
     *
     * This function requires the pgsql PHP extension to be installed.
     *
     * @param string $dbname name of database to operate on
     * @param string $tablename name of the table to truncate
     *
    */
    public function truncateTable($dbname,$tablename) {
        $sql = "TRUNCATE TABLE ${tablename} CASCADE";
        $dbconn = pg_connect("host={$this->dbConf['host']} dbname={$this->dbConf['db']} user={$this->dbConf['user']} password={$this->dbConf['password']} port=5432") or die('Could not connect: ' . pg_last_error());
        pg_query($dbconn, $sql);
        pg_close($dbconn);
        print_r("Truncated ${tablename} on ${dbname}");
    }

    /**
     * Restart the php-fpm docker container
     *
     * After the database has been recreated, we need to restart php-fpm
     * So it create a new valid connection. Not doing so will generate database errors on frontend.
     *
     * This function requires to be run in a container that has bind-mount
     * the Docker daemon UNIX socker: /var/run/docker.sock
     * So that we can send to it the API command to restart a container
     *
     *
    */
    public static function containerRestart()
    {
        $compose_name=getenv("COMPOSE_PROJECT_NAME");
        print_r("Restarting php container for ${compose_name} project...".PHP_EOL);
        exec("/var/www/ops/scripts/restart_php.sh",$output);
        sleep(2);
    }

    public function restartPhp()
    {
        GigadbWebsiteContext::containerRestart();
    }

    /**
     * Used to load a new user record into the database.
     *
     * This function requires the pgsql PHP extension to be installed.
     *
     * @param string $user file name (without the .sql extension) to load.
     *
    */
    public function loadUserData($user) {
        $sql = file_get_contents("sql/${user}.sql");
        $dbconn = pg_connect("host={$this->dbConf['host']} dbname={$this->dbConf['db']} user={$this->dbConf['user']} password={$this->dbConf['password']} port=5432") or die('Could not connect: ' . pg_last_error());
        pg_query($dbconn, $sql);
        pg_close($dbconn);
        print_r("Loaded ${user}.sql on gigadb");
    }

    /**
     * Remove from the database users created by the tests
     *
     * This function requires the pgsql PHP extension to be installed.
     *
     *
    */
    public function removeCreatedUsers() {
        print_r("Removing Created Users... ");
        $sql = "delete from gigadb_user where id not in (344,345)";
        $dbconn = pg_connect("host={$this->dbConf['host']} dbname={$this->dbConf['db']} user={$this->dbConf['user']} password={$this->dbConf['password']} port=5432") or die('Could not connect: ' . pg_last_error());
        pg_query($dbconn, $sql);
        pg_close($dbconn);
    }

    /**
     * @Given /^I started the timer$/
     */
    public function iStartedTheTimer()
    {
        $this->time_start = microtime(true);
    }

    /**
     * @Then /^the timer is stopped$/
     */
    public function theTimerIsStopped()
    {
        $time_end = microtime(true);
        $time = $time_end - $this->time_start;

        print_r("Timer stopped after $time seconds\n");
    }

    /** @BeforeSuite */
    public static function backupCurrentDB(BeforeSuiteScope $scope)
    {
        print_r("Loading environment variables... ".PHP_EOL);
        $dotenv = Dotenv\Dotenv::create('/var/www', '.env');
        $dotenv->load();
        $dotsecrets = Dotenv\Dotenv::create('/var/www', '.secrets');
        $dotsecrets->load();
        print_r("Backing up current database... ".PHP_EOL);
        exec("pg_dump ".getenv("GIGADB_DB")." -U ".getenv("GIGADB_USER")." -h ".getenv("GIGADB_HOST")." -F custom  -f /var/www/sql/before-run.pgdmp 2>&1",$output);
    }

    /** @AfterSuite */
    public static function restoreCurrentDB(AfterSuiteScope $scope)
    {
        print_r("Loading environment variables... ".PHP_EOL);
        $dotenv = Dotenv\Dotenv::create('/var/www', '.env');
        $dotenv->load();
        $dotsecrets = Dotenv\Dotenv::create('/var/www', '.secrets');
        $dotsecrets->load();        
        print_r("Restoring current database... ".PHP_EOL);
        GigadbWebsiteContext::call_pg_terminate_backend("gigadb");
        GigadbWebsiteContext::recreateDB("gigadb");
        exec("pg_restore -h ".getenv("GIGADB_HOST")."  -U ".getenv("GIGADB_USER")." -d ".getenv("GIGADB_DB")." --clean --no-owner -v /var/www/sql/before-run.pgdmp 2>&1",$output);
        GigadbWebsiteContext::containerRestart();
    }



}
