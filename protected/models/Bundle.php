<?php

class Bundle extends CModel
{
    public $bid;
    public $download_url;

    public function rules()
    {
        return array('bid, download_url', 'required');
    }

    public function attributeNames()
	{
		return array('bid','download_url');
	}

    public function check_download_status() {

        $cache = json_decode(Yii::app()->redis->executeCommand('GET',array($this->bid)), true) ;

        if ('PUBLISHED' === $cache['status'] ) {
            return true ;
        }
        else {
            return false;
        }

        // $s3 = Yii::app()->aws->getS3Instance();
        // try {
        //     $result = $s3->doesObjectExist('gigadb-bundles-test',$this->bid . '.tar.gz');
        // }
        // catch(Exception $e) {
        //     return false;
        // }
        //
        // return $result;

    }

}

?>
