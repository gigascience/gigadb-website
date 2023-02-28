<?php

/**
 * Service to manage functionalities for generating News ticker and RSS feeds
 *
 * It is used by the SiteController for home page news feed and for the RSS feed
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class NewsAndFeedsService extends CApplicationComponent
{
    /** @var RSSFeedDAO */
    private $rss_feed_dao;

    public function init()
    {
        parent::init();
        if (!isset($this->rss_feed_dao)) {
            $this->rss_feed_dao = new RSSFeedDAO() ;
        }
    }
    /**
     * Sorted merged list of 10 latest RSS messages and 10 latest datasets
     *
     * @return array
     */
    public function getFeedsData()
    {
        return $this->rss_feed_dao->getData();
    }

    /**
     * Todays's news infos from News table
     *
     * @uses News.php
     * @return array
     */
    public function getTodaysNews()
    {
        return News::model()->findAll("start_date<=current_date AND end_date>=current_date");
    }

    /**
     * return Rss feed for the website
     *
     * @uses Suin\RSSWriter\Feed
     * @uses Suin\RSSWriter\Channel
     * @return Suin\RSSWriter\Feed
     */
    public function getRss()
    {
        $feed = new Suin\RSSWriter\Feed();

        $channel = new Suin\RSSWriter\Channel();
        $channel
            ->title('GigaDB')
            ->description('Latest datasets')
            ->url('http://gigadb.org')
            ->feedUrl('http://gigadb.org/site/feed/')
            ->language('en-US')
            ->copyright('Copyright 2018, GigaScience Journal')
            ->pubDate(strtotime(date(DATE_RFC2822)))
            ->ttl(60)
            ->appendTo($feed);

        // Get data
        $feed_data = $this->getFeedsData();

        // Create feed
        foreach ($feed_data as $data_item) {
            $item = new Suin\RSSWriter\Item();
            if (get_class($data_item) == 'Dataset') {
                $item
                    ->title($data_item->title)
                    ->description($data_item->description)
                    ->contentEncoded($data_item->description)
                    ->url($data_item->shortUrl)
                    ->author($data_item->getAuthorNames())
                    ->pubDate(strtotime($data_item->publication_date))
                    ->guid(Yii::app()->params['mds_prefix'] . "/" . $data_item->identifier, true)
                    ->preferCdata(true) // By this, title and description become CDATA wrapped HTML.
                    ->appendTo($channel);
            } else { //$data_item is RssMessage
                $item
                    ->title("GigaDB News bulletin")
                    ->description($data_item->message)
                    ->contentEncoded($data_item->message)
                    ->author("GigaDB Team")
                    ->pubDate(strtotime($data_item->publication_date))
                    ->preferCdata(true) // By this, title and description become CDATA wrapped HTML.
                    ->appendTo($channel);
            }
        }

        return $feed;
    }
}
