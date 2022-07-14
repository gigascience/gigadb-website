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
        if ($this->scope === "manuscripts") {
            $manuscriptReport = "fetch-manuscript-content-3.csv";
            echo "Get manuscript q job....".PHP_EOL;
            file_put_contents($manuscriptReport, $this->content);
            file_put_contents("test-parse-manuscript.txt", print_r($this->parseManuscriptReport($manuscriptReport),true));
            unlink($manuscriptReport);
        }

//        file_put_contents('fetch-manuscript.txt', print_r($this->content, true));
//        echo "Try to parse manuscript with worker id".$manuscripts_q->workerPid.PHP_EOL;
//        echo "Get content type".getType($this->content);
//        $testParseResult = $this->parseManuscriptReport($this->content);
//        file_put_contents('testParseResult.txt', print_r($testParseResult, true));

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
            'Editorial Status' => 'editorial_status',
            'Editorial Status Date' => 'editorial_status_date',
        ];
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Csv");
        $reader->setDelimiter(",");
        $spreadsheet = $reader->load($manuscriptPath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        foreach ($sheetData as $row) {
            if (!in_array("Manuscript Number", $row)) {
                $manuscriptData[] = array_combine($columnHeader,$row);
            }
        }
        return ($manuscriptData);
    }

}

