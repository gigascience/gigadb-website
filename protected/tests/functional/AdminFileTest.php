<?php
 /**
 * Functional test for the AdminFileTest form
 *
 * It just tests the that sizes display correctly
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class AdminFileTest extends FunctionalTesting
{

    use BrowserSignInSteps;
    use BrowserPageSteps;
    use CommonDataProviders;

    /**
     *
     * @uses \BrowserSignInSteps::loginToWebSiteWithSessionAndCredentialsThenAssert()
     */
    public function setUp()
    {
        parent::setUp();
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org","gigadb","Admin");
    }

    /**
     * The files form in the dataset wizard should display formatted file size
     *
     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
     *
     * @dataProvider adminFileExamplesOfAppropriateMetricDisplayOfFileSize
     */
    public function testItShouldDisplayFormattedFileSize($size_value, $size_unit) {
        $url = "http://gigadb.dev/adminFile/create1/id/22/" ;
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, "$size_value $size_unit");
    }

    /**
     * The form for updating files should display formatted file size
     *
     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
     *
     * @dataProvider adminFileExamplesOfAppropriateMetricDisplayOfFileSize
     */
    public function testItShouldDisplayFormattedFileSizeOnUpdate($size_value, $size_unit) {
        // Go to the file admin tab of the Dataset wizard
        $url = "http://gigadb.dev/adminFile/update1/?id=22" ;
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, "$size_value $size_unit");
    }

}

?>