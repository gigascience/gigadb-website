<?php

use aik099\PHPUnit\BrowserTestCase;

class SearchViewTest extends BrowserTestCase {

	public static $browsers = array(
        array(
            'driver' => 'selenium2',
            'host' => 'phantomjs',
            'port' => 8910,
            'browserName' => 'phantomjs',
            'baseUrl' => 'http://gigadb.dev',
        ),
    );

    /**
     * The search result page should display formatted file size
     *
     * @uses AdminFileTest::adminFileExamplesOfAppropriateMetricDisplayOfFileSize
     * @dataProvider AdminFileTest::adminFileExamplesOfAppropriateMetricDisplayOfFileSize
     */
    public function testItShouldDisplayFormattedFileSize($size_value, $size_unit) {
    	// This is Mink's Session. We assumed we are already logged in as an Admin
        $session = $this->getSession();

        // Go to the file admin tab of the Dataset wizard
        $url = "http://gigadb.dev/search/new?keyword=millet" ;
        $session->visit($url);

        // Validate text presence on a page.
        $this->assertTrue($session->getPage()->hasContent("$size_value $size_unit"));

    }

}

?>