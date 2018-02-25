<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;
use Behat\YiiExtension\Context\YiiAwareContextInterface;

use PHPUnit\Framework\Assert;

/**
 * GigadbWebsiteContext Features context.
 */
class GigadbWebsiteContext extends Behat\MinkExtension\Context\MinkContext implements Behat\YiiExtension\Context\YiiAwareContextInterface
{
    private $admin_login;
    private $admin_password;
    private $user_login;
    private $user_password;


	public function __construct(array $parameters)
    {

        $this->admin_login = $_ENV["GIGADB_admin_tester_email"];
        $this->admin_password = $_ENV["GIGADB_admin_tester_password"] ;

        $this->useContext('affiliate_login', new AffiliateLoginContext($parameters));
        $this->useContext('normal_login', new NormalLoginContext($parameters));

        $this->useContext('dataset_view_context', new DatasetViewContext($parameters));
        $this->useContext('admins_attach_author_user', new AuthorUserContext($parameters));
        $this->useContext('datasets_on_profile', new DatasetsOnProfileContext($parameters));
        $this->useContext('claim_dataset', new ClaimDatasetContext($parameters));
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

    /**
     * @AfterStep
    */
    public function debugStep($event)
    {
        if ($event->getResult() == 4 ) {
            try { # take a snapshot of web page
                $this->printCurrentUrl();
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


     /**
     * @Given /^an admin user exists$/
     */
    public function anAdminUserExists()
    {
        if ( null != $this->admin_login ) {
            $nb_ocurrences = $this->getSubcontext('affiliate_login')->countEmailOccurencesInUserList( $this->admin_login );
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
        $nb_ocurrences = $this->getSubcontext('affiliate_login')->countEmailOccurencesInUserList( "admin@gigadb.org");
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
       $nb_ocurrences = $this->getSubcontext('affiliate_login')->countEmailOccurencesInUserList( "user@gigadb.org");
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
     * @Given /^I sign in as an admin$/
     */
    public function iSignInAsAnAdmin()
    {
         $this->visit("/site/login");
         $this->fillField("LoginForm_username", $this->admin_login);
         $this->fillField("LoginForm_password", $this->admin_password);
         $this->pressButton("Login");

         $this->assertResponseContains("Administration");
    }

     /**
     * @Given /^I sign in as a user$/
     */
    public function iSignInAsAUser()
    {
        $this->visit("/site/login");
        $this->fillField("LoginForm_username", $this->user_login);
        $this->fillField("LoginForm_password", $this->user_password);
        $this->pressButton("Login");

        $this->assertResponseNotContains("Administration");
        $this->assertResponseContains("'s GigaDB Page");
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
            exec("vagrant ssh -c \"pg_restore -i -h localhost -p 5432 -U gigadb -d gigadb -v /vagrant/sql/${arg1} \"",$output);
        }
        else if ( preg_match("/\.sql$/", $arg1) ) {
            exec("vagrant ssh -c \"sudo -Hiu postgres /usr/bin/psql gigadb < /vagrant/sql/${arg1}\"",$output);
        }
        else {
            throw new Exception("cannot load database file ${arg1}");
        }
        $this->restartPhp();

    }






    // ---  utility functions

    public function terminateDbBackend($dbname) {
        $sql = "SELECT pg_terminate_backend(procpid) FROM pg_stat_activity WHERE datname='${dbname}' and procpid <> pg_backend_pid()";
        $dbconn = pg_connect("host=localhost dbname=postgres user=postgres port=9171") or die('Could not connect: ' . pg_last_error());
        pg_query($dbconn, $sql);
        pg_close($dbconn);

    }

    public function dropCreateDb($dbname) {
        $sql_to_fence ="ALTER DATABASE $dbname WITH CONNECTION LIMIT 0;";
        $sql_to_drop = "DROP DATABASE ${dbname}";
        $sql_to_create = "CREATE DATABASE ${dbname} OWNER gigadb";
        $dbconn = pg_connect("host=localhost dbname=postgres user=postgres port=9171") or die('Could not connect: ' . pg_last_error());
        pg_query($dbconn, $sql_to_fence);
        pg_query($dbconn, $sql_to_drop);
        pg_query($dbconn, $sql_to_create);
        pg_close($dbconn);

    }
    public function truncateTable($dbname,$tablename) {
        $sql = "TRUNCATE TABLE ${tablename} CASCADE";
        $dbconn = pg_connect("host=localhost dbname=${dbname} user=postgres port=9171") or die('Could not connect: ' . pg_last_error());
        pg_query($dbconn, $sql);
        pg_close($dbconn);
    }

    public function restartPhp()
    {
        exec("vagrant ssh -c \"sudo /etc/init.d/php-fpm restart\"",$output);
    }

    public function loadUserData($user) {
        $sql = file_get_contents("sql/${user}.sql");
        $dbconn = pg_connect("host=localhost dbname=gigadb user=postgres port=9171") or die('Could not connect: ' . pg_last_error());
        pg_query($dbconn, $sql);
        pg_close($dbconn);
    }



}