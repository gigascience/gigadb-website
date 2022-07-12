<?php

namespace common\models;

use \Yii;
use yii\queue\Queue;
use \PhpOffice\PhpSpreadsheet\Reader;


class EMReportJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{

    public string $content;
    public string $effectiveDate ;
    public string $fetchDate ;
    public string $scope;

    public function execute($queue)
    {
        // TODO: Implement execute() method.
        echo "Hello World".PHP_EOL;
    }

    /**
     * @param string $manuscriptPath
     * @return array
     */
    public function parseManuscriptReport(string $manuscriptPath): array
    {
        $manuscriptData = [];
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Csv");
        $reader->setDelimiter(",");
        $spreadsheet = $reader->load($manuscriptPath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        foreach ($sheetData as $row) {
            $manuscriptData[] = array_push($row);
        }

        print_r($manuscriptData, true);
        return ($manuscriptData);
    }

}

