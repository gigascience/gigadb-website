<?php
/**
 * Routing, aggregating and composing logic for Dataset submission
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class DatasetSubmissionController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    //public $layout='//layouts/new_column2';

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
            array('allow',  // allow logged-in users to perform 'upload'
                'actions'=>array('choose', 'upload','delete','create1','submit','updateSubmit', 'updateFile',
                    'datasetManagement', 'authorManagement', 'validateAuthor', 'addAuthors', 'saveAuthors',
                    'additionalManagement', 'saveAdditional',
                    'fundingManagement', 'validateFunding', 'saveFundings',
                    'projectManagement','linkManagement','exLinkManagement',
                    'relatedDoiManagement','sampleManagement', 'getAttributes', 'saveSamples', 'validateSamples', 'checkUnit', 'end', 'PxInfoManagement','datasetAjaxDelete', 'datasetAjaxUndo'),
                'users'=>array('@'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
     * Index page.
     */
    public function actionChoose()
    {
        $this->render('choose');
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $dataProvider=new CActiveDataProvider('Dataset');
        $this->render('index', array(
            'dataProvider'=>$dataProvider,
        ));
    }

    public function actionUpload()
    {
        $file = CUploadedFile::getInstanceByName('xls');
        if ($file) {
            $loggedUser = MainHelper::getLoggedUser();
            if (MailHelper::sendUploadedDatasetToAdmin($loggedUser, $file->getTempName(), $file->getName())) {
                $this->redirect('/datasetSubmission/upload/status/successful');
            } else {
                $this->redirect('/datasetSubmission/upload/status/failed');
            }
        }

        $this->render('upload');
    }


    public function actionSubmit()
    {
        if (!isset($_GET['id'])) {
            $this->redirect("/user/view_profile");
        } else {
            $dataset_id = $_GET['id'];

            $dataset = $this->getDataset($_GET['id']);

            $this->isSubmitter($dataset);

            $isOld = 1;
            if ($dataset->upload_status == 'UserStartedIncomplete') {
                $isOld = 0;
            }

            if (isset($_POST['file'])) {
                $dataset->upload_status = 'Pending';
                CurationLog::createlog($dataset->upload_status, $dataset->id);
            } else {
                $dataset->upload_status = 'Submitted';
                CurationLog::createlog($dataset->upload_status, $dataset->id);
            }

            $dataset->modification_date = date('Y-m-d');
            if (!$dataset->save(false)) {
                Yii::app()->user->setFlash('keyword', "Submit failure" . $dataset_id);
                $this->redirect("/user/view_profile");
                return;
            }
        }

        $user = User::model()->findByPk(Yii::app()->user->id);

        if (!$isOld) {
            MailHelper::sendNewSubmittedDatasetToAdmin($user, $dataset);
        } else {
            MailHelper::sendUpdateDatasetToAdmin($user, $dataset);
        }

        $this->redirect('/user/view_profile/thanks/1/added/' . $dataset->id . '#submitted');
    }

    public function actionUpdateSubmit()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $identifier = Dataset::model()->findByAttributes(array('id' => $id))->identifier;
            $dataset_session = DatasetSession::model()->findByAttributes(array('identifier' => $identifier));
            if ($dataset_session == null) {
                return $this->redirect("/user/view_profile");
            }
            $vars = array('dataset', 'images', 'identifier', 'dataset_id',
                'datasettypes', 'authors', 'projects',
                'links', 'externalLinks', 'relations', 'samples');
            foreach ($vars as $var) {
                $_SESSION[$var] = CJSON::decode($dataset_session->$var);
            }
            //indicate that this is an old dataset
            $_SESSION['isOld'] = 1;

            $this->redirect("/datasetSubmission/create1");
        }
        Yii::app()->user->setFlash('keyword', 'no dataset is specified');
        return $this->redirect("/user/view_profile");
    }


    public function actionUpdateFile()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $user = User::model()->findByPk(Yii::app()->user->id);
            $dataset = Dataset::model()->findByattributes(array('id' => $id));
            if ($user->id != $dataset->submitter_id) {
                return false;
            }
            $identifier = $dataset->identifier;
            $dataset_session = DatasetSession::model()->findByAttributes(array('identifier' => $identifier));
            if ($dataset_session == null) {
                return $this->redirect("/user/view_profile");
            }
            $vars = array('dataset', 'images', 'identifier', 'dataset_id',
                'datasettypes', 'authors', 'projects',
                'links', 'externalLinks', 'relations', 'samples');
            foreach ($vars as $var) {
                $_SESSION[$var] = CJSON::decode($dataset_session->$var);
            }
            $_SESSION['isOld'] = 1;
            $this->redirect("/adminFile/create1");
        }
        Yii::app()->user->setFlash('keyword', 'no dataset is specified');
        return $this->redirect("/user/view_profile");
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax']==='dataset-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionCreate1()
    {
        if (isset($_GET['id'])) {
            $this->redirect(array('/datasetSubmission/datasetManagement', 'id'=>$_GET['id']));
        } else {
            $dataset = new Dataset();
            $dataset->is_test = isset($_GET['is_test']) && $_GET['is_test'] === '1' ? 1 : 0;
            $dataset->creation_date = date('Y-m-d');
            $image = new Images();
        }

        $this->datasetUpdate($dataset, $image);

        $this->render('create1', array('model' => $dataset, 'image'=>$image));
    }

    public function actionDatasetManagement()
    {
        if (isset($_GET['id'])) {
            $dataset = $this->getDataset($_GET['id']);
            $dataset->modification_date = date('Y-m-d');
            $image = $dataset->image ?: new Images();

            if (isset($_POST['Images'])) {
                $image->setIsNoImage(!!$_POST['Images']['is_no_image']);
            } else {
                $image->setIsNoImage($image->location == 'no_image.jpg');
            }

            $this->isSubmitter($dataset);
        } else {
            $this->redirect($this->getRedirectUrl());
        }

        $this->datasetUpdate($dataset, $image);

        $this->render('create1', array('model' => $dataset, 'image'=>$image));
    }

    protected function datasetUpdate(Dataset $dataset, Images $image)
    {
        if (isset($_POST['Dataset']) && isset($_POST['Images'])) {
            $newKeywords = isset($_POST['keywords']) ? $_POST['keywords'] : '';
            $newTypes = isset($_POST['datasettypes']) ? $_POST['datasettypes'] : array();

            $image->loadByData($_POST['Images']);
            $dataset->loadByData($_POST['Dataset']);
            $dataset->types = $newTypes;
            $dataset->keywords = explode(',', $newKeywords);
            $datasetValidate = $dataset->validate();
            $imageValidate = $image->validate();
            if ($datasetValidate && $imageValidate) {
                $image->save();
                $dataset->image_id = $image->id;
                $dataset->save();

                $dataset->updateKeywords($newKeywords);
                $dataset->updateTypes($newTypes);

                $image->saveImageFile();

                if (isset($_POST['redirect_url']) && $_POST['redirect_url']) {
                    $this->redirect($_POST['redirect_url']);
                }
                $this->redirect(array('/datasetSubmission/datasetManagement', 'id'=>$dataset->id));
            }
        }
    }

    public function actionAuthorManagement()
    {
        if (!isset($_GET['id'])) {
            $this->redirect($this->getRedirectUrl());
        } else {
            $dataset = $this->getDataset($_GET['id']);

            $this->isSubmitter($dataset);

            $das = DatasetAuthor::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'rank asc'));
            $contributions = Contribution::model()->findAll(array('order'=>'name asc'));

            $this->render('authorManagement', array(
                'model' => $dataset,
                'das'=>$das,
                'contributions' => $contributions,
            ));
        }
    }


    /**
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionValidateAuthor() {
        if(isset($_POST['dataset_id']) && isset($_POST['Author'])) {
            $author = new Author();
            $author->loadByData($_POST['Author']);
            if($author->validate()) {
                $contribution = Contribution::model()->findByAttributes(array('name'=>$_POST['Author']['contribution']));
                if (!$contribution) {
                    Util::returnJSON(array("success"=>false,"message"=>'Credit is invalid.'));
                }

                Util::returnJSON(array(
                    "success"=>true,
                    'author' => array(
                        'first_name' => $author->first_name,
                        'middle_name' => $author->middle_name,
                        'last_name' => $author->surname,
                        'orcid' => $author->orcid,
                        'contribution' => $contribution->name,
                    ),
                ));
            }

            Util::returnJSON(array("success"=>false,"message"=>current($author->getErrors())));
        }
    }

    /**
     * @throws Exception
     */
    public function actionAddAuthors() {
        $authors = CUploadedFile::getInstanceByName('authors');
        if($authors) {
            $rows = CsvHelper::parse($authors->getTempName(), $authors->getExtensionName());

            $authors = array();
            foreach ($rows as $i => $row) {
                $num = $i + 1;
                $author = new Author();
                $author->loadByCsvRow($row);
                if($author->validate()) {
                    $contribution = Contribution::model()->findByAttributes(array('name'=>$row[4]));
                    if (!$contribution) {
                        Util::returnJSON(array("success"=>false,"message"=> "Row $num: " . 'Credit is invalid.'));
                    }

                    $authors[] = array(
                        'first_name' => $author->first_name,
                        'middle_name' => $author->middle_name,
                        'last_name' => $author->surname,
                        'orcid' => $author->orcid,
                        'contribution' => $contribution->name,
                    );
                } else {
                    $error = current($author->getErrors());
                    Util::returnJSON(array("success"=>false,"message"=> "Row $num: " . $error[0]));
                }
            }

            Util::returnJSON(array("success"=>true, 'authors' => $authors));
        }

        Util::returnJSON(array("success"=>false,"message"=>"You must input file."));
    }

    /**
     * @throws CException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSaveAuthors() {
        if(isset($_POST['dataset_id'])) {
            $dataset = $this->getDataset($_POST['dataset_id']);

            $transaction = Yii::app()->db->beginTransaction();
            if (isset($_POST['authors']) && is_array($_POST['authors'])) {
                foreach ($_POST['authors'] as $i => $row) {
                    $num = $i + 1;
                    if ($row['id']) {
                        $da = DatasetAuthor::model()->findByPk($row['id']);
                        if (!$da) {
                            $transaction->rollback();
                            Util::returnJSON(array("success" => false, "message" => "Row $num: Wrong id"));
                        }
                        $author = $da->author;
                    } else {
                        $author = new Author();
                        $author->loadByData($row);
                    }

                    if ($author->validate()) {
                        $author->save();
                        $dataset->addAuthor($author, $row['order'], $row['contribution']);
                    } else {
                        $transaction->rollback();
                        $error = current($author->getErrors());
                        Util::returnJSON(array("success" => false, "message" => "Row $num: " . $error[0]));
                    }
                }
            }

            if (isset($_POST['delete_ids']) && is_array($_POST['delete_ids'])) {
                foreach ($_POST['delete_ids'] as $deleteId) {
                    $da = DatasetAuthor::model()->findByPk($deleteId);
                    if ($da) {
                        if ($da->delete()) {
                            $da->author->delete();
                        }
                    }
                }
            }

            $transaction->commit();
            Util::returnJSON(array("success"=>true));
        }

        Util::returnJSON(array("success"=>false,"message"=>"Data is empty."));
    }

    /**
     * Additional page.
     */
    public function actionAdditionalManagement()
    {
        if (!isset($_GET['id'])) {
            $this->redirect($this->getRedirectUrl());
        } else {
            $dataset = $this->getDataset($_GET['id']);

            $this->isSubmitter($dataset);

            $links = Link::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

            $link_database = Yii::app()->db->createCommand()
                ->select("prefix")
                ->from("prefix")
                ->order("prefix asc")
                ->group("prefix")
                ->queryAll();

            $relations = Relation::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'related_doi asc'));

            $dps = DatasetProject::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

            $manuscripts = Manuscript::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

            $protocols = ExternalLink::model()->findAllByAttributes(array(
                'dataset_id'=>$dataset->id,
                'external_link_type_id' => AIHelper::PROTOCOLS
            ), array('order'=>'id asc'));

            $_3dImages = ExternalLink::model()->findAllByAttributes(array(
                'dataset_id'=>$dataset->id,
                'external_link_type_id' => AIHelper::_3D_IMAGES
            ), array('order'=>'id asc'));

            $codes = ExternalLink::model()->findAllByAttributes(array(
                'dataset_id'=>$dataset->id,
                'external_link_type_id' => AIHelper::CODES
            ), array('order'=>'id asc'));

            $sources = ExternalLink::model()->findAllByAttributes(array(
                'dataset_id'=>$dataset->id,
                'external_link_type_id' => AIHelper::SOURCES
            ), array('order'=>'id asc'));

            $this->render('additionalManagement', array(
                'model' => $dataset,
                'links' => $links,
                'link_database' => $link_database,
                'relations' => $relations,
                'dps' => $dps,
                'manuscripts' => $manuscripts,
                'protocols' => $protocols,
                '_3dImages' => $_3dImages,
                'codes' => $codes,
                'sources' => $sources,
            ));
        }
    }

    /**
     * @throws CException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSaveAdditional() {
        if(isset($_POST['dataset_id'])) {
            $dataset = $this->getDataset($_POST['dataset_id']);

            $transaction = Yii::app()->db->beginTransaction();

            $links = Link::model()->findAllByAttributes(array('dataset_id'=>$dataset->id));
            $newLinks = isset($_POST['publicLinks']) && is_array($_POST['publicLinks']) ? $_POST['publicLinks'] : array();
            $needLinks = array();
            if ($newLinks) {
                foreach ($newLinks as $newLink) {
                    if (!$newLink['id']) {
                        $link = new Link;
                        $link->dataset_id = $_POST['dataset_id'];
                        $link->is_primary = true;
                        $link->link = $newLink['link_type'] . ":" . $newLink['link'];

                        if (!$link->validate()) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=>"Save Error."
                            ));
                        }

                        $link->save();
                    } else {
                        $needLinks[] = $newLink['id'];
                    }
                }
            }


            foreach ($links as $link) {
                if (!in_array($link->id, $needLinks)) {
                    $link->delete();
                }
            }

            $relations = Relation::model()->findAllByAttributes(array('dataset_id'=>$dataset->id));
            $newRelations = isset($_POST['relatedDoi']) && is_array($_POST['relatedDoi']) ? $_POST['relatedDoi'] : array();
            $needRelations = array();
            if ($newRelations) {
                foreach ($newRelations as $newRelation) {
                    if (!$newRelation['id']) {
                        $relation = new Relation;
                        $relation->dataset_id = $_POST['dataset_id'];
                        $relation->related_doi = $newRelation['related_doi'];
                        $relation->relationship_id = $newRelation['relationship_id'];

                        if (!$relation->validate()) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=>"Save Error."
                            ));
                        }

                        $relation->save();
                    } else {
                        $needRelations[] = $newRelation['id'];
                    }
                }
            }

            foreach ($relations as $relation) {
                if (!in_array($relation->id, $needRelations)) {
                    $relation->delete();
                }
            }

            $projects = DatasetProject::model()->findAllByAttributes(array('dataset_id'=>$dataset->id));
            $newProjects = isset($_POST['projects']) && is_array($_POST['projects']) ? $_POST['projects'] : array();
            $needProjects = array();
            if ($newProjects) {
                foreach ($newProjects as $newProject) {
                    if (!$newProject['id']) {
                        $dp = new DatasetProject;
                        $dp->dataset_id = $_POST['dataset_id'];
                        $dp->project_id = $newProject['project_id'];

                        if (!$dp->validate()) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=>"Save Error."
                            ));
                        }

                        $dp->save();
                    } else {
                        $needProjects[] = $newProject['id'];
                    }
                }
            }

            foreach ($projects as $project) {
                if (!in_array($project->id, $needProjects)) {
                    $project->delete();
                }
            }

            $exLinks = ExternalLink::model()->findAllByAttributes(array('dataset_id'=>$dataset->id));
            $manuscripts = Manuscript::model()->findAllByAttributes(array('dataset_id'=>$dataset->id));
            $newExLinks = isset($_POST['exLinks']) && is_array($_POST['exLinks']) ? $_POST['exLinks'] : array();
            $needExLinks = array();
            $needManuscripts = array();
            if ($newExLinks) {
                foreach ($newExLinks as $newExLink) {
                    if (!$newExLink['id']) {
                        if ($newExLink['externalLinkType'] == AIHelper::MANUSCRIPTS) {
                            $exLink = new Manuscript;
                            $exLink->dataset_id = $newExLink['dataset_id'];
                            $exLink->identifier = $newExLink['url'];
                        } else {
                            $exLink = new ExternalLink;
                            $exLink->loadByData($newExLink);
                        }

                        if (!$exLink->validate()) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=>"Save Error."
                            ));
                        }

                        $exLink->save();
                    } else {
                        if ($newExLink['externalLinkType'] == AIHelper::MANUSCRIPTS) {
                            $needManuscripts[] = $newExLink['id'];
                        } else {
                            $needExLinks[] = $newExLink['id'];
                        }
                    }
                }
            }

            foreach ($exLinks as $exLink) {
                if (!in_array($exLink->id, $needExLinks)) {
                    $exLink->delete();
                }
            }

            foreach ($manuscripts as $manuscript) {
                if (!in_array($manuscript->id, $needManuscripts)) {
                    $manuscript->delete();
                }
            }

            $dataset->additional_information = 1;
            $dataset->save(false);

            $transaction->commit();
            Util::returnJSON(array("success"=>true));
        }

        Util::returnJSON(array("success"=>false,"message"=>"Data is empty."));
    }

    /**
     * Funding page.
     */
    public function actionFundingManagement()
    {
        if (!isset($_GET['id'])) {
            $this->redirect($this->getRedirectUrl());
        } else {
            $dataset = $this->getDataset($_GET['id']);

            $this->isSubmitter($dataset);

            $funders = Funder::model()->findAllByAttributes(array(), array('order'=>'primary_name_display asc'));
            $fundings = DatasetFunder::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

            $this->render('fundingManagement', array(
                'model' => $dataset,
                'funders' => $funders,
                'fundings' => $fundings,
            ));
        }
    }

    public function actionValidateFunding() {
        if ($_POST) {
            $funding = new DatasetFunder();
            $funding->loadByData($_POST);

            if ($funding->validate()) {
                Util::returnJSON( array(
                    "success" => true,
                    'funding' => $funding->asArray(),
                ));
            }

            $error = current($funding->getErrors());
            Util::returnJSON( array(
                "success" => false,
                'message' => $error[0],
            ));
        }

        Util::returnJSON(array(
            "success"=>false,
            "message"=> "Data is empty."
        ));
    }

    /**
     * @throws CException
     */
    public function actionSaveFundings() {
        if(isset($_POST['dataset_id'])) {
            $transaction = Yii::app()->db->beginTransaction();

            $dataset = $this->getDataset($_POST['dataset_id']);
            $hasFunding = 0;

            $fundings = $dataset->datasetFunders;

            $newFundings = isset($_POST['fundings']) && is_array($_POST['fundings']) ? $_POST['fundings'] : array();

            $needFundings = array();
            foreach ($newFundings as $newFunding) {
                if ($newFunding['id']) {
                    $needFundings[] = $newFunding['id'];
                }
            }

            foreach ($fundings as $funding) {
                if (!in_array($funding->id, $needFundings)) {
                    if (!$funding->delete()) {
                        $transaction->rollback();
                        Util::returnJSON(array(
                            "success"=>false,
                            "message"=>"Save Error."
                        ));
                    }
                }
            }

            if ($newFundings) {
                foreach ($newFundings as $newFunding) {
                    if (!$newFunding['id']) {
                        $funding = new DatasetFunder();
                        $funding->loadByData($newFunding);
                        if (!$funding->validate()) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=>current($funding->getErrors())
                            ));
                        }

                        $funding->save();
                    }
                }

                $hasFunding = 1;
            }

            $dataset->funding = $hasFunding;
            if (!$dataset->save(false)) {
                $transaction->rollback();
                Util::returnJSON(array(
                    "success"=>false,
                    "message"=>"Save Error."
                ));
            }

            $transaction->commit();
            Util::returnJSON(array("success"=>true));
        }

        Util::returnJSON(array("success"=>false,"message"=>"Data is empty."));
    }

    public function actionProjectManagement()
    {
        if (!isset($_GET['id'])) {
            $this->redirect("/user/view_profile");
        } else {
            $dataset = Dataset::model()->findByPk($_GET['id']);
            if (!$dataset) {
                $this->redirect("/user/view_profile");
            }

            if ($dataset->submitter_id != Yii::app()->user->id) {
                Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                $this->redirect("/user/view_profile");
            }

            $dps = DatasetProject::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

            $this->render('projectManagement', array('model' => $dataset,'dps'=>$dps));
        }
    }

    public function actionLinkManagement()
    {
        if (!isset($_GET['id'])) {
            $this->redirect("/user/view_profile");
        } else {
            $dataset = Dataset::model()->findByPk($_GET['id']);
            if (!$dataset) {
                $this->redirect("/user/view_profile");
            }

            if ($dataset->submitter_id != Yii::app()->user->id) {
                Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                $this->redirect("/user/view_profile");
            }

            $links = Link::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

            $link_database = Yii::app()->db->createCommand()
                    ->select("prefix")
                    ->from("prefix")
                    ->order("prefix asc")
                    ->group("prefix")
                    ->queryAll();

            $this->render('linkManagement', array('model' => $dataset,'links'=>$links,'link_database' => $link_database));
        }
    }

    public function actionExLinkManagement()
    {
        if (!isset($_GET['id'])) {
            $this->redirect("/user/view_profile");
        } else {
            $dataset = Dataset::model()->findByPk($_GET['id']);
            if (!$dataset) {
                $this->redirect("/user/view_profile");
            }

            if ($dataset->submitter_id != Yii::app()->user->id) {
                Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                $this->redirect("/user/view_profile");
            }

            $exLinks = ExternalLink::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

            $this->render('exLinkManagement', array('model' => $dataset,'exLinks'=>$exLinks));
        }
    }

    public function actionRelatedDoiManagement()
    {
        if (!isset($_GET['id'])) {
            $this->redirect("/user/view_profile");
        } else {
            $dataset = Dataset::model()->findByPk($_GET['id']);
            if (!$dataset) {
                $this->redirect("/user/view_profile");
            }

            if ($dataset->submitter_id != Yii::app()->user->id) {
                Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                $this->redirect("/user/view_profile");
            }

            $relations = Relation::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'related_doi asc'));

            $this->render('relatedDoiManagement', array('model' => $dataset,'relations'=>$relations));
        }
    }

    /**
     * Sample page.
     */
    public function actionSampleManagement()
    {
        if (!isset($_GET['id'])) {
            $this->redirect($this->getRedirectUrl());
        }

        $dataset = $this->getDataset($_GET['id']);

        $this->isSubmitter($dataset);

        $rows = array();
        if ($_POST) {
            $rows = json_decode($_POST['rows']);

            if (isset($_POST['matches'])) {
                $matches = (array)json_decode($_POST['matches']);

                for ($i = 0, $n = count($rows[0]); $i < $n; $i++) {
                    if (isset($matches[$rows[0][$i]])) {
                        $rows[0][$i] = $matches[$rows[0][$i]];
                    }
                }
            }
        }

        $template = isset($_GET['template']) ? TemplateName::model()->findByPk($_GET['template']) : null;

        $units = Unit::model()->findAll(array('order'=>'name asc'));

        $sts = TemplateName::model()->findAll(array('order'=>'template_name asc'));

        $samples = $dataset->samples;

        $sampleIds = array();
        foreach ($samples as $sample) {
            $sampleIds[] = $sample->id;
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition("sample_id", $sampleIds);
        $sas = SampleAttribute::model()->findAll($criteria, array('order'=>'attribute_id asc'));

        $uniques = array();
        foreach ($sas as $key => $sa) {
            $unique = $sa->attribute_id . '-' . $sa->unit_id;
            if (in_array($unique, $uniques)) {
                unset($sas[$key]);
            } else {
                $uniques[] = $unique;
            }
        }

        //$species = Species::model()->findAll(array('order'=>'common_name asc'));
        //$attrs = Attribute::model()->findAll(array('order'=>'attribute_name asc'));

        $this->render('sampleManagement', array(
            'model' => $dataset,
            'template' => $template,
            'units' => $units,
            'samples' => $samples,
            'sas' => $sas,
            'sts' => $sts,
            'rows' => $rows,
            //'species' => $species,
            //'attrs' => $attrs,
        ));
    }

    public function actionGetAttributes()
    {
        if (isset($_GET['term'])) {
            $attributeName = trim($_GET['term']);
            if (strlen($attributeName) < 2) {
                return null;
            }

            /** @var Attribute[] $attributes */
            $attributes = Attribute::findAllSimilarByAttrName($attributeName);
            $data = array();
            foreach ($attributes as $attribute) {
                $data[] = array(
                    'id' => $attribute->id,
                    'label' => $attribute->attribute_name,
                    'value' => $attribute->attribute_name,
                );
            }

            Util::returnJSON($data);
        }

        return null;
    }

    /**
     * @throws Exception
     */
    public function actionValidateSamples()
    {
        if ($_POST) {
            $samples = CUploadedFile::getInstanceByName('samples');
            if($samples) {
                $rows = CsvHelper::parse($samples->getTempName(), $samples->getExtensionName());
                $lastRequired = 2;
                $matches = array();
                for ($j = 3, $k = count($rows[0]); $j < $k; $j++) {
                    if (!empty($rows[0][$j])) {
                        $match = strtolower(trim($rows[0][$j]));
                        $attr = Attribute::findByAttrName($match);
                        if (!$attr) {
                            $attr = Attribute::findSimilarByAttrName($match);
                        }

                        if ($attr && strtolower($attr->attribute_name) != $match) {
                            $matches[$match] = $attr->attribute_name;
                        }

                        $lastRequired = $j;
                    }
                }

                for ($i = 1, $n = count($rows); $i < $n; $i++) {
                    for ($j = 0; $j <= $lastRequired; $j++)
                        if (empty($rows[$i][$j])) {
                            $error = 'Row ' . ($i + 1) . ': ' . 'Column ' . ($j + 1) . ' cannot be blank.';
                            Util::returnJSON(array(
                                "success"=>false,
                                'message' => $error,
                            ));
                        }
                }

                Util::returnJSON(array(
                    "success"=>true,
                    'rows' => $rows,
                    'matches' => $matches ?: false,
                ));
            }
        }

        Util::returnJSON(array(
            "success"=>false,
            'message' => "Data is empty.",
        ));
    }

    /**
     * @throws CException
     */
    public function actionSaveSamples() {
        if(isset($_POST['dataset_id'])) {
            $transaction = Yii::app()->db->beginTransaction();

            $dataset = $this->getDataset($_POST['dataset_id']);

            $attrs = array();
            $newSampleAttrs = isset($_POST['sample_attrs']) && is_array($_POST['sample_attrs']) ? $_POST['sample_attrs'] : array();
            foreach ($newSampleAttrs as $i => $newSampleAttr) {
                if (!$newSampleAttr['attr_name']) {
                    Util::returnJSON(array(
                        "success"=>false,
                        "message"=> 'Col ' . ($i + 3) . ': ' . 'Attribute Name cannot be empty.',
                    ));
                }

                $attr = Attribute::findByAttrName($newSampleAttr['attr_name']);
                if (!$attr) {
                    if (strtolower($newSampleAttr['attr_name']) == 'description') {
                        $attr = new Attribute();
                        $attr->attribute_name = $newSampleAttr['attr_name'];
                        $attr->save();
                    } else {
                        Util::returnJSON(array(
                            "success"=>false,
                            "message"=> 'Col ' . ($i + 3) . ': ' . 'Attribute Name does\'nt exist. You can try an alternative attribute name or use "miscellaneous parameter" and include your own attribute name within the value, e.g. miscellaneous parameter=users-own-attribute-name:value-of-attribute.',
                        ));
                    }
                }

                $attrs[] = $attr;
            }

            /** @var Sample[] $samples */
            $samples = $dataset->samples;
            $newSamples = isset($_POST['samples']) && is_array($_POST['samples']) ? $_POST['samples'] : array();
            $needSamples = array();
            if ($newSamples) {
                foreach ($newSamples as $key => $newSample) {
                    if (!$newSample['id']) {
                        $sample = new Sample();
                        $ds = new DatasetSample;
                        $ds->dataset_id = $dataset->id;
                    } else {
                        $sample = Sample::model()->findByPk($newSample['id']);
                        $ds = DatasetSample::model()->findByAttributes(array('sample_id' => $newSample['id'], 'dataset_id' => $dataset->id));
                        if (!$sample || !$ds) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=>"Save Error."
                            ));
                        }

                        $needSamples[] = $newSample['id'];
                    }

                    $sample->loadByData($newSample);
                    if (!$sample->validate()) {
                        $transaction->rollback();
                        $error = current($sample->getErrors());
                        Util::returnJSON(array(
                            "success"=>false,
                            "message"=> 'Row ' . ($key + 1) . ': ' . current($error)
                        ));
                    }

                    $sample->save();
                    $ds->sample_id = $sample->id;
                    $ds->save();

                    $needSAttrs = array();
                    foreach ($attrs as $i => $attr) {
                        if (!$newSample['attr_values'][$i]) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=> 'Row ' . ($key + 1) . ': ' . 'Value for ' . $attr->attribute_name . ' cannot be blank.',
                            ));
                        }

                        $unitId = isset($_POST['sample_attrs'][$i]['unit_id']) && $_POST['sample_attrs'][$i]['unit_id']
                            ? $_POST['sample_attrs'][$i]['unit_id'] : null;
                        $sa = SampleAttribute::model()->findByAttributes(array(
                            'sample_id' => $sample->id,
                            'attribute_id' => $attr->id,
                            'unit_id' => $unitId
                        ));
                        if (!$sa) {
                            $sa = new SampleAttribute();
                            $sa->sample_id = $sample->id;
                            $sa->attribute_id = $attr->id;
                            $sa->unit_id = $unitId;
                        }

                        $sa->value = $newSample['attr_values'][$i];

                        if (!$sa->validate()) {
                            $transaction->rollback();
                            $error = current($sa->getErrors());
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=> 'Row ' . ($key + 1) . ', Col ' . ($i + 3) . ': ' . current($error)
                            ));
                        }

                        $sa->save();
                        $needSAttrs[] = $sa->id;
                    }

                    $sas = SampleAttribute::model()->findAllByAttributes(array('sample_id' => $sample->id));
                    foreach ($sas as $sa) {
                        if (!in_array($sa->id, $needSAttrs)) {
                            if (!$sa->delete()) {
                                $transaction->rollback();
                                Util::returnJSON(array(
                                    "success"=>false,
                                    "message"=>"Save Error."
                                ));
                            }
                        }
                    }
                }
            }

            foreach ($samples as $sample) {
                if (!in_array($sample->id, $needSamples)) {
                    try {
                        $sample->delete();
                    } catch (\Exception $e) {
                        $transaction->rollback();
                        Util::returnJSON(array(
                            "success"=>false,
                            "message"=>"Delete error: sample \"{$sample->id}\" already related to some File."
                        ));
                    }
                }
            }

            $transaction->commit();
            Util::returnJSON(array("success"=>true));
        }

        Util::returnJSON(array("success"=>false,"message"=>"Data is empty."));
    }

    public function actionCheckUnit() {
        if(isset($_GET['attr_name'])) {
            $attr = Attribute::model()->findByAttributes(array('attribute_name' => $_GET['attr_name']));

            if ($attr && $attr->allowed_units) {
                $unitIds = explode(',', $attr->allowed_units);
                Util::returnJSON(array(
                    "success" => true,
                    'unitId' => $unitIds[0]
                ));
            }
        }

        Util::returnJSON(array("success"=>false));
    }

    /**
     * End page.
     */
    public function actionEnd()
    {
        if (!isset($_GET['id'])) {
            $this->redirect("/user/view_profile");
        } else {
            $dataset = $this->getDataset($_GET['id']);
            $dataset->upload_status = 'AssigningFTPbox';
            if (isset($_GET['is_test']) && $_GET['is_test'] === '0'){
                $dataset->toReal();
            }
            $dataset->save(false);

            $this->isSubmitter($dataset);

            $this->render('end', array('model' => $dataset));
        }
    }

    public function actionPxInfoManagement()
    {
        if (!isset($_GET['id'])) {
            $this->redirect("/user/view_profile");
        } else {
            $dataset = Dataset::model()->findByPk($_GET['id']);
            if (!$dataset) {
                Yii::log('dataset id not found', 'debug');
                $this->redirect("/user/view_profile");
            }

            if ($dataset->submitter_id != Yii::app()->user->id) {
                Yii::log('not the owner', 'debug');
                Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                $this->redirect("/user/view_profile");
            }

            # load attributes
            $keywordsAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'PX_keywords'));
            $sppAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'PX_sample_processing_protocol'));
            $dppAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'PX_data_processing_protocol'));
            $expTypeAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'PX_experiment_type'));
            $instrumentAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'PX_instrument'));
            $quantificationAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'PX_quantification'));
            $modificationAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'PX_modification'));

            if (!$keywordsAttr or !$sppAttr or !$dppAttr or !$expTypeAttr or !$instrumentAttr or !$quantificationAttr or !$modificationAttr) {
                Yii::app()->user->setFlash('keyword', "Attr cannot be found.");
                Yii::log("Attr cannot be found.", 'debug');
                $this->redirect("/user/view_profile");
            }

            # create new pxForm for validation and store px info into pxForm
            $pxForm = new PxInfoForm;

            # load keywords
            $keywords = DatasetAttributes::model()->findByAttributes(array('dataset_id'=>$dataset->id, 'attribute_id'=>$keywordsAttr->id));
            if (!$keywords) {
                $keywords = new DatasetAttributes;
                $keywords->dataset_id = $dataset->id;
                $keywords->attribute_id = $keywordsAttr->id;
            }
            $pxForm->keywords = $keywords->value;

            # load sample processing protocol
            $criteria = new CDbCriteria;
            $criteria->join = 'LEFT JOIN sample s on (t.sample_id = s.id) LEFT JOIN dataset_sample ds on (ds.sample_id = s.id)';
            $criteria->addCondition('ds.dataset_id = '.$dataset->id);
            $criteria->addCondition('t.attribute_id = '.$sppAttr->id);
            $spp = SampleAttribute::model()->find($criteria);

            if ($spp) {
                # get one spp
                $pxForm->spp = $spp->value;
            }

            # load experiment first, if not create one
            $experiment = Experiment::model()->findByAttributes(array('dataset_id'=>$dataset->id));
            if (!$experiment) {
                #create new experiment
                $experiment = new Experiment;
                $experiment->experiment_type = 'proteomic';
                $experiment->experiment_name = 'PX "'.$dataset->title.'"';
                $experiment->dataset_id = $dataset->id;
                $experiment->save(false);
            }

            # load data processing protocol
            $dpp = ExpAttributes::model()->findByAttributes(array('exp_id'=>$experiment->id, 'attribute_id'=>$dppAttr->id));
            if (!$dpp) {
                $dpp = new ExpAttributes;
                $dpp->exp_id = $experiment->id;
                $dpp->attribute_id = $dppAttr->id;
            }
            $pxForm->dpp = $dpp->value;

            # load experiment type
            $expType = ExpAttributes::model()->findByAttributes(array('exp_id'=>$experiment->id, 'attribute_id'=>$expTypeAttr->id));
            if (!$expType) {
                # set default experiment type
                $expType = new ExpAttributes;
                $expType->exp_id = $experiment->id;
                $expType->attribute_id = $expTypeAttr->id;
                $expType->value = CJSON::encode(array());
            }
            $expTypeVal = CJSON::decode($expType->value);
            $pxForm->experimentType = $expTypeVal;
            if (isset($expTypeVal['Other'])) {
                $pxForm->exTypeOther = $expTypeVal['Other'];
            }

            # load instrument
            $instrument = ExpAttributes::model()->findByAttributes(array('exp_id'=>$experiment->id, 'attribute_id'=>$instrumentAttr->id));
            if (!$instrument) {
                # set default instrument
                $instrument = new ExpAttributes;
                $instrument->exp_id = $experiment->id;
                $instrument->attribute_id = $instrumentAttr->id;
                $instrument->value = CJSON::encode(array());
            }
            $insVal = CJSON::decode($instrument->value);
            $pxForm->instrument = $insVal;
            if (isset($insVal['Other'])) {
                $pxForm->instrumentOther = $insVal['Other'];
            }

            # load quantification
            $quantification = ExpAttributes::model()->findByAttributes(array('exp_id'=>$experiment->id, 'attribute_id'=>$quantificationAttr->id));
            if (!$quantification) {
                # set default quantification
                $quantification = new ExpAttributes;
                $quantification->exp_id = $experiment->id;
                $quantification->attribute_id = $quantificationAttr->id;
                $quantification->value = CJSON::encode(array());
            }
            $quanVal = CJSON::decode($quantification->value);
            $pxForm->quantification = $quanVal;
            if (isset($quanVal['Other'])) {
                $pxForm->quantificationOther = $quanVal['Other'];
            }

            # load modification
            $modification = ExpAttributes::model()->findByAttributes(array('exp_id'=>$experiment->id, 'attribute_id'=>$modificationAttr->id));
            if (!$modification) {
                # set default modificaiton
                $modification = new ExpAttributes;
                $modification->exp_id = $experiment->id;
                $modification->attribute_id = $modificationAttr->id;
                $modification->value = CJSON::encode(array());
            }
            $modiVal = CJSON::decode($modification->value);
            $pxForm->modification = $modiVal;
            if (isset($modiVal['Other'])) {
                $pxForm->modificationOther = $modiVal['Other'];
            }


            if (isset($_POST['PxInfoForm'])) {
                # default is save and quit, redirect to user view_profile page
                $isSubmit = false;
                if (isset($_POST['submit-btn'])) {
                    # if user click submit, then submit the dataset
                    $isSubmit = true;
                }

                # store px Info
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    $attrs = $_POST['PxInfoForm'];
                    $pxForm->attributes = $attrs;

                    if ($pxForm->validate()) {
                        #store keywords
                        $keywords->value = $attrs['keywords'];

                        #store dpp
                        $dpp->value = $attrs['dpp'];

                        if (isset($_POST['exType'])) {
                            #store exp type
                            $expTypeVal = $_POST['exType'];
                            if (isset($expTypeVal['Other'])) {
                                $expTypeVal['Other'] = ($attrs['exTypeOther'])? $attrs['exTypeOther'] : "";
                            }
                            $expType->value = CJSON::encode($expTypeVal);
                        }

                        if (isset($_POST['quantification'])) {
                            #store quantification
                            $quanVal = $_POST['quantification'];
                            if (isset($quanVal['Other'])) {
                                $quanVal['Other'] = ($attrs['quantificationOther'])? $attrs['quantificationOther'] : "";
                            }
                            $quantification->value = CJSON::encode($quanVal);
                        }

                        if (isset($_POST['instrument'])) {
                            #store instrument
                            $insVal = $_POST['instrument'];
                            if (isset($insVal['Other'])) {
                                $insVal['Other'] = ($attrs['instrumentOther'])? $attrs['instrumentOther'] : "";
                            }
                            $instrument->value = CJSON::encode($insVal);
                        }

                        if (isset($_POST['modification'])) {
                            #store modification
                            $modiVal = $_POST['modification'];
                            if (isset($modiVal['Other'])) {
                                $modiVal['Other'] = ($attrs['modificationOther'])? $attrs['modificationOther'] : "";
                            }
                            $modification->value = CJSON::encode($modiVal);
                        }

                        #store spp into each samples in the dataset
                        $samples = $dataset->allSamples;
                        $isSppsStored = $this->storeSpps($samples, $attrs['spp'], $sppAttr);

                        if ($isSppsStored and $keywords->save() and $dpp->save() and $expType->save() and $quantification->save() and $instrument->save() and $modification->save()) {
                            $transaction->commit();
                            if ($isSubmit) {
                                $this->redirect(array('/datasetSubmission/submit', 'id'=>$dataset->id));
                            }
                            $this->redirect('/user/view_profile');
                        }
                    }
                } catch (Exception $e) {
                    $message = $e->getMessage();
                    Yii::log(print_r($message, true), 'error');
                    $transaction->rollback();
                    $this->redirect('/');
                }
            }
            $this->render('pxInfoManagement', array('model' => $dataset, 'pxForm'=>$pxForm));
        }
    }

    /**
     * Deletes a particular Dataset.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $this->loadModel($id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax'])) {
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
            }
        } else {
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionDatasetAjaxDelete()
    {
        if (isset($_POST['dataset_id'])) {
            $dataset = Dataset::model()->findByPk($_POST['dataset_id']);

            if (!$dataset) {
                Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Dataset does not exist.")));
            }

            $dataset->is_deleted = 1;
            $dataset->modification_date = date('Y-m-d');
            if ($dataset->save(false)) {
                Util::returnJSON(array("success"=>true));
            }
        }
        Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Delete Error.")));
    }

    public function actionDatasetAjaxUndo()
    {
        if (isset($_POST['dataset_id'])) {
            $dataset = Dataset::model()->findByPk($_POST['dataset_id']);

            if (!$dataset) {
                Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Dataset does not exist.")));
            }

            $dataset->is_deleted = 0;
            $dataset->modification_date = date('Y-m-d');
            if ($dataset->save(false)) {
                Util::returnJSON(array("success"=>true));
            }
        }
        Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Undo Error.")));
    }

    private function storeSpps($samples, $value, $sppAttr)
    {
        $lastSA = SampleAttribute::model()->findByAttributes(array(), array('order'=>'id desc'));
        $lastId = $lastSA->id;
        foreach ($samples as $sample) {
            $spp = SampleAttribute::model()->findByAttributes(array('attribute_id' => $sppAttr->id, 'sample_id'=>$sample->id));
            if (!$spp) {
                $lastId = $lastId+1;
                $spp = new SampleAttribute;
                $spp->id = $lastId;
                $spp->attribute_id = $sppAttr->id;
                $spp->sample_id = $sample->id;
            }
            #store spp value
            $spp->value = $value;
            if (!$spp->save()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    private function loadModel($id)
    {
        $model=Dataset::model()->findByPk($id);
        if ($model===null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }

    protected function getDataset($id)
    {
        $dataset = Dataset::model()->findByPk($id);

        if (!$dataset) {
            $this->redirect("/datasetSubmission/create1");
        }

        return $dataset;
    }

    protected function isSubmitter(Dataset $dataset)
    {
        if ($dataset->submitter_id != Yii::app()->user->id) {
            Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
            $this->redirect("/user/view_profile");
        }

        return true;
    }

    protected function getRedirectUrl()
    {
        $isTest = isset($_GET['is_test']) && $_GET['is_test'] == '1' ? '/is_test/1' : '';

        return "/datasetSubmission/create1" . $isTest;
    }
}
