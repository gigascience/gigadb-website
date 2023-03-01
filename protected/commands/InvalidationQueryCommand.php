<?php

class InvalidationQueryCommand extends CConsoleCommand
{
    public function getHelp()
    {
        $helpText = "Commands to test the invalidation query" . PHP_EOL;
        $helpText .= "Usage: ./protected/yiic invalidationquery getlatestcreatebyleftjoindatasetlogandcurationlog" . PHP_EOL;
        $helpText .= "Usage: ./protected/yicc invalidationquery getlatestcreateusingqueryfrommainconfigfile" . PHP_EOL;
        return $helpText;
    }

    public function actionGetLatestCreateByLeftJoinDatasetlogAndCurationlog()
    {
        $sql = "select max(created_at) as dataset_log_latest, max(creation_date) as curation_log_latest from dataset_log d left join curation_log c on c.dataset_id = d.dataset_id  where d.dataset_id = 8 or c.dataset_id = 8;";
        $rows = Yii::app()->db->createCommand($sql)->queryRow();
        foreach ($rows as $row) {
            print_r($row);
        }
    }

    public function actionGetLatestCreateUsingQueryFromMainConfigFile()
    {
        $dataset_id = 8;
        $invalidationQuery = preg_replace("/@id/", $dataset_id, Yii::app()->params['invalidationQuery']);
        $rows = Yii::app()->db->createCommand($invalidationQuery)->queryRow();
        foreach ($rows as $row) {
            print_r($row);
        }
    }
}
