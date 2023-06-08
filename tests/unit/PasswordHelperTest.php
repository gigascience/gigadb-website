<?php

namespace GigaDB\Tests\UnitTests;

/**
 * Unit tests for PasswordHelper class
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class PasswordHelperTest extends \CDbTestCase
{
    public function testVerifyWithStrongHashedValidPassword()
    {
        $password = "correct horse battery staple";
        $hash = sodium_crypto_pwhash_str(
            $password,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
        );

        $this->assertEquals(true, \PasswordHelper::verifyPassword($password, $hash));
    }

    public function testVerifyWithStrongHashedInvalidPassword()
    {
        $password = "correct horse battery staple";
        $hash = "dsfdshfasdkhkdshaiufhihw4h87874r8hff";

        $this->assertEquals(false, \PasswordHelper::verifyPassword($password, $hash));
    }

    public function testVerifyWithLegacyHashedValidPassword()
    {
        $password = "correct horse battery staple";
        $hash = md5($password);

        $this->assertEquals(true, \PasswordHelper::verifyLegacyPassword($password, $hash));
    }

    public function testVerifyWithLegacyHashedInvalidPassword()
    {
        $password = "correct horse battery staple";
        $hash = "dsfdshfasdkhkdshaiufhihw4h87874r8hff";

        $this->assertEquals(false, \PasswordHelper::verifyLegacyPassword($password, $hash));
    }

    public function testVerifyWithLegacyHashedValidPasswordUsingVerifyPassword()
    {
        $password = "correct horse battery staple";
        $hash = md5($password);

        $this->assertEquals(true, \PasswordHelper::verifyPassword($password, $hash));
    }
}
