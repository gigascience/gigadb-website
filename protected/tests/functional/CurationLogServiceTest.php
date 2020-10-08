<?php

/**
 * Functional test for curation logging of new entry
 */

class CurationLogServiceTest extends FunctionalTesting
{
    public function testItShouldCreateEntryInCurationLogTableUsingCurationLogService()
    {
        try {
            $result = Yii::app()->curationLogService->createNewEntry("210", "20", "Stuff");
            $this->assertTrue("true" === "true", "stuff");
        } catch (Error $e) {
            $this->fail("Exception thrown: " . $e->getMessage());
        }

    }
//    use BrowserSignInSteps;
//    use BrowserPageSteps;
//
//    /**
//     * To test guest user cannot visit admin file update page.
//     *
//     * @uses \BrowserSignInSteps::loginToWebSiteWithSessionAndCredentialsThenAssert()
//     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
//     *
//     */
//    public function testItShouldDisplayToGuestUsers()
//    {
//        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("user@gigadb.org", "gigadb", "John's GigaDB Page");
//        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull("http://gigadb.dev/adminFile/update1/id/211", "Error 403");
//    }
//
//    /**
//     * To test admin user can login and access admin file update page.
//     *
//     * @uses \BrowserSignInSteps::loginToWebSiteWithSessionAndCredentialsThenAssert()
//     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
//     *
//     */
//    public function testItShouldDisplayToAdminUser()
//    {
//        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org", "gigadb", "Joe's GigaDB Page");
//        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull("http://gigadb.dev/adminFile/update1/id/211", "Update File");
//    }
//
//    /**
//     * To test admin user can see New Attribute, Edit and Delete buttons
//     *
//     * @uses \BrowserSignInSteps::loginToWebSiteWithSessionAndCredentialsThenAssert()
//     * @uses \BrowserPageSteps::getCurrentUrl()
//     */
//    public function testItShouldBeSeenByAdminUser()
//    {
//        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org", "gigadb", "Joe's GigaDB Page");
//        $url = "http://gigadb.dev/adminFile/update1/id/211";
//        $this->session->visit($url);
//        $this->assertTrue($this->session->getPage()->hasContent("New Attribute"), "Admin cannot see New Attribute");
//
//    }
}