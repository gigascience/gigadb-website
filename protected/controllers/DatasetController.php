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
        if (!$model) {
            $form = new SearchForm;
            $keyword = $id;
            $this->render('invalid', array('model' => $form, 'keyword' => $keyword, 'general_search' => 1));
            return;
        }
        $this->metaData['description'] = $model->description;
        // $status_array = array('Request', 'Incomplete', 'Uploaded');

        if ($model->upload_status != "Published") {
            if (isset($_GET['token']) && $model->token == $_GET['token']) {
            } else {
                $form = new SearchForm;
                $keyword = $id;
                $this->render('invalid', array('model' => $form, 'keyword' => $keyword));
                return;
            }
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
        $crit = new CDbCriteria;
        $crit->addCondition("t.dataset_id = ".$model->id);
        $crit->select = '*';
        $crit->join = "LEFT JOIN dataset ON dataset.id = t.dataset_id LEFT JOIN file_type ft ON t.type_id = ft.id
                LEFT JOIN file_format ff ON t.format_id = ff.id";

        $cookies = Yii::app()->request->cookies;
        // file
        $setting = array('name','size', 'type_id', 'format_id', 'location', 'date_stamp','sample_id'); // 'description','attribute' are hidden by default
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


        $files = new CActiveDataProvider('File', array(
            'criteria'=> $crit,
            'sort' => array('defaultOrder'=>'name ASC',
                            'attributes' => array(
                                'name',
                                'description',
                                'size',
                                'type_id' => array('asc'=>'ft.name asc', 'desc'=>'ft.name desc'),
                                'format_id' => array('asc'=>'ff.name asc', 'desc'=>'ff.name desc'),
                                'date_stamp',
                            )),
            'pagination' => false,
        ));

        $files_pagination = new CPagination($files->getTotalItemCount());
        $files_pagination->setPageSize($pageSize);
        $files_pagination->pageVar = "Files_page";
        // Yii::log("files pageVar: ". $files_pagination->pageVar,CLogger::LEVEL_ERROR,"DatasetController");
        $files->setPagination($files_pagination);



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

        $scrit = new CDbCriteria;
        $scrit->join = "LEFT JOIN dataset_sample ds ON ds.sample_id = t.id LEFT JOIN species ON t.species_id = species.id";
        $scrit->condition = "ds.dataset_id = :id";
        $scrit->params = array(':id' => $model->id);
        $samples = new CActiveDataProvider('Sample', array(
            'criteria'=> $scrit,
            'pagination' => false,
            'sort' => array('defaultOrder'=>'t.name ASC',
                            'attributes' => array(
                                    'name',
                                    'common_name' => array(
                                            'asc' => 'species.common_name ASC',
                                            'desc' => 'species.common_name DESC',
                                        ),
                                    'genbank_name' => array(
                                            'asc' => 'species.genbank_name ASC',
                                            'desc' => 'species.genbank_name DESC',
                                        ),
                                    'scientific_name' => array(
                                            'asc' => 'species.scientific_name ASC',
                                            'desc' => 'species.scientific_name DESC',
                                        ),
                                    'taxonomic_id' => array(
                                            'asc' => 'species.tax_id ASC',
                                            'desc' => 'species.tax_id DESC',
                                        ),
                                )),
        ));


        $samples_pagination = new CPagination($samples->getTotalItemCount());
        $samples_pagination->setPageSize($perPage);
        $samples_pagination->pageVar = "Samples_page";
        // Yii::log("samples pageVar: ". $samples_pagination->pageVar,CLogger::LEVEL_ERROR,"DatasetController");
        $samples->setPagination($samples_pagination);

        // Submitter email web component
        $datasetSubmitter = new AuthorisedDatasetSubmitter(
                                Yii::app()->user,
                                new CachedDatasetSubmitter(
                                    Yii::app()->cache,
                                    new StoredDatasetSubmitter(
                                        $id,
                                        Yii::app()->db
                                    )
                                )
                        );
        $email = $datasetSubmitter->getEmailAddress();

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

        $attributes = $model->attributes;
        $l = array();
        foreach ($attributes as $att) {
            $l[] = $att->id;
        }

        $authors = $model->authors;
        $at = array();
        foreach ($authors as $au) {
            $at[] = $au->id;
        }

        $relateCriteria = new CDbCriteria;
        $relateCriteria->distinct = true;
        $relateCriteria->addNotInCondition("t.id", array($model->id));
        $relateCriteria->addCondition("t.upload_status = 'Published'");
        $relateCriteria->limit = 9;

        $rc = clone $relateCriteria;
        $rc->join = "JOIN dataset_attributes da ON t.id = da.dataset_id JOIN dataset_author au ON t.id = au.dataset_id";
        $rc->addInCondition("da.attribute_id", $l);
        $rc->addInCondition("au.author_id", $at, 'OR');
        $rc->addCondition("t.upload_status = 'Published'");
        $relates = Dataset::model()->findAll($rc);

        // if we don't find any dataset related by the first way, then by common type
        if (!$relates || count($relates) < 9) {
            $relatesIds = array($model->id);
            foreach ($relates as $relate) {
                $relatesIds[] = $relate->id;
            }

            $rc = clone $relateCriteria;
            $rc->join = "JOIN dataset_type dt ON t.id = dt.dataset_id";
            $rc->addInCondition("dt.type_id", $model->getTypeIds());
            $rc->addNotInCondition("t.id", $relatesIds);
            $rc->limit = 9 - count($relates);
            $relatesType = Dataset::model()->findAll($rc);

            foreach ($relatesType as $relate) {
                $relates[] = $relate;
            }
        }

        $scholar = $model->cited;

        $link_type = 'EBI';
        if (!Yii::app()->user->isGuest) {
            $user = User::model()->findByPk(Yii::app()->user->_id);
            if ($user) {
                $link_type = $user->preferred_link;
            }
        }

        // Page private ? Disable robot to index
        $this->metaData['private'] = (Dataset::DATASET_PRIVATE == $model->upload_status);
        // Yii::log("ActionView: about to render",CLogger::LEVEL_ERROR,"DatasetController");

        $this->render('view', array(
            'model'=>$model,
            'form'=>$form,
            'dataset'=>$dataset,
            'files'=>$files,
            'samples'=>$samples,
            'email' => $email,
            'previous_doi' => $previous_doi,
            'previous_title' => $previous_title,
            'next_title'=> $next_title,
            'next_doi' => $next_doi,
            'setting' => $setting,
            'columns' => $columns,
            'logs'=>$model->datasetLogs,
            'relates' => $relates,
            'scholar' => $scholar,
            'link_type' => $link_type,
            'flag' => $flag,
        ));
    }

}
?>