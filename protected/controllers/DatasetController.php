<?php

/**
 * Routing, aggregating and composing logic for making the public dataset view
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class DatasetController extends Controller
{

    public $canonicalUrl;

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
                'actions' => array('view', 'mockup'),
                'users' => array('*'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
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
            'class' => 'application.controllers.Dataset.MockupViewAction'
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
        $srv = new FileUploadService(["webClient" => new \GuzzleHttp\Client()]);

        $datasetPageSettings = new DatasetPageSettings($model);

        $cookies = Yii::app()->request->cookies;
        $flag = null;
        $assembly = null;

        $userHostAddress = Yii::app()->request->getUserHostAddress();
        $userHostSubnet = substr($userHostAddress, 0, strrpos($userHostAddress, "."));

        // configuring files table
        if ("172.16.238" == $userHostSubnet && $id !== "101001") { //always displays all columns in tests
            $fileSettings = $datasetPageSettings->getFileSettings($cookies, DatasetPageSettings::MOCKUP_COLUMNS);
        } else {
            $fileSettings = $datasetPageSettings->getFileSettings($cookies);
        }

        if (isset($_POST['setting']) && $_POST['pageSize']) {
            $fileSettings = $datasetPageSettings->setFileSettings($_POST['setting'], $_POST['pageSize'], $cookies);
            $flag = "file";
        }


        //configuring samples table
        $sampleSettings = $datasetPageSettings->getSampleSettings($cookies);

        if (isset($_POST['columns'])) {
            $sampleSettings = $datasetPageSettings->setSampleSettings($_POST['columns'], $_POST['samplePageSize'], $cookies);
            $flag = "sample";
        }

        // Assembling page components and page settings
        $assemblyConfig = [];
        if ("invalid" !== $datasetPageSettings->getPageType()) {
            if (preg_match("/dataset\/$id\/token/",$_SERVER['REQUEST_URI'])) {
                $assemblyConfig = ['skip_cache' => true] ;
            }
            $assembly = DatasetPageAssembly::assemble($model, Yii::app(), $srv, $assemblyConfig);
            $assembly->setDatasetSubmitter()
                ->setDatasetAccessions()
                ->setDatasetMainSection()
                ->setDatasetConnections()
                ->setDatasetExternalLinks()
                ->setDatasetFiles($fileSettings["pageSize"], "stored")
                ->setDatasetSamples($sampleSettings["pageSize"])
                ->setSearchForm();

            // Rendering section
            $this->layout = 'datasetpage';

            $this->metaData['description'] = $assembly->getDataset()->description;

            $urlToRedirect = trim($assembly->getDataset()->getUrlToRedirectAttribute());
            $currentAbsoluteFullUrl = Yii::app()->request->getBaseUrl(true) . Yii::app()->request->url ;

            if ($urlToRedirect && $currentAbsoluteFullUrl == $urlToRedirect) {
                $this->metaData['redirect'] = 'http://dx.doi.org/10.5524/' . $assembly->getDataset()->identifier ;
                $this->render('interstitial', array(
                    'model' => $assembly->getDataset()
                ));
            }
        }

        // Final rendering phase

        $mainRenderer = function ($assembly, $datasetPageSettings, $previousDataset, $nextDataset, $fileSettings, $sampleSettings, $flag) {
            $this->render('view', array(
                'datasetPageSettings' => $datasetPageSettings,
                'model' => $assembly->getDataset(),
                'form' => $assembly->getSearchForm(),
                'email' => $assembly->getDatasetSubmitter()->getEmailAddress(),
                'accessions' => $assembly->getDatasetAccessions(),
                'mainSection' => $assembly->getDatasetMainSection(),
                'connections' => $assembly->getDatasetConnections(),
                'links' => $assembly->getDatasetExternalLinks(),
                'files' => $assembly->getDatasetFiles(),
                'samples' => $assembly->getDatasetSamples(),
                'previous_doi' => $previousDataset->identifier,
                'previous_title' => $previousDataset->title,
                'next_title' => $nextDataset->title,
                'next_doi' => $nextDataset->identifier,
                'setting' => $fileSettings["columns"],
                'columns' => $sampleSettings["columns"],
                'flag' => $flag,
            ));
        };

        // Different rendering based on page type (invalid, hidden, public)
        if ("invalid" === $datasetPageSettings->getPageType()) {
            $this->layout = 'datasetpage';
            $this->render('invalid', array('model' => new Dataset('search'), 'keyword' => $id, 'general_search' => 1));
        } elseif (in_array($datasetPageSettings->getPageType(), ["hidden","draft", "mockup"])) {
            // Page private ? Disable robot to index
            $this->metaData['private'] = true;

            if (preg_match("/dataset\/$id\/token/",$_SERVER['REQUEST_URI']) || preg_match("/dataset\/view\/id\/$id\/token\/.+/",$_SERVER['REQUEST_URI']) ) { //access using mockup page url
                $mainRenderer($assembly, $datasetPageSettings, $previousDataset, $nextDataset, $fileSettings, $sampleSettings, $flag);
            } else {
                Yii::log('Request is invalid for URI: '.$_SERVER['REQUEST_URI'],'error');
                $this->render('invalid', array('model' => new Dataset('search'), 'keyword' => $id));
            }
        } else { //page type is public
            // specify canonical URL due to samples and files pagination generating multiple URLs with the same main content
            $this->canonicalUrl = Yii::app()->request->hostInfo . '/dataset/' . $model->identifier;
            $mainRenderer($assembly, $datasetPageSettings, $previousDataset, $nextDataset, $fileSettings, $sampleSettings, $flag);
        }
    }
}
