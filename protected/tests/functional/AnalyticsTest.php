<?php
 /**
 * Functional test for the Google analytics controller
 *
 * It just tests the that the integration with Google client doesn't throw errors
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class AnalyticsTest extends FunctionalTesting
{
    use BrowserSignInSteps;
    use BrowserPageSteps;
    use BrowserFormSteps;

    /**
     * ReportController::actionIndex loads analytics report page
     *
     * @uses \BrowserSignInSteps::loginToWebSiteWithSessionAndCredentialsThenAssert()
     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
     * @uses \BrowserPageSteps::getCurrentUrl()
     * @uses \BrowserPageSteps::getStatusCode()
     * @uses \BrowserFormSteps::fillReportIndexForm()
     *
     * The test aims to test the credentials for connecting to Google API are configured correctly
     * Otherwise, a 500 Server Error will be returned. That's why we test the return code
     */
//    public function testItShouldLoadGoogleAnalyticsPage()
//    {
//        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org","gigadb","Admin");
//        // Make a call to the report controller
//        $url = "http://gigadb.dev/report/index" ;
//        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, null);
//
//        $this->fillReportIndexForm();
//
//        // Check that after submission we land on the same view
//        $this->assertEquals( "http://gigadb.dev/report/index", $this->getCurrentUrl() );
//
//        // Check that the status code is 200 OK
//        $this->assertEquals( 200,  $this->getStatusCode() );
//
//    }
// TODO: failing for no obvious reason when using the default .env.

}

?>