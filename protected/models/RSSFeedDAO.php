<?php
/**
 * Class to fetch from DB latest data for Dataset and RSS messages
 *
 * It is used by the SiteController for home page news feed and for the RSS feed
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class RSSFeedDAO
{
	public function getData() {
		$criteria=new CDbCriteria;
		$criteria->limit = 10;
		$criteria->condition = "upload_status = 'Published'";
		#$criteria->order = "id DESC";
		$criteria->order = 'publication_date DESC';
		$latest_datasets = Dataset::model()->findAll($criteria);

		$criteria->condition = null;
		$criteria->order = 'publication_date DESC';
		$latest_messages = RssMessage::model()->findAll($criteria);

		$rss_arr = array_merge($latest_datasets , $latest_messages);

        usort($rss_arr, function ($a,$b) {
              return strtotime($a->publication_date ? $a->publication_date : 0) - strtotime($b->publication_date ? $b->publication_date : 0);
        });

        return $rss_arr;
	}
}
