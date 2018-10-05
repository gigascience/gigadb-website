<?php

use aik099\PHPUnit\BrowserTestCase;

class SiteTest extends BrowserTestCase {

	public static $browsers = array(
        array(
            'driver' => 'goutte',
            'browserName' => 'goutte',
            'baseUrl' => 'http://gigadb.dev',
        ),
    );

    public function testItShouldShowLatestNews()
    {
    	// this is the order we expect the news  to be in
    	$expectations = ["2016-05-11", "2016-05-11", "2016-05-09", "2011-11-12"];
    	$actual = [];

    	// This is Mink's Session.
        $session = $this->getSession();
        $url = "http://gigadb.dev/site/" ;

        // Go to a page and getting xml content
        $session->visit($url);

        //Find all the news items on the page
        $newsitemsElement = $session->getPage()->findAll('css','html body div.content section div.container div.row div#rss.col-xs-4 p');

        //extract the publication date from the page
        foreach ($newsitemsElement as $node) {
        	preg_match('(\d\d\d\d-\d\d-\d\d)', $node->getHtml(), $matches);
        	$actual[]=$matches[0];
        }

        //make sure there are in the right order
        $this->assertEquals($expectations, $actual, "order of news should be most recent first") ;

    }

}

?>