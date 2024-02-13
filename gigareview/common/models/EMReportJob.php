<?php

namespace common\models;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use \Yii;
use yii\queue\Queue;

class EMReportJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{

    public string $content;
    public string $effectiveDate ;
    public string $fetchDate ;
    public string $scope;
    public string $reportFileName;


    public function execute($queue)
    {
        if ($this->scope === "manuscripts") {
            $this->executeManuscriptJob($this->content, $this->reportFileName, $this->scope);
        }
    }

    /**
     * Create manuscript instances from the queue content and save them to the manuscript table
     *
     * @param string $content
     * @param string $reportFileName
     * @return void
     */
    public function executeManuscriptJob(string $content, string $reportFileName, string $scope): void
    {
        if ($content !== "No Results") {
            //Step 1: Put queue content to csv
            $tempManuscriptCsvFile = tempnam(sys_get_temp_dir(), "test-manuscripts").".csv";
            file_put_contents($tempManuscriptCsvFile, $content);

            //Step 2: Parse the csv
            $reportData = self::parseReport($tempManuscriptCsvFile);

            //Step 3: Create manuscript instance
            $manuscriptInstances = Manuscript::createInstancesFromEmReport($reportData);

            //Step 4: Save content to  manuscript table and update status in ingest table
            if ($this->storeManuscripts($manuscriptInstances)) {
                Ingest::logStatusAfterSave($reportFileName, $scope);
            } else {
                Ingest::logStatusFailSave($reportFileName, $scope);
            }

        } else {
            Ingest::logNoResultsReportStatus($reportFileName,$scope);
        }
    }

    /**
     * Create an associative array from the csv file
     *
     * @param $emReportPath
     * @return array
     */

    public static function parseReport($emReportPath): array
    {
        $reportData = [];

        $reader = new Csv();
        $spreadsheet = $reader->load($emReportPath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $columnHeader = str_replace(' ', '_', array_map('strtolower', array_shift($sheetData)));
        foreach ($sheetData as $row) {
            $reportData[] = array_combine($columnHeader,$row);
        }
        return $reportData;
    }

    /**
     * Store manuscript object to its table
     *
     * @param array Manuscript[]
     * @return bool
     */
    public function storeManuscripts(array $manuscripts): bool
    {
        $storeStatus = 0;
        foreach ($manuscripts as $manuscript) {
            if ($manuscript->save()) {
                $storeStatus = 1;
            }
        }
        return $storeStatus;
    }
}
