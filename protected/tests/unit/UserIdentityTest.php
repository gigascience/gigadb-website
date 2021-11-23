<?php

namespace GigaDB\Tests\UnitTests;


/**
 * Unit tests for UserIdentity class
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class UserIdentityTest extends \CDbTestCase
{

	protected $fixtures=array(
        'gigadb_user'=>'User',
        'dataset'=>'Dataset',
    );

	public function setUp()
	{
		parent::setUp();
	}

	public function testAuthenticateStrongHashedValidPasswordAndActiveUser()
	{
		$visiting_user = new \User();
		$visiting_user->username = "author@gigadb.org";
		$visiting_user->password = "correct horse battery staple";
		$userIdentity = new \UserIdentity($visiting_user->username,$visiting_user->password);
		$this->assertTrue($userIdentity->authenticate());
		$this->assertEquals(\UserIdentity::ERROR_NONE, $userIdentity->errorCode);
	}

	public function testAuthenticateStrongHashedValidPasswordAndNonActiveUser()
	{
		$visiting_user = new \User();
		$visiting_user->username = "new@gigadb.org";
		$visiting_user->password = "correct horse battery staple";
		$userIdentity = new \UserIdentity($visiting_user->username,$visiting_user->password);
		$this->assertNotTrue($userIdentity->authenticate());
		$this->assertEquals(\UserIdentity::ERROR_USER_NOT_ACTIVATED, $userIdentity->errorCode);
	}

	public function testAuthenticateNonExistingUser()
	{
		$visiting_user = new \User();
		$visiting_user->username = "fantasy@gigadb.org";
		$visiting_user->password = "correct horse battery staple";
		$userIdentity = new \UserIdentity($visiting_user->username,$visiting_user->password);
		$this->assertNotTrue($userIdentity->authenticate());
		$this->assertEquals(\UserIdentity::ERROR_USERNAME_INVALID, $userIdentity->errorCode);
	}

	public function testAuthenticateStrongHashedInvalidPasswordAndActiveUser()
	{
		$visiting_user = new \User();
		$visiting_user->username = "author@gigadb.org";
		$visiting_user->password = "correct horse battery stable";
		$userIdentity = new \UserIdentity($visiting_user->username,$visiting_user->password);
		$this->assertNotTrue($userIdentity->authenticate());
		$this->assertEquals(\UserIdentity::ERROR_PASSWORD_INVALID, $userIdentity->errorCode);
	}

	public function testAuthenticateLegacyHashedValidPasswordAndActiveUser()
	{
		$visiting_user = new \User();
		$visiting_user->username = "user@gigadb.org";
		$visiting_user->password = "gigadb";
		$userIdentity = new \UserIdentity($visiting_user->username,$visiting_user->password);
		$this->assertTrue($userIdentity->authenticate());
		$this->assertEquals(\UserIdentity::ERROR_NONE, $userIdentity->errorCode);
	}

}
?>