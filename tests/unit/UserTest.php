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

    public function testReturnsLinkedAuthor()
    {
        $user = \User::model()->findByPk(345);
        $this->assertEquals($this->authors(2), $user->getLinkedAuthor(), "Retrieve A3 linked to default user");
    }

    public function testReturnsFullName()
    {
        $user = \User::model()->findByPk(345);
        $this->assertEquals("John Smith", $user->getFullName(), "Retrieve full name of a user");
    }

    public function testEncryptPassword()
    {
        $user = new \User();
        $password = "correct horse battery staple";
        $user->newsletter = false;
        $user->email = "foo@bar";
        $user->terms = true;
        $user->password = $password;

        $user->encryptPassword();
        $this->assertTrue(sodium_crypto_pwhash_str_verify($user->password, $password));
    }

    public function testGeneratePassword()
    {
        $user = new \User();
        $password = $user->generatePassword();
        $this->assertTrue(strlen($password) >= 8);
        $this->assertRegExp('/^[a-z0-9]+$/', $password);
    }

    public function testPasswordRequired()
    {
        $user = new \User();
        $user->validate();
        $errors = $user->getErrors();

        $this->assertArrayHasKey("password", $errors);
        $this->assertContains('Password cannot be blank', $errors['password'][0]);
    }

    public function testPasswordMustMatchRegex()
    {
        $user = new \User();
        $user->password = "foo";
        $user->validate();
        $errors = $user->getErrors();

        $this->assertArrayHasKey('password', $errors);
        $this->assertContains('Make sure your password contains at least 8 characters with 1 uppercase character, 1 number and 1 special character.', $errors['password'][0]);
    }

    public function testPasswordMustBeExactlyRepeated()
    {
        $user = new \User();
        $user->password = 'Azertyu1@';
        $user->validate();
        $errors = $user->getErrors();

        $this->assertArrayHasKey('password', $errors);
        $this->assertContains('Password must be repeated exactly', $errors['password'][0]);
    }

    public function testEmptyPassword()
    {
        $user = new \User();
        $user->password = '   ';
        $user->password_repeat = '   ';
        $user->validate();
        $errors = $user->getErrors();

        $this->assertArrayHasKey('password', $errors);
        $this->assertContains('Password cannot be blank', $errors['password'][0]);
    }

    public function testValidPassword()
    {
        $user = new \User();
        $user->password = 'Azertyu1@';
        $user->password_repeat = 'Azertyu1@';
        $user->validate();
        $errors = $user->getErrors();

        $this->assertArrayNotHasKey('password', $errors);
    }

    public function testTermsRequired()
    {
        $user = new \User();
        $user->validate();
        $errors = $user->getErrors();

        $this->assertArrayHasKey('terms', $errors);
        $this->assertContains('Terms and Conditions cannot be blank.', $errors['terms'][0]);
        $this->assertContains('Tick here to confirm you have read and understood our Terms of use and Privacy policy.', $errors['terms'][1]);
    }

    public function testTermsMustBeAccepted()
    {
        $user = new \User();
        $user->terms = true;
        $user->validate();
        $errors = $user->getErrors();

        $this->assertArrayNotHasKey('terms', $errors);
    }
}
