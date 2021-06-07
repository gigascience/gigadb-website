<?php


namespace Helper;


class DatasetFilesGrabber extends \Codeception\Module
{
    const TARGET_URL = "http://gigadb.dev/";

    static public $datasetUrls;

    public function _beforeSuite($settings = array())
    {
        $this->debug("********** BEFORE *********");
        self::$datasetUrls = [];
    }

    public function _afterSuite()
    {
        $this->debug("********** AFTER *********");
        $this->debug(var_dump(self::$datasetUrls));
        self::$datasetUrls = null;
    }
}