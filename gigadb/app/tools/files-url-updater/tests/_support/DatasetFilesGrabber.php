<?php


namespace Helper;


use \app\models\DatasetFiles;
use \Codeception\TestInterface;

class DatasetFilesGrabber extends \Codeception\Module
{
    const TARGET_URL = "http://gigadb.dev/";

    static public $datasetUrls;

    public function _beforeSuite($settings = array())
    {
        self::$datasetUrls = [];
    }

    public function _afterSuite()
    {
        $this->debug(var_dump(self::$datasetUrls));
        $this->debug("********** AFTER *********");
        self::$datasetUrls = null;
    }

    public function _before(TestInterface $test)
    {
        parent::_before($test);
        $dateStr = "20210608";
        system("./yii dataset-files/download-restore-backup --date $dateStr --nodownload");
    }
}