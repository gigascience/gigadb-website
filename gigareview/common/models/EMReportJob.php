<?php

namespace common\models;

use \Yii;
use yii\queue\Queue;
use \PhpOffice\PhpSpreadsheet\Reader;
use common\models\Ingest;
use console\models\ManuscriptsWorker;


class EMReportJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{

    public string $content;
    public string $effectiveDate ;
    public string $fetchDate ;
    public string $scope;


    public function execute($queue)
    {
        if ($this->scope === "manuscripts" && $this->content !== "No Results") {
            $tempManuscriptCsvFile = tempnam(sys_get_temp_dir(), "test-manuscripts").".csv";

            file_put_contents($tempManuscriptCsvFile, $this->content);

            $manuscriptsWorker = new ManuscriptsWorker();
            $manuscriptsWorker->saveManuscripts($manuscriptsWorker->parseManuscriptReport($tempManuscriptCsvFile));
            unlink($tempManuscriptCsvFile);

            //TODO: Will be implemented in ticket no. #1065
//            $ingest = Ingest::findOne(["report_type" => "1", "parse_status" => null]);
//            $ingest->remote_file_status = Ingest::REMOTE_FILES_STATUS_EXISTS;
//            $ingest->parse_status = Ingest::PARSE_STATUS_YES;
//            $ingest->update();

        }
            //TODO: Will be implemented in ticket no. #1065
//        elseif ($this->scope === "manuscripts" && $this->content === "No Results") {
//            $ingest = Ingest::findOne(["report_type" => "1", "parse_status" => null]);
//            $ingest->remote_file_status = Ingest::REMOTE_FILES_STATUS_NO_RESULTS;
//            $ingest->parse_status = Ingest::PARSE_STATUS_NO;
//            $ingest->update();
//        }
    }

    /**
     * @param string $emReportPath
     * @return array
     */
    public function parseReport(string $emReportPath):array
    {
        $reportData = [];
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $spreadsheet = $reader->load($emReportPath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $columnHeader = str_replace(' ', '_', array_map('strtolower', array_shift($sheetData)));
        foreach ($sheetData as $row) {
            $reportData[] = array_combine($columnHeader,$row);
        }
        return ($reportData);
    }

}
