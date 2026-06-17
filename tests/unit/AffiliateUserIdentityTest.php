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
class AffiliateUserIdentityTest extends Unit
{
    use LoadingFixtureTrait;

    public function _before()
    {
        $db = $this->getModule('Db')->_getDbh();
        $db->exec('TRUNCATE TABLE gigadb_user CASCADE');

        $this->loadFixture('gigadb_user', \User::class);
    }

    public function testAuthenticateNonExistingUser()
    {
        $visiting_user = new \User();
        $visiting_user->username = 'Facebook';
        $visiting_user->password = '234325325';

        $userIdentity = new \AffiliateUserIdentity(
            $visiting_user->username,
            $visiting_user->password
        );

        $this->assertFalse(
            $userIdentity->authenticate(),
            "shouldn't authenticate an user if the user doesn't exists"
        );

        $this->assertEquals(
            \AffiliateUserIdentity::ERROR_USERNAME_INVALID,
            $userIdentity->errorCode,
        );
    }

    public function testAuthenticateExistingAffiliateUser()
    {
        $visiting_user = \User::model()->findByAttributes([
          'email'       => 'social@gigadb.org',
          'facebook_id' => '23545234',
        ]);
        $this->assertNotNull($visiting_user);

        $provider = 'Facebook';
        $userIdentity = new \AffiliateUserIdentity(
            $provider,
            $visiting_user->facebook_id
        );

        $this->assertTrue($userIdentity->authenticate());

        $this->assertEquals(
            \AffiliateUserIdentity::ERROR_NONE,
            $userIdentity->errorCode
        );
    }
}
