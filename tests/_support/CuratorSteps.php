<?php

class CuratorSteps extends \Codeception\Actor
{

    /**
     * @Given there is no user with email :email
     */
    public function thereIsNoUserWithEmail($email)
    {
        $dbConfig = json_decode(file_get_contents(dirname(__FILE__).'/../../protected/config/db.json'), true);
        shell_exec("psql -h {$dbConfig['host']} -U {$dbConfig['user']} -d {$dbConfig['user']} -c \"DELETE FROM gigadb_user WHERE email='$email'\"");
    }
}