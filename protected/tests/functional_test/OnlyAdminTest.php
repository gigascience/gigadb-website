<?php
/**
 * Functional test for only logged-in user can access /site/admin
 */

class OnlyAdminTest extends FunctionalTesting
{
    use BrowserSignInSteps;

    public function testAdminCanLogin ()
    {
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org", "gigadb", "Admin");
    }

    public function testNonAdminCannotLogin()
    {
        $this->loginToWebAsNonAdminThenAssert("abc@gigadb.org", "gigadb", "Admin");
    }
}

?>