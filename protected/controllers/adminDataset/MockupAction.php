<?php
/**
 * This action for AdminDatasetController will generate a mockup access
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class MockupAction extends CAction
{
    public function run($id)
    {
        $model= Dataset::model()->findByPk($id);
        $datasetPageSettings = new DatasetPageSettings($model);
        if ( "invalid" === $datasetPageSettings->getPageType() ) {
            $this->getController()->redirect('/site/index');
        } elseif ( "public" === $datasetPageSettings->getPageType() ) {
           $this->getController()->redirect('/adminDataset/update/id/'.$model->id);
        }

        // Create token
        $model->token = Yii::$app->security->generateRandomString(16);
        $model->save();

        // Add entry to curation log
        $curationlog = new CurationLog;
        $curationlog->creation_date = date("Y-m-d");
        $curationlog->created_by = "System";
        $curationlog->dataset_id = $id;
        $curationlog->action = "Mockup created at http://gigadb.test/dataset/mockup/".$model->token;
        $curationlog->save();

        // Show a flash message
        Yii::app()->user->setFlash('success','New mockup ready at http://gigadb.test/dataset/mockup/'.$model->token);

        $this->getController()->redirect("/adminDataset/admin/");
    }
}

?>