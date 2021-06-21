<?php


namespace Helper;


use \app\models\DatasetFiles;
use \Codeception\TestInterface;

class DatasetFilesGrabber extends \Codeception\Module
{
    const TARGET_URL = "http://gigadb.dev/";

    public function _before(TestInterface $test)
    {
        parent::_before($test);
        $dateStr = "20210608";
        system("./yii dataset-files/download-restore-backup --date $dateStr --nodownload");
    }

    public function _after(TestInterface $test)
    {
        $downloaded = "/app/readme_100633.txt";
        if(file_exists($downloaded))
            unlink($downloaded);
        parent::_after($test);
    }
}