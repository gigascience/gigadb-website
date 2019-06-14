<?php
/**
 * Functional test for the Home page
 *
 * Currently, It tests that the news (so called RSS) feed appears and in the right order
 *
 * @uses \BrowserPageSteps::getXMLWithSessionAndUrl()
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class SiteTest extends FunctionalTesting
{
    use BrowserPageSteps;
    use BrowserFindSteps;

    /**
    * test that the latest news are displayed and in the right order
    *
    * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
    * @uses \BrowserFindSteps::findAllByCSS()
    * @uses \BrowserFindSteps::nodeMatch()
    */
    public function testItShouldShowLatestNews()
    {
    	// this is the order we expect the news  to be in
    	$expectations = ["2016-05-11", "2016-05-11", "2016-05-09", "2011-11-12"];
    	$actual = [];

        $url = "http://gigadb.dev/site/" ;

        // Go to home page
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, null);

        //Find all the news items on the page
        $newsitemsElement = $this->findAllByCSS('html body div.content section div.container div.row div#rss.col-xs-4 p');

        //extract the publication date from the page
        foreach ($newsitemsElement as $node) {
            $matches = $this->nodeMatch($node, '(\d\d\d\d-\d\d-\d\d)' );
        	$actual[]=$matches[0];
        }

        //make sure there are in the right order
        $this->assertEquals($expectations, $actual, "order of news should be most recent first") ;

    }

    public function testItShouldShowCountNumber()
    {

        // this is the content we expect to be in
        $expectations = ["4", "2", "16", "2 G"];
        $actual = [];
        $url = "http://gigadb.dev/site/" ;
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, null);

        //Find all count items on the page
        $newsitemsElement = $this->findAllByCSS('html body div.content section div.container div.color-background div.home-color-background-block h4');

        foreach ($newsitemsElement as $node) {

            $actual[]=$node->getHtml();
        }

        $this->assertEquals($expectations, $actual, "Checked the number in dataset, samples, files, data_volume") ;
    }

}

?>