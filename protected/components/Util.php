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

    public static function getDois(bool $fetchAsso = false)
    {
        $rows = Yii::app()->db->createCommand()
                ->select("id, identifier")
                ->from("dataset")
                ->order("id DESC")
                ->queryAll();

        if ($fetchAsso) {
            return CHtml::listData($rows, 'id', 'identifier');
        }

        return $rows;
    }
}
