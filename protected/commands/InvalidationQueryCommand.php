<?php

class InvalidationQueryCommand extends CConsoleCommand
{
    public function getHelp()
    {
        $helpText = "Commands to test the invalidation query" . PHP_EOL;
        $helpText .= "Usage: ./protected/yiic invalidationquery getmaxcreatedatFromdatasetlog" . PHP_EOL;
        $helpText .= "Usage: ./protected/yiic invalidationquery getmaxcreateddatefromcurationlog" . PHP_EOL;
        $helpText .= "Usage: ./protected/yiic invalidationquery getmaxcreatefromdatasetlogandcurationlog" . PHP_EOL;
        $helpText .= "Usage: ./protected/yiic invalidationquery getmaxcreatebyleftjoindatasetlogandcurationlog" . PHP_EOL;
        return $helpText;
    }

    public function actionGetMaxCreatedAtFromDatasetlog()
    {
        $sql = "select max(created_at) from dataset_log where dataset_id=8";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        print_r($rows);
    }

    public function actionGetMaxCreatedDateFromCurationlog()
    {
        $sql = "select max(creation_date) from curation_log where dataset_id=8";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        print_r($rows);
    }

    public function actionGetMaxCreateFromDatasetlogAndCurationlog()
    {
        $sql = "with the_dataset_log as (select max(created_at) as latest from dataset_log where dataset_id=8), the_curation_log as (select max(creation_date) as latest from curation_log where dataset_id=8)select d.latest,c.latest from the_dataset_log d, the_curation_log c;";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        print_r($rows);
    }

    public function actionGetMaxCreateByLeftJoinDatasetlogAndCurationlog()
    {
        $sql = "select max(d.created_at), max(c.creation_date) from dataset_log d left join curation_log c on d.dataset_id = c.dataset_id;";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        print_r($rows);
    }
}