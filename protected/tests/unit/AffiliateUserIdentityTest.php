<?php

namespace GigaDB\Tests\UnitTests;


/**
 * Unit tests for UserIdentity class
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class AffilateUserIdentityTest extends \CDbTestCase
{

	protected $fixtures=array(
        'gigadb_user'=>'User',
    );

	public function setUp()
	{
		parent::setUp();
	}


	public function testAuthenticateNonExistingUser()
	{
		$visiting_user = new \User();
		$visiting_user->username = "Facebook";
		$visiting_user->password = "234325325";
		$userIdentity = new \AffiliateUserIdentity($visiting_user->username,$visiting_user->password);
		$this->assertNotTrue($userIdentity->authenticate());
		$this->assertEquals(\AffiliateUserIdentity::ERROR_USERNAME_INVALID, $userIdentity->errorCode);
	}

	public function testAuthenticateExistingAffiliateUser()
	{
		$visiting_user = new \User();
		$visiting_user->username = "social@gigadb.org";
		$visiting_user->facebook_id = "23545234";
		$provider = "Facebook";
		$userIdentity = new \AffiliateUserIdentity($provider,$visiting_user->facebook_id);
		$this->assertTrue($userIdentity->authenticate());
		$this->assertEquals(\AffiliateUserIdentity::ERROR_NONE, $userIdentity->errorCode);
	}


}
?>