<?php

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

        // test that we have the right ids and in the right order
        $this->assertEquals([20,10,1,2, 7,4,3,6,5], array_map(function ($item) {
            return $item->id;
        }, $feed));
    }
}
