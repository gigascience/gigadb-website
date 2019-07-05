<?php

class Util {
    public static function returnJSON($data) {
        header('Content-type: application/json');
        echo CJSON::encode($data);
        ob_start();
        Yii::app()->end(0, false);
        ob_end_clean();
        exit(0);
    }

    public static function trimText($text) {
    	try {
		return (ctype_space($text))? "" : $text;  
	} catch (Exception $e) {
		return "";
	}
    }

    public static function getDois() {
        $dois = Yii::app()->db->createCommand()
                ->select("id, identifier")
                ->from("dataset")
                ->where('upload_status=:upload_status', array(':upload_status' => 'Published'))
                ->order("id DESC")
                ->queryAll();

        return $dois;
    }
}

?>