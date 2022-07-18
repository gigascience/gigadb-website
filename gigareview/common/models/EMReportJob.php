<?php

namespace common\models;

use \Yii;
use yii\queue\Queue;
use \PhpOffice\PhpSpreadsheet\Reader;
use common\models\Ingest;
use common\models\Manuscript;
use console\controllers\FetchReportsController;


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

//            $tempManuscriptCsvFile = "Report-GIGA-em-manuscripts-latest-214-20220713004136.csv";

            $this->saveManuscripts($this->parseManuscriptReport($tempManuscriptCsvFile));
            unlink($tempManuscriptCsvFile);

            $ingest = Ingest::findOne(["report_type" => "1"]);
            $ingest->parse_status = "1";
            $ingest->save();

        } elseif ($this->scope === "manuscripts" && $this->content === "No Results") {
            $ingest = Ingest::findOne(["report_type" => "1"]);
            $ingest->parse_status = "0";
            $ingest->save();
        }
    }

    /**
     * @param string $manuscriptPath
     * @return array
     */
    public function parseManuscriptReport(string $manuscriptPath): array
    {
        $manuscriptData = [];
        $columnHeader = [
            'Manuscript Number' => 'manuscript_number',
            'Article Title' => 'article_title',
            'Editorial Status Date' => 'editorial_status_date',
            'Editorial Status' => 'editorial_status',
        ];
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $spreadsheet = $reader->load($manuscriptPath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        foreach ($sheetData as $row) {
            if (!in_array("Manuscript Number", $row)) {
                $manuscriptData[] = array_combine($columnHeader,$row);
            }
        }
        return ($manuscriptData);
    }

    public function saveManuscripts($manuscriptContents)
    {
        $manuscripts = new Manuscript();
        $manuscripts->manuscript_number = $manuscriptContents[0]['manuscript_number'];
        $manuscripts->article_title = $manuscriptContents[0]['article_title'];
        $manuscripts->editorial_status_date = $manuscriptContents[0]['editorial_status_date'];
        $manuscripts->editorial_status = $manuscriptContents[0]['editorial_status'];
        $manuscripts->save();
    }

}

