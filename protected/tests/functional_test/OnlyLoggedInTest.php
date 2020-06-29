<?php
/**
 * Functional test for only logged in user can access /site/admin
 */

class OnlyLoggedInTest extends FunctionalTesting
{
    use BrowserSignInSteps;

    public function testLoggedInCanVisitAdmin ()
    {
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org", "gigadb", "Admin");
    }

    public function testNonLoggedInCannotVisitAdmin()
    {
        $this->loginToWebAsNonLoggedInThenAssert("abc@gigadb.org", "gigadb", "Admin");
    }
}

?>