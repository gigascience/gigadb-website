<?php
class Utils {

    public static function get($array, $key, $default = null) {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    public static function readCsv($file, $delimiter = ',') {
        $data = array();
        $fd = fopen($file, 'r');
        if($fd) {
            while(true) {
                $row = fgetcsv($fd, 0, $delimiter);
                if(!$row) {
                    fclose($fd);
                    break;
                }
                $data[] = $row;
            }
        }
        Yii::log(print_r($data, true), 'debug');
        return $data;
    }

}
?>
