<?php
/**
 * Functional test for only logged in admin user can access /site/admin.
 */

class AdminSiteAccessTest extends FunctionalTesting
{
    use BrowserSignInSteps;
    use BrowserPageSteps;

    public function testItShouldBeDisplayedToUsersWithAdminRole()
    {
        // Logged in as Admin, and can visit to /site/admin page.
        // 2 assertions: 2 passes.
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org","gigadb","Joe's GigaDB Page");
        $url = "http://gigadb.dev/site/admin";
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, "Administration Page");
    }

    public function testItShouldNotBeDisplayedToUsersWithUserRole()
    {
        // Logged in as User, but cannot visit to /site/admin page.
        // 2 assertions: login pass, visit to admin page fail as assertTrue that the content has "Error 403".
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("user@gigadb.org","gigadb","John's GigaDB Page");
        $url = "http://gigadb.dev/site/admin";
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, "Error 403");

    }

    public function testItShouldNotBeDisplayedToOrdinaryUsers()
    {
        // No login was tested.
        // 2 assertions, visit to /site/admin, but would be redirected to /site/login, confirmed by seeing "Login in" as assertTrue is true.
        // Fail to get "Administration Page" from the content of /site/admin, so assertFalse is false.

        $url = "http://gigadb.dev/site/admin";
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, "Login");
        //$current_site = $this->getCurrentUrl();
        //print($current_site);
        $this->session->visit($url);
        $out = $this->session->getPage()->getContent();
        //print($out);
        //there is no "Administration Page' can be found in the $out content.
        $this->assertFalse(strpos($out, "Administration Page"), "Ordinary User cannot visit admin page");

    }

}

?>