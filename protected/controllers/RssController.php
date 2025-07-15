<?php

class RssController extends Controller {

	public $title = "TODO";
	public $rssLink = "http://gigadb.org";
	public $rssDescription = "";
	public $rssAbout = "http://gigadb.org";
    public $numberOfLatestDataset = 10;

    public function actionLatest(){
        $criteria=new CDbCriteria;
        $criteria->limit = $this->numberOfLatestDataset;
        $criteria->condition = "upload_status = 'Published'";
        #$criteria->order = "id DESC";
        $criteria->order = 'publication_date DESC';
        $datasets = Dataset::model()->findAll($criteria);

        $criteria->condition = null;
        $criteria->order = 'publication_date DESC';
        $latest_messages = RssMessage::model()->findAll($criteria);

        $rss_arr = array_merge($datasets , $latest_messages);

        $this->sortRssArray($rss_arr);
        $this->generateFeed($rss_arr);
    }

    private function sortRssArray(&$rss_arr){
        //Using Bubble Sort
        while(True){
            $swapped = False ;
            for($i = 0 ; $i < count($rss_arr) - 1 ; ++$i){
                if($rss_arr[$i]->publication_date < $rss_arr[$i+1]->publication_date){
                    $temp = $rss_arr[$i+1];
                    $rss_arr[$i+1] = $rss_arr[$i];
                    $rss_arr[$i] = $temp;
                    $swapped = True;
                }
            }
            if(!$swapped)
                break;
        }
    }

	private function generateFeed($datasets){
		$feed = new \Laminas\Feed\Writer\Feed();
		$feed->setTitle($this->title);
		$feed->setLink($this->rssLink);
		$feed->setDescription('GigaDB RSS Feed');
        $feed->setLanguage('en-us');
        $feed->setDateModified(time());
		foreach ($datasets as $dataset) {
            $title = $this->isDataset($dataset) ? $dataset->title : $dataset->message;
            $link = $this->isDataset($dataset) ? Yii::app()->request->hostInfo."/dataset/".$dataset->identifier : Yii::app()->request->hostInfo;
            $desc = $this->isDataset($dataset) ? $dataset->description : $dataset->message;
			// create dataset item
			$item = $feed->createEntry();
			$item->setTitle($title);
			$item->setLink($link);
			$item->setDateModified(strtotime($dataset->publication_date) ?: time());
			$item->setDescription($desc);
			$feed->addEntry($item);
		}
		if (count($datasets) === 0) {
			echo "No Item";
            Yii::app()->end();
		}
        header('Content-Type: application/rss+xml; charset=utf-8');
        echo $feed->export('rss');
        exit;

	}

    private function isDataset($class): bool
    {
        return (get_class($class) === 'Dataset') ;
    }

	private function convertDate($date)
    {
        return strtotime($date);
    }
}
