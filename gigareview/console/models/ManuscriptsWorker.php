<?php

namespace console\models;

use \Yii;
use common\models\Manuscript;

class ManuscriptsWorker
{

    public function init()
    {

    }

    public function execute($queue)
    {

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

    /**
     * @param $manuscriptContents
     * @return void
     */
    public function saveManuscripts($manuscriptContents)
    {
        foreach ($manuscriptContents as $content) {
            $manuscripts = new Manuscript();
            $manuscripts->manuscript_number = $content['manuscript_number'];
            $manuscripts->article_title = $content['article_title'];
            $manuscripts->editorial_status_date = $content['editorial_status_date'];
            $manuscripts->editorial_status = $content['editorial_status'];
            $manuscripts->save();
        }
    }
}

?>