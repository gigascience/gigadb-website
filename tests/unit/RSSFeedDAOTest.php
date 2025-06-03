<?php

declare(strict_types=1);

require_once __DIR__ . '/LoadingFixtureTrait.php';

use Codeception\Test\Unit;

class RSSFeedDAOTest extends Unit
{
    use LoadingFixtureTrait;

    public function _before()
    {
        $db = $this->getModule('Db')->_getDbh();
        $db->exec('TRUNCATE TABLE rss_message CASCADE');

        $this->loadFixture('rss_message', \RssMessage::class);
    }

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
