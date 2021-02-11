<?php
 /**
 * Functional test for the Dataset View
 *
 * Currently, It just tests the that file sizes display correctly
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class DatasetViewTest extends FunctionalTesting
{
    use BrowserPageSteps;
    use CommonDataProviders;

    /**
     * The Files tab on a dataset view should display formatted file size
     *
     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
     * @uses \CommonDataProviders::adminFileExamplesOfAppropriateMetricDisplayOfFileSize
     *
     * @dataProvider adminFileExamplesOfAppropriateMetricDisplayOfFileSize
     */
    public function testItShouldDisplayFormattedFileSize($size_value, $size_unit) {

        // Go to the file admin tab of the Dataset wizard
        $url = "http://gigadb.dev/dataset/100003/Files_page/1" ;
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, "$size_value $size_unit");

    }

    public function testItShouldLoadPageWithoutGatewayTimeoutError() {
        // Go to parrot dataset page which sometimes returns timeout error
        //$url = "http://gigadb.dev/dataset/100039";
        $url = "http://gigadb.dev/test_gateway_error";
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, null);
        //$this->assertTrue($this->getStatusCode() === 200, "Return status is not 200");
        $this->assertFalse($this->getStatusCode() === 504, "Return status is 504 Gateway Timeout error");
    }

}

?>