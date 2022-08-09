<?php

namespace common\models;

use \Yii;
use yii\queue\Queue;

class EMReportJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{

    public string $content;
    public string $effectiveDate ;
    public string $fetchDate ;
    public string $scope;


    public function execute($queue)
    {
        if ($this->scope === "manuscripts") {
            $this->executeManuscriptJob($this->content);
        }
            //TODO: Will be implemented in ticket no. #1065
//            $ingest = Ingest::findOne(["report_type" => "1", "parse_status" => null]);
//            $ingest->remote_file_status = Ingest::REMOTE_FILES_STATUS_EXISTS;
//            $ingest->parse_status = Ingest::PARSE_STATUS_YES;
//            $ingest->update();

//        }
            //TODO: Will be implemented in ticket no. #1065
//        elseif ($this->scope === "manuscripts" && $this->content === "No Results") {
//            $ingest = Ingest::findOne(["report_type" => "1", "parse_status" => null]);
//            $ingest->remote_file_status = Ingest::REMOTE_FILES_STATUS_NO_RESULTS;
//            $ingest->parse_status = Ingest::PARSE_STATUS_NO;
//            $ingest->update();
//        }
    }


    public function executeManuscriptJob(string $content): void
    {
        if ($content !== "No Results") {
            //Step 1: Put queue content to csv
            $tempManuscriptCsvFile = tempnam(sys_get_temp_dir(), "test-manuscripts").".csv";
            file_put_contents($tempManuscriptCsvFile, $content);

            //Step 2: Parse the csv
            $reportData = self::parseReport($content);

            //Step 3: Create manuscript instance
//            $manuscriptContents = Manuscript::createInstanceFromEmReport($reportData);

//            //Step 4: Save content to table
//            $this->storeManuscript($manuscriptContents);

        }
    }

    /**
     * @param $emReportPath
     * @return array
     */

    public static function parseReport($emReportPath): array
    {
        $reportData = [];

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $spreadsheet = $reader->load($emReportPath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $columnHeader = str_replace(' ', '_', array_map('strtolower', array_shift($sheetData)));
        foreach ($sheetData as $row) {
            $reportData[] = array_combine($columnHeader,$row);
        }
        return $reportData;
    }

//    public function storeManuscript(array $manuscriptReport)
//    {
//        foreach ($manuscriptReport as $report) {
//            $report->save();
//        }
//    }
}
