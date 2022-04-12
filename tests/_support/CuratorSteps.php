<?php

/**
 * Class CuratorSteps
 * steps specific to user story for curators
 *
 * stubs copied from (after gherkin scenario steps are created):
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept g:snippets acceptance
 */
class CuratorSteps extends \Codeception\Actor
{
    protected $I;


    public function __construct(AcceptanceTester $I)
    {
        $this->I = $I;
    }

    /**
     * @Given there is no user with email :email
     */
    public function thereIsNoUserWithEmail($email)
    {
        $dbConfig = json_decode(file_get_contents(dirname(__FILE__).'/../../protected/config/db.json'), true);
        shell_exec("psql -h {$dbConfig['host']} -U {$dbConfig['user']} -d {$dbConfig['user']} -c \"DELETE FROM gigadb_user WHERE email='$email'\"");
    }

    /**
     * @Given I have signed in as admin
     */
    public function iHaveSignedInAsAdmin()
    {
        $this->I->amOnUrl('http://gigadb.test');
        $this->I->amOnPage('/site/login');
        $this->I->fillField(['name' => 'LoginForm[username]'], 'admin@gigadb.org');
        $this->I->fillField(['name' => 'LoginForm[password]'], 'gigadb');
        $this->I->click('Login');
        $this->I->waitForText("Home",10);
    }


}