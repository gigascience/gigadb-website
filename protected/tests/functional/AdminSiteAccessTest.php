<?php
/**
 * Functional test for only logged in as admin user can access Administration Page.
 *
 */

class AdminSiteAccessTest extends FunctionalTesting
{
    use BrowserSignInSteps;
    use BrowserPageSteps;

    /**
     * To test admin user can login and access Administration Page.
     *
     * @uses \BrowserSignInSteps::loginToWebSiteWithSessionAndCredentialsThenAssert()
     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
     *
     */
    public function testItShouldBeDisplayedToUsersWithAdminRole()
    {
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org", "gigadb", "Joe's GigaDB Page");
        $url = "http://gigadb.dev/site/admin";
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, "Administration Page");
    }

    /**
     * To test registered user can login, but cannot access Administration Page.
     *
     * @uses \BrowserSignInSteps::loginToWebSiteWithSessionAndCredentialsThenAssert()
     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
     *
     */
    public function testItShouldNotBeDisplayedToUsersWithUserRole()
    {
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("user@gigadb.org", "gigadb", "John's GigaDB Page");
        $url = "http://gigadb.dev/site/admin";
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, "Error 403");
    }

    /**
     * To test ordinary user fails to access Administration Page and would be re-directed to Login page.
     *
     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
     * @uses \BrowserPageSteps::getCurrentUrl()
     *
     */
    public function testItShouldNotBeDisplayedToGuest()
    {
        $url = "http://gigadb.dev/site/admin";
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, "Login");

        // To confirm User has been re-directed to /site/login
        $current_site = $this->getCurrentUrl();
        $this->assertTrue($current_site == "http://gigadb.dev/site/login", "The current site has not been re-directed.");

        // To confirm Guest visits to /admin/page will be redirect to /site/login
        $this->session->visit($url);
        $out = $this->session->getPage()->hasContent();
        $this->assertTrue($out=="True", "Out has no content");
    }
}
?>

