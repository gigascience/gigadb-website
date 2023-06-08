<?php

namespace GigaDB\Tests\UnitTests;

/**
 * unit tests for user class
 */
class UserTest extends \CDbTestCase
{
    protected $fixtures = array(
        'authors' => 'Author',
    );

    function testReturnsLinkedAuthor()
    {
        $user = \User::model()->findByPk(345);
        $this->assertEquals($this->authors(2), $user->getLinkedAuthor(), "Retrieve A3 linked to default user");
    }

    function testReturnsFullName()
    {
        $user = \User::model()->findByPk(345);
        $this->assertEquals("John Smith", $user->getFullName(), "Retrieve full name of a user");
    }

    function testEncryptPassword()
    {
        $user = new \User();
        $password = "correct horse battery staple" ;
        $user->newsletter = false;
        $user->email = "foo@bar";
        $user->terms = true;
        $user->password = $password;

        $user->encryptPassword();
        $this->assertTrue(sodium_crypto_pwhash_str_verify($user->password, $password));
    }

    function testGeneratePassword()
    {
        $user = new \User() ;
        $password = $user->generatePassword();
        $this->assertTrue(strlen($password) >= 8);
        $this->assertRegExp('/^[a-z0-9]+$/', $password);
    }
}
