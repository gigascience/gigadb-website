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

    function testPasswordRequired()
    {
        $user= new \User();
        $user->validate();
        $errors = $user->getErrors();

        $this->assertArrayHasKey("password", $errors);
        $this->assertContains('Password cannot be blank', $errors['password'][0]);
    }

    function testPasswordMustMatchRegex()
    {
        $user = new \User();
        $user->password = "foo";
        $user->validate();
        $errors = $user->getErrors();

        $this->assertArrayHasKey('password', $errors);
        $this->assertContains('Make sure your password contains at least 8 characters with 1 uppercase character, 1 number and 1 special character.', $errors['password'][0]);
    }

    function testPasswordMustBeExactlyRepeated()
    {
        $user = new \User();
        $user->password = 'Azertyu1@';
        $user->validate();
        $errors = $user->getErrors();

        $this->assertArrayHasKey('password', $errors);
        $this->assertContains('Password must be repeated exactly', $errors['password'][0]);
    }

    function testEmptyPassword()
    {
        $user = new \User();
        $user->password = '   ';
        $user->password_repeat = '   ';
        $user->validate();
        $errors = $user->getErrors();

        $this->assertArrayHasKey('password', $errors);
        $this->assertContains('Password cannot be blank', $errors['password'][0]);
    }

    function testValidPassword()
    {
        $user = new \User();
        $user->password = 'Azertyu1@';
        $user->password_repeat = 'Azertyu1@';
        $user->validate();
        $errors = $user->getErrors();

        $this->assertArrayNotHasKey('password', $errors);
    }
}
