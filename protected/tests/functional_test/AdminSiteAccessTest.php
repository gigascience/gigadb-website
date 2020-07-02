<?php
/**
 * Functional test for only logged in admin user can access /site/admin.
 */

class AdminSiteAccessTest extends FunctionalTesting
{
    use BrowserSignInSteps;
    use BrowserPageSteps;

    public function testItShouldBeDisplayedToUsersWithAdminRole ()
    {
        // Logged in as Admin, and can visit to /site/admin page.
        // 2 assertions, 2 passes.
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org","gigadb","Admin");
        $url = "http://gigadb.dev/site/admin";
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, "Administration Page");
    }

    public function testItShouldNotBeDisplayedToUsersWithUserRole ()
    {
        // Logged in as User, but cannot visit to /site/admin page.
        // 2 assertions, login pass, but visit to /site/admin fail.
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("user@gigadb.org","vagrant","User");
        $url = "http://gigadb.dev/site/admin";
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, "Administration Page");

    }

    public function testItShouldNotBeDisplayedToOrdinaryUsers ()
    {
        // No login was tested. 1 assertion,
        // Visit to /site/admin, but would be redirected to /site/login, confirmed by seeing "Login in" as assert true,
        // and the current url was /site/login.
        $url = "http://gigadb.dev/site/admin";
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, "Login");
        $out = $this->getCurrentUrl();
        print($out);
    }
}

?>