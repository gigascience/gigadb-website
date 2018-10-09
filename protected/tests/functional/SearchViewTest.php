<?php
/**
 * Functional test for the Search result
 *
 * Currently, It just tests the that file sizes display correctly
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class SearchViewTest extends FunctionalTesting {

    use BrowserPageSteps;
    use CommonDataProviders;

    /**
     * The search result page should display formatted file size
     *
     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
     * @uses \CommonDataProviders::adminFileExamplesOfAppropriateMetricDisplayOfFileSize
     * @dataProvider adminFileExamplesOfAppropriateMetricDisplayOfFileSize
     */
    public function testItShouldDisplayFormattedFileSize($size_value, $size_unit) {

        // Go to the file admin tab of the Dataset wizard
        $url = "http://gigadb.dev/search/new?keyword=millet" ;
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, "$size_value $size_unit");

    }


}

?>