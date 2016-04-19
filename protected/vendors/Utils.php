<?php
class Utils {

    # Convert datetime in UTC to local timezone for application
    public static function datetimeToLocal($datetime, $format='Y-m-d H:i:s') {
        $dt = new DateTime($datetime, new DateTimeZone("UTC"));
        $dt->setTimezone(new DateTimeZone(Yii::app()->params['timezone']));
        return $dt->format($format);
    }

    # Convert date in UTC to local timezone for application
    public static function dateToLocal($date) {
        $dt = new DateTime($date, new DateTimeZone("UTC"));
        $dt->setTimezone(new DateTimeZone(Yii::app()->params['timezone']));
        return $dt->format('Y-m-d');
    }

    public static function addDays($days, $datetime='') {
        if ($datetime) {
            $time = strtotime($datetime) + (60 * 60 * 24 * $days);
        }
        else {
            $time = time() + (60 * 60 * 24 * $days);
        }
        return date('Y-m-s H:i:s', $time);
    }

    public static function get($array, $key, $default = null) {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    public static function supportedLanguages() {
        return self::get(Yii::app()->params, 'languages', array());
    }

    public static function isLanguageSupported($language) {
        return in_array($language, array_keys(self::supportedLanguages()));
    }

    public static function changeLanguage($language) {
        if (Utils::isLanguageSupported($language)) {
            Yii::app()->session['language'] = $language;
            Yii::app()->language = $language;
        }
    }

    public static function languageChangingLinks($base = '/site/changeLanguage') {
        $languages = self::supportedLanguages();
        $items = array();
        foreach($languages as $id => $name)
            $items []= CHtml::link($name, array($base, 'lang' => $id),
                                       ($id !== Yii::app()->language)? array('style' => 'margin: 0px 5px;') :array('style' => "font-weight:bold;margin: 0px 5px;"));
        return implode('|', $items);
    }

    public static function newSphinxClient() {
        $s = new SphinxClient;
        $s->setServer(Yii::app()->params['sphinx_servername'], Yii::app()->params['sphinx_port']);
        $s->setMaxQueryTime(10000);
        $s->setLimits(0, 5000, 5000);
        $s->setMatchMode(SPH_MATCH_EXTENDED);
        return $s;
    }

    public static function convertDate($date){
        date_default_timezone_set('America/Los_Angeles');
        return @strtotime($date);
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

     public static function addToMailing($email, $params = array()) {
        $mc = Yii::app()->mailchimp;
        $result = $mc->listSubscribe($email, $params);
        return $result;
    }

    public static function removeFromMailing($email) {
        $mc = Yii::app()->mailchimp;
        $result = $mc->listUnsubscribe($email);
        return $result;
    }

    public static function parseCsvString($str, $delimiter = ",") {
        $result = array();
        $data = str_getcsv($str, "\n");
        foreach($data as $d) {
            $result[] = str_getcsv($d, $delimiter);
        }
        return $result;

    }

    // run python script to get scholar result
    public static function searchScholar($phrase = '') {
        if(!$phrase)
            return null;
        $script = Yii::getPathOfAlias('scholar');
        $cmd = "python $script --phrase $phrase --csv";
        $str = shell_exec($cmd);

        $result = Utils::parseCsvString($str, "|");
        return $result;
    }

}
?>
