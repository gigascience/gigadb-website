<?php

declare(strict_types=1);

class RSSFeedDAOTest extends CDbTestCase
{
    protected $fixtures = array(
        'rss_messages' => 'RssMessage',
    );

    function testItShouldGetDataForDatasetAndRssMessage()
    {
        $rss_feed_dao = new RSSFeedDAO();
        $feed = $rss_feed_dao->getData();
        // test we have the expected number of items
        $this->assertEquals(9, count($feed));

        $dates = array_map(function($item) {
            return $item->publication_date;
        }, $feed);

        $expected = $dates;
        rsort($expected);
        $this->assertEquals($expected, $dates, 'Not in descending order');
    }
}
