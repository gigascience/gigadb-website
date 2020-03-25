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
                'actions'=>array('view'),
                'users'=>array('*'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }


    public function actionView($id)
    {
        $this->layout='new_column2';
        $model = Dataset::model()->find("identifier=?", array($id));
        

        $cookies = Yii::app()->request->cookies;
        // file
        $setting = DatasetPageSettings::VIEW_DEFAULT_FILE_COLUMNS;
        $pageSize = 10;
        $flag=null;

        if (isset($cookies['file_setting'])) {
            //$ss = json_decode($cookies['sample_setting']);
            $fcookie = $cookies['file_setting'];
            $fcookie = json_decode($fcookie->value, true);
            if ($fcookie['setting']) {
                $setting = $fcookie['setting'];
            }
            $pageSize = $fcookie['page'];
        }

        if (isset($_POST['setting'])) {
            $setting = $_POST['setting'];
            $pageSize = $_POST['pageSize'];

            if (isset($cookies['file_setting'])) {
                unset(Yii::app()->request->cookies['file_setting']);
            }

            $nc = new CHttpCookie('file_setting', json_encode(array('setting'=> $setting, 'page'=>$pageSize)));
            $nc->expire = time() + (60*60*24*30);
            Yii::app()->request->cookies['file_setting'] = $nc;
            $flag="file";
        }


        //Sample
        $columns = array('name', 'taxonomic_id', 'genbank_name', 'scientific_name', 'common_name', 'attribute');
        $perPage = 10;
        if (isset($cookies['sample_setting'])) {
            //$ss = json_decode($cookies['sample_setting']);
            $scookie = $cookies['sample_setting'];
            $scookie = json_decode($scookie->value, true);
            if ($scookie['columns']) {
                $columns = $scookie['columns'];
            }
            $perPage = $scookie['page'];
        }

        if (isset($_POST['columns'])) {
            $columns = $_POST['columns'];
            $perPage = $_POST['samplePageSize'];
            $flag="sample";
            if (isset($cookies['sample_setting'])) {
                unset(Yii::app()->request->cookies['sample_setting']);
            }

            $ncookie = new CHttpCookie('sample_setting', json_encode(array('columns'=> $columns, 'page'=>$perPage)));
            $ncookie->expire = time() + (60*60*24*30);
            Yii::app()->request->cookies['sample_setting'] = $ncookie;
        }

        $result = Dataset::model()->findAllBySql("select identifier,title from dataset where identifier > '" . $id . "' and upload_status='Published' order by identifier asc limit 1;");
        if (count($result) == 0) {
            $result = Dataset::model()->findAllBySql("select identifier,title from dataset where upload_status='Published' order by identifier asc limit 1;");
            $next_doi = $result[0]->identifier;
            $next_title = $result[0]->title;
        } else {
            $next_doi = $result[0]->identifier;
            $next_title = $result[0]->title;
        }

        $result = Dataset::model()->findAllBySql("select identifier,title from dataset where identifier < '" . $id . "' and upload_status='Published' order by identifier desc limit 1;");
        if (count($result) == 0) {
            $result = Dataset::model()->findAllBySql("select identifier,title from dataset where upload_status='Published' order by identifier desc limit 1;");
            $previous_doi = $result[0]->identifier;
            $previous_title = $result[0]->title;
        } else {
            $previous_doi = $result[0]->identifier;
            $previous_title = $result[0]->title;
        }


        // Assembling page components and page settings

        $datasetPageSettings = new DatasetPageSettings($model);

        $assembly = DatasetPageAssembly::assemble($model, Yii::app());
        $assembly->setDatasetSubmitter()
                    ->setDatasetAccessions()
                    ->setDatasetMainSection()
                    ->setDatasetConnections()
                    ->setDatasetExternalLinks()
                    ->setDatasetFiles($pageSize)
                    ->setDatasetSamples($perPage)
                    ->setSearchForm();

        // Rendering section

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
        elseif("hidden" === $datasetPageSettings->getPageType() && $assembly->getDataset()->token !== $_GET['token']) {
            // Page private ? Disable robot to index
            $this->metaData['private'] = (Dataset::DATASET_PRIVATE == $assembly->getDataset()->upload_status);
            $this->render('invalid', array('model' => $assembly->getSearchForm(), 'keyword' => $id));
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
            'previous_doi' => $previous_doi,
            'previous_title' => $previous_title,
            'next_title'=> $next_title,
            'next_doi' => $next_doi,
            'setting' => $setting,
            'columns' => $columns,
            'flag' => $flag,
        ));
    }

}
?>