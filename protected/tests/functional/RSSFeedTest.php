<?php

use aik099\PHPUnit\BrowserTestCase;

class RSSFeedTest extends BrowserTestCase
{
	public static $browsers = array(
        array(
            'driver' => 'goutte',
            'browserName' => 'goutte',
            'baseUrl' => 'http://gigadb.dev',
        ),
    );

    public function testItShouldShowAnRssFeed()
    {
    	// this is the order we expect the news  to be in
    	$expectations = ["2016-05-11", "2016-05-11", "2016-05-09", "2011-11-12"];
    	$actual = [];

    	// This is Mink's Session.
        $session = $this->getSession();
        $url = "http://gigadb.dev/site/feed/" ;

        // Go to a page and getting xml content
        $session->visit($url);

        //Find all the news items on the page
        $feed_raw = $session->getPage()->getContent();

        $feed = new SimpleXMLElement($feed_raw);

        $this->assertEquals("10.5072/100004", $feed->channel->item[0]->guid);
        $this->assertEquals("10.5072/100003", $feed->channel->item[3]->guid);


    }
}