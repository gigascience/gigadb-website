<?

class DeletedDatasetsCommand extends CConsoleCommand {
    public function actionClear() {
        $criteria = new CDbCriteria;
        $criteria->condition = 'modification_date < \'' . date("Y-m-d", strtotime("-7 days")) . '\' AND is_deleted = 1';
        $datasets = Dataset::model()->findAll($criteria);
        foreach ($datasets as $dataset) {
            $dataset->removeWithAllData();
        }
    } 
}
?>

