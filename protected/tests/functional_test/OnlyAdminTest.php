<?php
/**
 * Functional test for only logged-in user can access /site/admin
 */

class OnlyAdminTest extends FunctionalTesting
{
    use BrowserSignInSteps;
    use BrowserPageSteps;

    public function testAdminCanLogin ()
    {
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org", "gigadb", "Admin");
    }

    public function testNotAdminCannotLogin ()
    {
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("abc@gigadb.org", "gigadb", "Admin");
    }
}

?>