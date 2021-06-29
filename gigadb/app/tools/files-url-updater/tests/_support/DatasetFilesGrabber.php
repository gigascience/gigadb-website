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
    const TARGET_URL = "http://gigadb.dev/";


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
     * - we load the production database to create the state needed by the scenario
     */
    public function _beforeSuite()
    {
        $dateStr = "20210608";
        system("./yii dataset-files/download-restore-backup --date $dateStr --nodownload");
    }

    /**
     * in this hook run after the suite
     * - we make sure to restore a pristine production database backup
     */
    public function _afterSuite()
    {
        $dateStr = "20210608";
        system("./yii dataset-files/download-restore-backup --date $dateStr --nodownload");
    }
}