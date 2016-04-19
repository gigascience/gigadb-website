<?

class AutoSendNotificationCommand extends CConsoleCommand {

    private $lastdataset="";
    private $numberOfNewDataset=5;
    private $timeWaitPeriod=3600;  // 1 Hour

    private $emailSubject="New Datasets from GigaDB";
    public function getHelp() {
        return 'Usage: Auto Search Sphinx for saved search';
    }

    public function actionNotification(){
        $this->lastdataset=Yii::app()->basePath."/scripts/data/lastdataset.txt";

        $filecontent= $this->readFile();
        $temp = json_decode($filecontent,true);
        $lastdataset=isset($temp['lastdataset'])?$temp['lastdataset']:0;
        $lastTimeRun=isset($temp['lastTimeRun'])?$temp['lastTimeRun']:0;


        $now=microtime(true);
        $lastestDataset = Dataset::model()->find("id=(SELECT MAX(id) FROM dataset)");

        

        if(($lastestDataset->id-$lastdataset > $this->numberOfNewDataset) || ( ($now-$lastTimeRun)>$this->timeWaitPeriod  ) && $lastestDataset->id-$lastdataset >0){
            $newDatasets = Dataset::model()->findAll("id>?",array($lastdataset));
            $content= $this->generateEmail($newDatasets);
            $this->sendBroadcastEmail($this->emailSubject,$content);

            // Store Information for next time
            $temp['lastdataset'] = $lastestDataset->id;
            $temp['lastTimeRun'] = floor($now);
            $this->writeFile(json_encode($temp));
        }else {
            echo "Don't have enough dataset";
        }
    }

    private function sendBroadcastEmail($subject,$content){
        Yii::import('application.controllers.*');
        $news = new MailListController('news');
        $news->actionbroadcastEmail($subject,$content);
    }


    private function generateEmail($listDataset){
        return CController::renderInternal(Yii::app()->basePath.'/views/search/emailNewDatasets.php',array('listDataset'=>$listDataset),true);
    }


    private function readFile(){
        $result="";

        $file = fopen( $this->lastdataset, "r" )or die ( "Cannot open the file" );
        if ( filesize($this->lastdataset) != 0 ){
            $filesize=filesize($this->lastdataset);
            $result=fread($file,$filesize);
        }
        fclose($file);
        return $result;

    }
    private function writeFile($content){
        $fh = fopen($this->lastdataset, 'w');
        fwrite($fh,  $content);
        fclose($fh);
    }
}
?>

