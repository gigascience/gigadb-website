<?php
/**
 * This action for DatasetController that will display dataset mockup page
 *
 * @param $id the UUID that was generated for the mockup page
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class MockupAction extends CAction
{
    public function run($uuid)
    {

        // Yii::log("MockupAction in DatasetController with uuid: $uuid","info");
        // Retrieve mockup token data (email, validity and dataset DOI) based on url fragment 
        $srv = new FileUploadService(["webClient" => new \GuzzleHttp\Client()]);
        $tokenData = $srv->getMockupUrl($uuid);
        if (null === $tokenData) {
            throw new CHttpException(404, "Unknown mockup UUID");
        }
        $model = Dataset::model()->findByAttributes(["identifier" => $tokenData["DOI"]]);
        $datasetPageSettings = new DatasetPageSettings($model);
        $assembly = DatasetPageAssembly::assemble($model, Yii::app());

        if( "mockup" !== $datasetPageSettings->getPageType() ) {
            Yii::log("Incorrect page type is not 'mockup', instead got '".$datasetPageSettings->getPageType()."'", "error");
        }
        else {

            // Retrieving the dataset data
            $dao = new DatasetDAO(["identifier" => $tokenData["DOI"]]) ;
            $nextDataset =  $dao->getNextDataset() ?? $dao->getFirstDataset();
            $previousDataset =  $dao->getPreviousDataset() ?? $dao->getFirstDataset();
    
            // Page configuration
            $cookies = Yii::app()->request->cookies;
            $flag=null;
            $fileSettings = $datasetPageSettings->getFileSettings($cookies);
            $sampleSettings = $datasetPageSettings->getSampleSettings($cookies);

            // Assembling page components and page settings
            $assembly->setDatasetSubmitter()
                        ->setDatasetAccessions()
                        ->setDatasetMainSection()
                        ->setDatasetConnections()
                        ->setDatasetExternalLinks()
                        ->setDatasetFiles($fileSettings["pageSize"], "resourced")
                        ->setDatasetSamples($sampleSettings["pageSize"])
                        ->setSearchForm();
        }

        // Rendering section

        if( "mockup" !== $datasetPageSettings->getPageType() ) {
            return $this->getController()->redirect('/dataset/'.$tokenData['DOI']);
        }

       Yii::app()->user->setFlash("mockupMode", "Mockup created for {$tokenData['reviewerEmail']}, valid for {$tokenData['monthsOfValidity']} month(s)");
        $this->getController()->layout='new_column2';

        $this->getController()->metaData['private'] = true ; //Don't want searchengines to index this page
        $this->getController()->metaData['description'] = $assembly->getDataset()->description;

        $this->getController()->render('view', array(
            'datasetPageSettings' => $datasetPageSettings,
            'model'=>$assembly->getDataset(),
            'form'=>$assembly->getSearchForm(),
            'email' => $assembly->getDatasetSubmitter()->getEmailAddress(),
            'accessions' => $assembly->getDatasetAccessions(),
            'mainSection' => $assembly->getDatasetMainSection(),
            'connections' => $assembly->getDatasetConnections(),
            'links' => $assembly->getDatasetExternalLinks(),
            'files'=>$assembly->getDatasetFiles(),
            'samples'=>$assembly->getDatasetSamples(),
            'previous_doi' => $previousDataset->identifier,
            'previous_title' => $previousDataset->title,
            'next_title'=> $nextDataset->title,
            'next_doi' => $nextDataset->identifier,
            'setting' => $fileSettings["columns"],
            'columns' => $sampleSettings["columns"],
            'flag' => $flag,
        ));
        
    }
}

?>