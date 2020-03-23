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
        $form = new SearchForm;  // Use for Form
        $dataset = new Dataset; // Use for auto suggestion
        $this->layout='new_column2';
        $model = Dataset::model()->find("identifier=?", array($id));
        $datasetPageSettings = new DatasetPageSettings($model);
        if( "invalid" === $datasetPageSettings->getPageType() ) {
            $form = new SearchForm;
            $keyword = $id;
            $this->render('invalid', array('model' => $form, 'keyword' => $keyword, 'general_search' => 1));
        }
        elseif("hidden" === $datasetPageSettings->getPageType() && $model->token !== $_GET['token']) {
            $form = new SearchForm;
            $keyword = $id;
            $this->render('invalid', array('model' => $form, 'keyword' => $keyword));
        }

        $urlToRedirect = trim($model->getUrlToRedirectAttribute());
        $currentAbsoluteFullUrl = Yii::app()->request->getBaseUrl(true) . Yii::app()->request->url ;

        if ($urlToRedirect && $currentAbsoluteFullUrl == $urlToRedirect) {
            $this->metaData['redirect'] = 'http://dx.doi.org/10.5524/'.$model->identifier ;
            $this->render('interstitial', array(
                'model'=>$model
            ));
            return;
        }

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

        // Creating a Database cache dependency
        $cacheDependency = new CDbCacheDependency();

        // Submitter email web component
        $datasetSubmitter = new AuthorisedDatasetSubmitter(
                                Yii::app()->user,
                                new CachedDatasetSubmitter(
                                    Yii::app()->cache,
                                    $cacheDependency,
                                    new StoredDatasetSubmitter(
                                        $model->id,
                                        Yii::app()->db
                                    )
                                )
                        );
        $email = $datasetSubmitter->getEmailAddress();

        // Accesssions links web component
        $accessions = new FormattedDatasetAccessions(
                                new AuthorisedDatasetAccessions(
                                    Yii::app()->user,
                                    new CachedDatasetAccessions(
                                        Yii::app()->cache,
                                        $cacheDependency,
                                        new StoredDatasetAccessions(
                                            $model->id,
                                            Yii::app()->db
                                        )
                                    )
                                ),
                                'target="_blank"'
        );

        // Main section with headline, release details, description and citations
       $mainSection = new FormattedDatasetMainSection(
                        new CachedDatasetMainSection (
                            Yii::app()->cache,
                            $cacheDependency,
                            new StoredDatasetMainSection(
                                $model->id,
                                Yii::app()->db
                            )
                    )
                );

       //Dataset connections (other datasets related to this one)
       $connections = new FormattedDatasetConnections(
                            Yii::app()->controller,
                        new CachedDatasetConnections (
                            Yii::app()->cache,
                            $cacheDependency,
                            new StoredDatasetConnections(
                                $model->id,
                                Yii::app()->db,
                                new \GuzzleHttp\Client()
                            )
                    )
                );

        //External links
        $external_links = new FormattedDatasetExternalLinks(
                            new CachedDatasetExternalLinks(
                                Yii::app()->cache,
                                $cacheDependency,
                                new StoredDatasetExternalLinks(
                                    $model->id,
                                    Yii::app()->db
                                )
                            )
                        );

        //Files
         $filesDataProvider = new FormattedDatasetFiles(
                            $pageSize,
                            new CachedDatasetFiles(
                                Yii::app()->cache,
                                $cacheDependency,
                                new StoredDatasetFiles(
                                    $model->id,
                                    Yii::app()->db
                                )
                            )
                        );
        //Samples
         $samplesDataProvider = new FormattedDatasetSamples(
                            $perPage,
                            new CachedDatasetSamples(
                                Yii::app()->cache,
                                $cacheDependency,
                                new StoredDatasetSamples(
                                    $model->id,
                                    Yii::app()->db
                                )
                            )
                        );

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

        $this->metaData['description'] = $model->description;
        // Page private ? Disable robot to index
        $this->metaData['private'] = (Dataset::DATASET_PRIVATE == $model->upload_status);
        // Yii::log("ActionView: about to render",CLogger::LEVEL_ERROR,"DatasetController");

        $this->render('view', array(
            'model'=>$model,
            'datasetPageSettings' => $datasetPageSettings,
            'form'=>$form,
            'dataset'=>$dataset,
            'files'=>$filesDataProvider,
            'samples'=>$samplesDataProvider,
            'email' => $email,
            'accessions' => $accessions,
            'mainSection' => $mainSection,
            'connections' => $connections,
            'links' => $external_links,
            'previous_doi' => $previous_doi,
            'previous_title' => $previous_title,
            'next_title'=> $next_title,
            'next_doi' => $next_doi,
            'setting' => $setting,
            'columns' => $columns,
            'logs'=>$model->datasetLogs,
            'flag' => $flag,
        ));
    }

}
?>