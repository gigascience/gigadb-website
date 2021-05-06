<?php
 /**
 * Functional test for the RSS feed
 *
 * It tests that the feed appears and in the right order
 *
 * @uses \BrowserPageSteps::getXMLWithSessionAndUrl()
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class RSSFeedTest extends FunctionalTesting
{
    use BrowserPageSteps;

    public function testItShouldShowAnRssFeed()
    {
    	// this is the order we expect the news  to be in
        //    	$expectations = ["2016-05-11", "2016-05-11", "2016-05-09", "2011-11-12"];
        //    	$actual = [];

        $url = "http://gigadb.dev/site/feed/" ;

        // Go to a page and getting xml content
        $feed = $this->getXMLWithSessionAndUrl($url);
        // Don't pick the first two as they have the same publishing date causing them to appear in undetermined order
        $this->assertEquals("10.5072/100004", $feed->channel->item[0]->guid);
        $this->assertEquals("10.5072/100039", $feed->channel->item[3]->guid);


    }
}
