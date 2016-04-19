<?

class AutoSearchCommand extends CConsoleCommand {

    public function getHelp() {
        return 'Usage: Auto Search Sphinx for saved search';
    }

    public function actionSearch(){
        $searches = SearchRecord::model()->findAll();
        foreach ($searches as $key => $search) {
            $criteria=json_decode($search->query,true);
            $oldResult=json_decode($search->result,true);
            $newResult = Dataset::sphinxSearch($criteria);
            $this->compareResult($oldResult,$newResult,$search);
        }
        echo count($searches)." saved search are done \n";
    }


    private function compareResult($oldIds , $newIds, $model){
        if(!is_array($oldIds)){
            $oldIds=array();
        }

        $diff = array_diff($newIds, $oldIds);

        if(!empty($diff)){
            $model->result=json_encode($newIds);
            $model->save();
            $this->sendNotificationEmail($diff,$model->user,$model->convertCriteria(true));
        }
    }

    private function sendNotificationEmail($list,$user,$criteria){
        $app_email_name = Yii::app()->params['app_email_name'];
        $app_email = Yii::app()->params['app_email'];
        $email_prefix = Yii::app()->params['email_prefix'];
        $headers = "From: $app_email_name <$app_email>\r\n"; //optional header fields
        $headers .= "Content-type: text/html\r\n";
        ini_set('sendmail_from', $app_email);

        $listurl="";
        foreach ($list as $key => $value) {
            $model = Dataset::model()->findByPk($value);
            $url = Yii::app()->params['home_url']."/dataset/".$model->identifier;
            $author_list = '';
            if (count($model->authors) > 0) {
                  $i = 0;
                foreach( $model->authors as $key => $author){
                    if (++$i < count($model->authors)) $author_list .= $author->name.';'; else $author_list .= $author->name.' ';
                }
            }
            $listurl .= <<<EO_LU
<span style='font-weight:bold;'>{$model->title}</span><br>
{$author_list}<br>
<a href='{$url}'>{$url}</a><br><br>
EO_LU;
        }

        $recipient = Yii::app()->params['notify_email'];
        $subject = "GigaDB has new content which matches your interest";

        $body = CController::renderInternal(Yii::app()->basePath.'/views/search/emailMatchedSearches.php',array('listurl'=>$listurl,'criteria'=>$criteria),true);

        mail($user->email, $subject, $body, $headers);
        Yii::log(__FUNCTION__."> Sent email to $recipient, $subject");
    }
}
?>

