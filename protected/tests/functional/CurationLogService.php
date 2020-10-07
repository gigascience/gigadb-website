<?php

/**
 * Functional test for curation logging of new entry
 */

class CurationLogService extends FunctionalTesting
{
    use BrowserSignInSteps;
    use BrowserPageSteps;

    /**
     * To test guest user cannot visit admin file update page.
     *
     * @uses \BrowserSignInSteps::loginToWebSiteWithSessionAndCredentialsThenAssert()
     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
     *
     */
    public function testItShouldNotBeDisplayedToGuestUsers()
    {
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("user@gigadb.org", "gigadb", "John's GigaDB Page");
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull("http://gigadb.dev/adminFile/update1/?id=211/", "Error");
    }

    /**
     * To test admin user can login and access admin file update page.
     *
     * @uses \BrowserSignInSteps::loginToWebSiteWithSessionAndCredentialsThenAssert()
     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
     *
     */
    public function testItShouldBeDisplayedToAdminUser()
    {
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org", "gigadb", "Joe's GigaDB Page");
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull("http://gigadb.dev/adminFile/update1/?id=211", "Update File");
    }

}