<?php
/**
 * Functional test for only logged-in user can access /site/admin
 */

class OnlyAdmin extends FunctionalTesting
{
    use BrowserSignInSteps;

    public function testAdminCanLogin ()
    {
        $email = 'admin@gigadb.org';
        $password = 'gigadb';

        // $this->loginToWebSiteWithSessionAndCredentialsThenAssert();
        $this->assertEquals($email, 'abc@gmail.com');
    }
}