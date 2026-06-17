<?php

declare(strict_types=1);

require_once __DIR__ . '/LoadingFixtureTrait.php';

use Codeception\Test\Unit;
/**
 * Unit tests for UserIdentity class
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class UserIdentityTest extends Unit
{
    use LoadingFixtureTrait;

    public function _before()
    {
        $db = $this->getModule('Db')->_getDbh();
        $db->exec('TRUNCATE TABLE gigadb_user CASCADE');
        $db->exec('TRUNCATE TABLE dataset CASCADE');

        $this->loadFixture('gigadb_user', \User::class);
        $this->loadFixture('dataset', \Dataset::class);
    }

    public function testAuthenticateStrongHashedValidPasswordAndActiveUser()
    {
        $visiting_user = new \User();
        $visiting_user->username = "author@gigadb.org";
        $visiting_user->password = "correct horse battery staple";
        $userIdentity = new \UserIdentity($visiting_user->username, $visiting_user->password);
        $this->assertTrue($userIdentity->authenticate());
        $this->assertEquals(\UserIdentity::ERROR_NONE, $userIdentity->errorCode);
    }

    public function testAuthenticateStrongHashedValidPasswordAndNonActiveUser()
    {
        $visiting_user = new \User();
        $visiting_user->username = "new@gigadb.org";
        $visiting_user->password = "correct horse battery staple";
        $userIdentity = new \UserIdentity($visiting_user->username, $visiting_user->password);
        $this->assertNotTrue($userIdentity->authenticate());
        $this->assertEquals(\UserIdentity::ERROR_USER_NOT_ACTIVATED, $userIdentity->errorCode);
    }

    public function testAuthenticateNonExistingUser()
    {
        $visiting_user = new \User();
        $visiting_user->username = "fantasy@gigadb.org";
        $visiting_user->password = "correct horse battery staple";
        $userIdentity = new \UserIdentity($visiting_user->username, $visiting_user->password);
        $this->assertNotTrue($userIdentity->authenticate());
        $this->assertEquals(\UserIdentity::ERROR_USERNAME_INVALID, $userIdentity->errorCode);
    }

    public function testAuthenticateStrongHashedInvalidPasswordAndActiveUser()
    {
        $visiting_user = new \User();
        $visiting_user->username = "author@gigadb.org";
        $visiting_user->password = "correct horse battery stable";
        $userIdentity = new \UserIdentity($visiting_user->username, $visiting_user->password);
        $this->assertNotTrue($userIdentity->authenticate());
        $this->assertEquals(\UserIdentity::ERROR_PASSWORD_INVALID, $userIdentity->errorCode);
    }

    public function testAuthenticateLegacyHashedValidPasswordAndActiveUser()
    {
        $visiting_user = new \User();
        $visiting_user->username = "user@gigadb.org";
        $visiting_user->password = "gigadb";
        $userIdentity = new \UserIdentity($visiting_user->username, $visiting_user->password);
        $this->assertTrue($userIdentity->authenticate());
        $this->assertEquals(\UserIdentity::ERROR_NONE, $userIdentity->errorCode);
    }
}
