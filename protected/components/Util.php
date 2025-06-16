<?php

class Util
{
    public static function returnJSON($data)
    {
        header('Content-type: application/json');
        echo CJSON::encode($data);
        Yii::app()->end();
    }

    public static function trimText($text)
    {
        try {
            return (ctype_space($text)) ? "" : $text;
        } catch (Exception $e) {
            return "";
        }
    }

    public static function getDois(int $option = PDO::FETCH_ASSOC)
    {
        return Yii::app()->db->createCommand()
                ->select("id, identifier")
                ->from("dataset")
                ->order("id DESC")
                ->queryAll($option);
    }
}
