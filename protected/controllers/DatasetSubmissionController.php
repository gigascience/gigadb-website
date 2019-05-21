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
                    'authorManagement', 'validateAuthor', 'addAuthors', 'saveAuthors',
                    'additionalManagement', 'saveAdditional',
                    'fundingManagement', 'validateFunding', 'saveFundings',
                    'projectManagement','linkManagement','exLinkManagement',
                    'relatedDoiManagement','sampleManagement', 'saveSamples', 'checkUnit', 'PxInfoManagement','datasetAjaxDelete'),
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
        if (isset($_POST['File'])) {
            $count = count($_POST['File']);
            //var_dump('count'.$count);
            for ($i = 0; $i < $count; $i++) {
                $id=$_POST['File'][$i]['id'];
                $model = File::model()->findByPk($id);
                if ($model === null) {
                    continue;
                }
                $model->attributes = $_POST['File'][$i];
                if ($model->date_stamp == "") {
                    $model->date_stamp = null;
                }
                // var_dump($model->description);
                if (!$model->save()) {
                    var_dump($_POST['File'][$i]);
                }
            }
        }

        if (!isset($_GET['id'])) {
            $this->redirect("/user/view_profile");
        } else {
            $dataset_id = $_GET['id'];
            $dataset = Dataset::model()->findByPk($dataset_id);
            if (!$dataset) {
                Yii::app()->user->setFlash('keyword', "Cannot find dataset");
                $this->redirect("/user/view_profile");
            }

            if ($dataset->submitter_id != Yii::app()->user->id) {
                Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                $this->redirect("/user/view_profile");
            }

            //change dataset status to Request
            $samples =  DatasetSample::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'sample_id asc'));

            $sampleLink = "";
            if ($samples != null) {
                $sampleLink .= "Samples:<br/>";
                foreach ($samples as $sample) {
                    $sampleLink = $sampleLink . Yii::app()->params['home_url'] . "/adminSample/view/id/" . $sample->sample_id . "<br/>";
                }
            }

            $isOld = 1;
            if ($dataset->upload_status == 'Incomplete') {
                $isOld = 0;
            }

            //change the upload status
            $fileLink = "";
            if (isset($_POST['file'])) {
                $fileLink .= 'Files:<br/>';
                $fileLink = $link = Yii::app()->params['home_url'] . "/datasetSubmission/updateFile/?id=" . $dataset_id;
                $dataset->upload_status = 'Pending';
                CurationLog::createlog($dataset->upload_status, $dataset->id);
            } else {
                $dataset->upload_status = 'Request';
                CurationLog::createlog($dataset->upload_status, $dataset->id);
            }

            if (!$dataset->save()) {
                Yii::app()->user->setFlash('keyword', "Submit failure" . $dataset_id);
                $this->redirect("/user/view_profile");
                return;
            }
        }

        $link = Yii::app()->params['home_url'] . "/adminDataset/update/id/" . $dataset_id;
        $linkFolder ="Link File Folder:<br/>";
        $linkFolder .= (Yii::app()->params['home_url'] . "/adminFile/linkFolder/?id=".$dataset_id);
        $user = User::model()->findByPk(Yii::app()->user->id);

        $from = Yii::app()->params['app_email_name'] . " <" . Yii::app()->params['app_email'] . ">";
        $ok1 = false;
        $ok2 = false;

        if (!$isOld) {
            $to = Yii::app()->params['adminEmail'];

            $subject = "New dataset " . $dataset_id . " submitted online by user " . $user->id . " - " . $user->first_name . ' ' . $user->last_name;
            $receiveNewsletter = $user->newsletter ? 'Yes' : 'No';
            $date = getdate();

            $message = <<<EO_MAIL

New dataset is submitted by:
<br/>
<br/>
User:  <b>{$user->id}</b>
<br/>
Email: <b>{$user->email}</b>
<br/>
First Name:  <b>{$user->first_name}</b>
<br/>
Last Name:  <b>{$user->last_name}</b>
<br/>
Affiliation:  <b>{$user->affiliation}</b>
<br/>
Receiving Newsletter:  <b>{$receiveNewsletter}</b>
<br/>
Submission ID: <b>$dataset_id</b><br/>
$link
<br/>
$sampleLink
    <br/>
$linkFolder
        <br/>

EO_MAIL;
            $headers = "Fcrrom: $from";

            /* prepare attachments */

            // boundary
            $semi_rand = md5(time());
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

            // headers for attachment
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
            // multipart boundary
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
            $message .= "--{$mime_boundary}\n";

            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . Yii::app()->params['adminEmail'];

            $ok1 = @mail($to, $subject, $message, $headers, $returnpath);

            //send email to user to

            $to = $user->email;

            $subject = "GigaDB submission \"" . $dataset->title . '"'.' ['.$dataset_id.']';
            $receiveNewsletter = $user->newsletter ? 'Yes' : 'No';
            $timestamp = $date['mday'] . "-" . $date['mon'] . "-" . $date['year'];
            $message = <<<EO_MAIL
Dear $user->first_name $user->last_name,<br/>

Thank you for submitting your dataset information to GigaDB.
Our curation team will contact you shortly regarding your
submission "$dataset->title".<br/>
<br/>
In the meantime, please contact us at <a href="mailto:database@gigasciencejournal.com">database@gigasciencejournal.com</a> with any questions.<br/>
<br/>
Best regards,<br/>
<br/>
The GigaDB team<br/>
<br/>
Submission date: $timestamp
<br/>
EO_MAIL;

            $headers = "From: $from";

            /* prepare attachments */

            // boundary
            $semi_rand = md5(time());
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

            // headers for attachment
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
            // multipart boundary
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
            $message .= "--{$mime_boundary}\n";

            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . Yii::app()->params['adminEmail'];

            $ok2 = @mail($to, $subject, $message, $headers, $returnpath);
        } else {
            $to = Yii::app()->params['adminEmail'];

            $subject = "Dataset " . $dataset_id . " updated online by user " . $user->id . " - " . $user->first_name . ' ' . $user->last_name;
            $receiveNewsletter = $user->newsletter ? 'Yes' : 'No';
            $date = getdate();
            $adminFileLink = Yii::app()->params['home_url'] . "/adminFile/update1/?id=" .$dataset_id;
            $message = <<<EO_MAIL
Dataset is updated by:
<br/>
<br/>
User:  <b>{$user->id}</b>
<br/>
Email: <b>{$user->email}</b>
<br/>
First Name:  <b>{$user->first_name}</b>
<br/>
Last Name:  <b>{$user->last_name}</b>
<br/>
Affiliation:  <b>{$user->affiliation}</b>
<br/>
Receiving Newsletter:  <b>{$receiveNewsletter}</b>
<br/>
Submission ID: <b>$dataset_id</b><br/>
$link
<br/>
$adminFileLink
    <br/>
EO_MAIL;

            $headers = "From: $from";

            /* prepare attachments */

            // boundary
            $semi_rand = md5(time());
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

            // headers for attachment
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
            // multipart boundary
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
            $message .= "--{$mime_boundary}\n";

            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . Yii::app()->params['adminEmail'];

            $ok1 = @mail($to, $subject, $message, $headers, $returnpath);

            //send email to user to

            $to = $user->email;

            //  $subject = "GigaDB update \"" . $dataset->title . '"';
            $subject = "GigaDB submission \"" . $dataset->title . '"'.' ['.$dataset_id.']';
            $receiveNewsletter = $user->newsletter ? 'Yes' : 'No';
            $timestamp = $date['mday'] . "-" . $date['mon'] . "-" . $date['year'];
            $message = <<<EO_MAIL
Dear $user->first_name $user->last_name,<br/>

Thank you for updating your dataset information to GigaDB.
Our curation team will contact you shortly regarding your
updates "$dataset->title".<br/>
<br/>
In the meantime, please contact us at <a href="mailto:database@gigasciencejournal.com">database@gigasciencejournal.com</a> with any questions.<br/>
<br/>
Best regards,<br/>
<br/>
The GigaDB team<br/>
<br/>
Submission date: $timestamp
<br/>
EO_MAIL;

            $headers = "From: $from";

            /* prepare attachments */

            // boundary
            $semi_rand = md5(time());
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

            // headers for attachment
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
            // multipart boundary
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
            $message .= "--{$mime_boundary}\n";

            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . Yii::app()->params['adminEmail'];

            $ok2 = @mail($to, $subject, $message, $headers, $returnpath);
        }

        if ($ok1 && $ok2) {
            $uploadedDatasets = Dataset::model()->findAllByAttributes(array('submitter_id' => Yii::app()->user->id));
            $this->render("upload", array('study' => $dataset_id, 'uploadedDatasets' => $uploadedDatasets));
        } else {
            //add something
            $uploadedDatasets = Dataset::model()->findAllByAttributes(array('submitter_id' => Yii::app()->user->id));
            $this->render("upload", array('study' => $dataset_id, 'uploadedDatasets' => $uploadedDatasets));
        }
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
            $dataset = $this->getDataset($_GET['id']);
            $image = $dataset->image ?: new Images();

            if (isset($_POST['Images'])) {
                $image->setIsNoImage(!!$_POST['Images']['is_no_image']);
            } else {
                $image->setIsNoImage($image->location == 'no_image.jpg');
            }

            $this->isSubmitter($dataset);
        } else {
            $dataset = new Dataset();
            $image = new Images();
        }

        if (isset($_POST['Dataset']) && isset($_POST['Images'])) {
            $newKeywords = isset($_POST['keywords']) ? $_POST['keywords'] : '';
            $newTypes = isset($_POST['datasettypes']) ? $_POST['datasettypes'] : array();

            $image->loadByData($_POST['Images']);
            $dataset->loadByData($_POST['Dataset']);
            $dataset->types = $newTypes;
            $dataset->keywords = explode(',', $newKeywords);
            if ($dataset->validate() && $image->validate()) {
                $image->save();
                $dataset->image_id = $image->id;
                $dataset->save();

                $dataset->updateKeywords($newKeywords);
                $dataset->updateTypes($newTypes);

                $image->saveImageFile();

                if (isset($_POST['redirect_url']) && $_POST['redirect_url']) {
                    $this->redirect($_POST['redirect_url']);
                }
                $this->redirect(array('/datasetSubmission/create1', 'id'=>$dataset->id));
            }
        }


        $this->render('create1', array('model' => $dataset, 'image'=>$image));
    }

    public function actionAuthorManagement()
    {
        if (!isset($_GET['id'])) {
            $this->redirect("/user/view_profile");
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
                Util::returnJSON(array(
                    "success"=>true,
                    'author' => $author->asArray(),
                ));
            }

            Util::returnJSON(array("success"=>false,"message"=>current($author->getErrors())));
        }
    }

    /**
     * @throws CException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionAddAuthors() {
        $authors = CUploadedFile::getInstanceByName('authors');
        if($authors) {
            if ($authors->getType() != CsvHelper::TYPE_CSV && $authors->getType() != CsvHelper::TYPE_TSV) {
                Util::returnJSON(array("success"=>false,"message"=>"File has wrong extension."));
            }

            $delimiter = $authors->getType() == CsvHelper::TYPE_CSV ? ';' : "\t";
            $rows = CsvHelper::getArrayByFileName($authors->getTempName(), $delimiter);
            if (!$rows) {
                Util::returnJSON(array("success"=>false,"message"=>"File is empty."));
            }

            $authors = array();
            foreach ($rows as $num => $row) {
                $author = new Author();
                $author->loadByCsvRow($row);
                if($author->validate()) {
                    $authors[] = $author->asArray();
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
                foreach ($_POST['authors'] as $num => $row) {
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
                        $dataset->addAuthor($author, $row['order']);
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
            $this->redirect("/user/view_profile");
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

            $exLinks = ExternalLink::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

            $this->render('additionalManagement', array(
                'model' => $dataset,
                'links' => $links,
                'link_database' => $link_database,
                'relations' => $relations,
                'dps' => $dps,
                'exLinks' => $exLinks,
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
                        $link->link = $newLink['link'];

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

                $dataset->setAdditionalInformationKey(AIHelper::PUBLIC_LINKS, true);
                $dataset->save(false);
            } else {
                $dataset->setAdditionalInformationKey(AIHelper::PUBLIC_LINKS, false);
                $dataset->save(false);
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

                $dataset->setAdditionalInformationKey(AIHelper::RELATED_DOI, true);
                $dataset->save(false);
            } else {
                $dataset->setAdditionalInformationKey(AIHelper::RELATED_DOI, false);
                $dataset->save(false);
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

                $dataset->setAdditionalInformationKey(AIHelper::PROJECTS, true);
                $dataset->save(false);
            } else {
                $dataset->setAdditionalInformationKey(AIHelper::PROJECTS, false);
                $dataset->save(false);
            }

            foreach ($projects as $project) {
                if (!in_array($project->id, $needProjects)) {
                    $project->delete();
                }
            }

            $exLinks = ExternalLink::model()->findAllByAttributes(array('dataset_id'=>$dataset->id));
            $newExLinks = isset($_POST['exLinks']) && is_array($_POST['exLinks']) ? $_POST['exLinks'] : array();
            $needExLinks = array();
            if ($newExLinks) {
                $hasManuscripts = false;
                $hasProtocols = false;
                $has3d = false;
                $hasCodes = false;
                $hasSources = false;
                foreach ($newExLinks as $newExLink) {
                    if (!$newExLink['id']) {
                        $exLink = new ExternalLink;
                        $exLink->loadByData($newExLink);

                        if (!$exLink->validate()) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=>"Save Error."
                            ));
                        }

                        $exLink->save();
                    } else {
                        $needExLinks[] = $newExLink['id'];
                    }

                    switch ($newExLink['externalLinkType']) {
                        case AIHelper::MANUSCRIPTS:
                            $hasManuscripts = true;
                            break;
                        case AIHelper::PROTOCOLS:
                            $hasProtocols = true;
                            break;
                        case AIHelper::_3D_IMAGES:
                            $has3d = true;
                            break;
                        case AIHelper::CODES:
                            $hasCodes = true;
                            break;
                        default:
                            $hasSources = true;
                            break;
                    }
                }

                $dataset->setAdditionalInformationKey(AIHelper::MANUSCRIPTS, $hasManuscripts);
                $dataset->setAdditionalInformationKey(AIHelper::PROTOCOLS, $hasProtocols);
                $dataset->setAdditionalInformationKey(AIHelper::_3D_IMAGES, $has3d);
                $dataset->setAdditionalInformationKey(AIHelper::CODES, $hasCodes);
                $dataset->setAdditionalInformationKey(AIHelper::SOURCES, $hasSources);
                $dataset->save(false);
            } else {
                $dataset->setAdditionalInformationKey(AIHelper::MANUSCRIPTS, false);
                $dataset->setAdditionalInformationKey(AIHelper::PROTOCOLS, false);
                $dataset->setAdditionalInformationKey(AIHelper::_3D_IMAGES, false);
                $dataset->setAdditionalInformationKey(AIHelper::CODES, false);
                $dataset->setAdditionalInformationKey(AIHelper::SOURCES, false);
                $dataset->save(false);
            }

            foreach ($exLinks as $exLink) {
                if (!in_array($exLink->id, $needExLinks)) {
                    $exLink->delete();
                }
            }

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
            $this->redirect("/user/view_profile");
        } else {
            $dataset = $this->getDataset($_GET['id']);

            $this->isSubmitter($dataset);

            $funders = Funder::model()->findAllByAttributes(array(), array('order'=>'primary_name_display asc'));
            $fundings = Funding::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

            $this->render('fundingManagement', array(
                'model' => $dataset,
                'funders' => $funders,
                'fundings' => $fundings,
            ));
        }
    }

    public function actionValidateFunding() {
        if ($_POST) {
            $funding = new Funding();
            $funding->loadByData($_POST);

            if($funding->validate()) {
                Util::returnJSON( array(
                    "success" => true,
                    'funding' => $funding->asArray(),
                ));
            }
            Util::returnJSON(array(
                "success"=>false,
                "message"=>current($funding->getErrors())
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

            $fundings = $dataset->fundings;

            $newFundings = isset($_POST['fundings']) && is_array($_POST['fundings']) ? $_POST['fundings'] : array();
            $needFundings = array();
            if ($newFundings) {
                foreach ($newFundings as $newFunding) {
                    if (!$newFunding['id']) {
                        $funding = new Funding();
                        $funding->loadByData($newFunding);
                        if (!$funding->validate()) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=>"Save Error."
                            ));
                        }

                        $funding->save();
                    } else {
                        $needFundings[] = $newFunding['id'];
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
            $this->redirect("/user/view_profile");
        }

        $dataset = $this->getDataset($_GET['id']);

        $this->isSubmitter($dataset);

        $error = '';
        $rows = array();
        if ($_POST) {
            $samples = CUploadedFile::getInstanceByName('samples');
            if($samples) {

                if ($samples->getType() != CsvHelper::TYPE_CSV && $samples->getType() != CsvHelper::TYPE_TSV) {
                    $error = "File has wrong extension.";
                } else {
                    $delimiter = $samples->getType() == CsvHelper::TYPE_CSV ? ';' : "\t";
                    $rows = CsvHelper::getArrayByFileName($samples->getTempName(), $delimiter);
                    if (!$rows) {
                        $error = "File is empty.";
                    } else {
                        $lastRequired = 2;
                        for ($j = 3, $k = count($rows[0]); $j < $k; $j++) {
                            if (!empty($rows[0][$j])) {
                                $lastRequired = $j;
                            }
                        }

                        for ($i = 1, $n = count($rows); $i < $n; $i++) {
                            for ($j = 0; $j <= $lastRequired; $j++)
                                if (empty($rows[$i][$j])) {
                                    $error = 'Row ' . ($i + 1) . ': ' . 'Column ' . ($j + 1) . ' cannot be blank.';
                                    break 2;
                                }
                        }
                    }
                }
            }
        }

        $template = isset($_GET['template']) ? SampleTemplate::model()->findByPk($_GET['template']) : null;

        $units = Unit::model()->findAll(array('order'=>'name asc'));

        $sts = SampleTemplate::model()->findAll(array('order'=>'name asc'));

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

        $species = Species::model()->findAll(array('order'=>'common_name asc'));
        $attrs = Attribute::model()->findAll(array('order'=>'attribute_name asc'));

        $this->render('sampleManagement', array(
            'model' => $dataset,
            'template' => $template,
            'units' => $units,
            'samples' => $samples,
            'sas' => $sas,
            'sts' => $sts,
            'error' => $error,
            'rows' => $rows,
            'species' => $species,
            'attrs' => $attrs,
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
                $attr = Attribute::model()->findByAttributes(array('attribute_name' => $newSampleAttr['attr_name']));
                if (!$attr) {
                    $attr = new Attribute;
                    $attr->attribute_name = $newSampleAttr['attr_name'];
                    if (!$attr->validate()) {
                        $transaction->rollback();
                        $error = current($attr->getErrors());
                        Util::returnJSON(array(
                            "success"=>false,
                            "message"=> 'Col ' . ($i + 4) . ': ' . current($error)
                        ));
                    }
                    $attr->save();
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

                    $needAttrs = array();
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
                                "message"=> 'Row ' . ($key + 1) . ', Col ' . ($i + 4) . ': ' . current($error)
                            ));
                        }

                        $sa->save();
                        $needAttrs[] = $attr->id;
                    }

                    $sas = SampleAttribute::model()->findAllByAttributes(array('sample_id' => $sample->id));
                    foreach ($sas as $sa) {
                        if (!in_array($sa->attribute_id, $needAttrs)) {
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
                    if (!$sample->delete()) {
                        $transaction->rollback();
                        Util::returnJSON(array(
                            "success"=>false,
                            "message"=>"Save Error."
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
                Util::returnJSON(array(
                    "success"=>true,
                    'unitId' => $attr->allowed_units
                ));
            }
        }

        Util::returnJSON(array("success"=>false));
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

            if ($dataset->delete()) {
                Util::returnJSON(array("success"=>true));
            }
        }
        Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Delete Error.")));
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
            $this->redirect("/user/view_profile");
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
}
