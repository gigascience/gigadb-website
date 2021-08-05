<?php


namespace Helper;


use \app\models\DatasetFiles;
use \Codeception\TestInterface;

/**
 * Lifecycle utility methods for acceptance tests
 *
 * @package Helper
 */
class DatasetFilesGrabber extends \Codeception\Module
{
    /**
     * @const used as default target url in scenario steps
     */
    const TARGET_URL = "http://gigadb.test/";


    /**
     * in this hook run after each scenario,
     * - we make sure we remove the file tath was downloaded by one of the scenario
     *
     * @param TestInterface $test
     */
    public function _after(TestInterface $test)
    {
        $downloaded = "/app/readme_100633.txt";
        if(file_exists($downloaded))
            unlink($downloaded);
        parent::_after($test);
    }

    /**
     * in this hook run before the suite
     * - we verify that we running against the local database, if not we bail out
     * - we load the production database to create the state needed by the scenario
     */
    public function _beforeSuite($settings = [] )
    {

        $currentConfig = require("/app/config/params.php");
        if("pg9_3" !== $currentConfig["db"]["host"]) {
            exit("Wrong database! Check your config/params.php. Acceptance tests should be run against the local database only");
        }

        $dateStamp = date('Ymd', strtotime(date('Ymd')." - 1 day"));
        system("echo yes | ./yii dataset-files/download-restore-backup --default --nodownload");
        system("cp sql/gigadbv3_default.backup sql/gigadbv3_$dateStamp.backup");
    }

    /**
     * in this hook run after the suite
     * - we make sure to restore a pristine production database backup
     */
    public function _afterSuite()
    {
        system("echo yes | ./yii dataset-files/download-restore-backup --latest --nodownload");
    }
}