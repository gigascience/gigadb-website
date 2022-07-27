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
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $spreadsheet = $reader->load($manuscriptPath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $columnHeader = str_replace(' ', '_', array_map('strtolower', array_shift($sheetData)));
        foreach ($sheetData as $row) {
                $manuscriptData[] = array_combine($columnHeader,$row);
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