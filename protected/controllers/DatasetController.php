<?php
/**
 * Routing, aggregating and composing logic for making the public dataset view
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class DatasetController extends Controller
{
	/**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions'=>array('view', 'mockup'),
                'users'=>array('*'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

  /**
     * Yii's method for routing urls to an action. Override to use custom actions
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['mockup'] = [
            'class' => 'application.controllers.dataset.MockupAction'
        ];
        return $actions;
    }

    public function actionView($id)
    {
        // Retrieving the data
        $model = Dataset::model()->find("identifier=?", array($id));
        $dao = new DatasetDAO(["identifier" => $id]) ;
        $nextDataset =  $dao->getNextDataset() ?? $dao->getFirstDataset();
        $previousDataset =  $dao->getPreviousDataset() ?? $dao->getFirstDataset();

        $datasetPageSettings = new DatasetPageSettings($model);

        $cookies = Yii::app()->request->cookies;
        $flag=null;

        // configuring files table
        $fileSettings = $datasetPageSettings->getFileSettings($cookies);

        if (isset($_POST['setting']) && $_POST['pageSize']) {
            $fileSettings = $datasetPageSettings->setFileSettings($_POST['setting'], $_POST['pageSize'], $cookies);
            $flag="file";
        }

        //configuring samples table
        $sampleSettings = $datasetPageSettings->getSampleSettings($cookies);

        if (isset($_POST['columns'])) {
            $sampleSettings = $datasetPageSettings->setSampleSettings($_POST['columns'], $_POST['samplePageSize'], $cookies);
            $flag="sample";
        }

        // Assembling page components and page settings

        $assembly = DatasetPageAssembly::assemble($model, Yii::app());
        $assembly->setDatasetSubmitter()
                    ->setDatasetAccessions()
                    ->setDatasetMainSection()
                    ->setDatasetConnections()
                    ->setDatasetExternalLinks()
                    ->setDatasetFiles($fileSettings["pageSize"])
                    ->setDatasetSamples($sampleSettings["pageSize"])
                    ->setSearchForm();

        // Rendering section
         $this->layout='new_column2';

        $this->metaData['description'] = $assembly->getDataset()->description;

        $urlToRedirect = trim($assembly->getDataset()->getUrlToRedirectAttribute());
        $currentAbsoluteFullUrl = Yii::app()->request->getBaseUrl(true) . Yii::app()->request->url ;

        if ($urlToRedirect && $currentAbsoluteFullUrl == $urlToRedirect) {
            $this->metaData['redirect'] = 'http://dx.doi.org/10.5524/'.$assembly->getDataset()->identifier ;
            $this->render('interstitial', array(
                'model'=>$assembly->getDataset()
            ));
        }

        if( "invalid" === $datasetPageSettings->getPageType() ) {
            $this->render('invalid', array('model' => $assembly->getSearchForm(), 'keyword' => $id, 'general_search' => 1));
        }
        elseif( "hidden" === $datasetPageSettings->getPageType() ) {
            // Page private ? Disable robot to index
            $this->metaData['private'] = (Dataset::DATASET_PRIVATE === $assembly->getDataset()->upload_status);
            if ( isset($_GET['token']) && $assembly->getDataset()->token !== $_GET['token'] ) {
                $this->render('invalid', array('model' => $assembly->getSearchForm(), 'keyword' => $id));
            }
        }

        $this->render('view', array(
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