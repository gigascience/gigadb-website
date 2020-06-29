<?php
/**
 * Functional test for only logged in user can access /site/admin
 */

class OnlyLoggedInTest extends FunctionalTesting
{
    use BrowserSignInSteps;
    use BrowserPageSteps;

    public function testLoggedInCanVisitAdmin ()
    {
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org", "gigadb", "Admin");
    }

    public function testNonLoggedInCannotVisitAdmin()
    {
        $this->nonLoggedInAndCannotVisitAdminThenAssert("abc@gigadb.org", "gigadb", "Admin");
        $url = "http://gigadb.dev/site/admin";
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, "$login");
    }

}

?>