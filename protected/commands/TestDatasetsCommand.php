<?

class TestDatasetsCommand extends CConsoleCommand {
    public function actionClear() {
        $criteria = new CDbCriteria;
        $criteria->condition = 'creation_date < \'' . date("Y-m-d", strtotime("-1 months")) . '\' AND is_test = 1';
        $datasets = Dataset::model()->findAll($criteria);
        foreach ($datasets as $dataset) {
            $dataset->removeWithAllData();
        }
    } 
}
?>

