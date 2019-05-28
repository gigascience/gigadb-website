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
                'actions'=>array('upload','delete','create1','submit','updateSubmit', 'updateFile',
                    'datasetManagement','authorManagement','projectManagement','linkManagement','exLinkManagement',
                    'relatedDoiManagement','sampleManagement','PxInfoManagement','datasetAjaxDelete'),
                'users'=>array('@'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
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
        if (isset($_POST['userId'])) {
            $user = User::model()->findByPk(Yii::app()->user->id);


            $excelFile = CUploadedFile::getInstanceByName('xls');
            // print_r($excelFile);die;
            $excelTempFileName = $excelFile->tempName;

            // email fields: to, from, subject, and so on
            $from = Yii::app()->params['app_email_name']." <".Yii::app()->params['app_email'].">";
            $to = Yii::app()->params['adminEmail'];
            $subject = "New dataset uploaded by user ".$user->id." - ".$user->first_name.' '.$user->last_name;
            $receiveNewsletter = $user->newsletter ? 'Yes' : 'No';
            $message = <<<EO_MAIL

New dataset is uploaded by:
<br/>
<br/>
Id:  <b>{$user->id}</b>
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
<br/><br/>
EO_MAIL;

            $headers = "From: $from";

            /* prepare attachments */

            // boundary
            $semi_rand = md5(time());
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

            // headers for attachment
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
            // multipart boundary
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" ."Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
            $message .= "--{$mime_boundary}\n";
            $fp =    @fopen($excelTempFileName, "rb");
            $data =    @fread($fp, filesize($excelTempFileName));
            @fclose($fp);
            $data = chunk_split(base64_encode($data));
            // $newFileName = 'dataset_upload_'.$user->id.'.xls';
            $newFileName = $excelFile->name;
            $message .= "Content-Type: application/octet-stream; name=\"".$newFileName."\"\n" .
            "Content-Description: ".$newFileName."\n" ."Content-Disposition: attachment;\n" . " filename=\"".$newFileName."\"; size=".filesize($excelTempFileName).";\n" ."Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";

            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . Yii::app()->params['adminEmail'];

            $ok = @mail($to, $subject, $message, $headers, $returnpath);

            if ($ok) {
                $this->redirect('/datasetSubmission/upload/status/successful');
                return;
            } else {
                $this->redirect('/datasetSubmission/upload/status/failed');
                return;
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
            if ($dataset->upload_status == 'UserStartedIncomplete') {
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
        $dataset = new Dataset;
        $image = new Images;
        // set default types
        $dataset->types = array();

        if (isset($_POST['Dataset']) && isset($_POST['Images'])) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                #save dataset
                $dataset->submitter_id = Yii::app()->user->_id;
                $attrs = $_POST['Dataset'];
                $dataset->title = $attrs['title'];
                $dataset->description = $attrs['description'];
                $dataset->upload_status = "UserStartedIncomplete";
                $dataset->ftp_site = "''";

                // save dataset types
                if (isset($_POST['datasettypes'])) {
                    $dataset->types = $_POST['datasettypes'];
                }

                $lastDataset = Dataset::model()->find(array('order'=>'identifier desc'));
                $lastIdentifier = intval($lastDataset->identifier);

                if (!is_int($lastIdentifier)) {
                    $transaction->rollback();
                    $this->redirect('/');
                }

                $dataset->identifier = $lastIdentifier + 1;

                //TODO: replace below with Bytes-Unit library
                if ($_POST['Dataset']['union']=='B') {
                    $dataset->dataset_size=$_POST['Dataset']['dataset_size'];
                } elseif ($_POST['Dataset']['union']=='M') {
                    $dataset->dataset_size=$_POST['Dataset']['dataset_size']*1024*1024;
                } elseif ($_POST['Dataset']['union']=='G') {
                    $dataset->dataset_size=$_POST['Dataset']['dataset_size']*1024*1024*1024;
                } elseif ($_POST['Dataset']['union']=='T') {
                    $dataset->dataset_size=$_POST['Dataset']['dataset_size']*1024*1024*1024*1024;
                }

                #save image
                if (!$_POST['Images']['is_no_image']) {
                    $uploadedFile = CUploadedFile::getInstance($image, 'image_upload');
                    $fileName = "{$uploadedFile}";
                    $path = Yii::getPathOfAlias('webroot') ."/images/uploads/".$fileName;

                    $image->image_upload = $uploadedFile;
                    $image->url = $path;
                    $image->location = $fileName;
                    $image->tag = $_POST['Images']['tag'];
                    $image->license = $_POST['Images']['license'];
                    $image->photographer = $_POST['Images']['photographer'];
                    $image->source = $_POST['Images']['source'];
                } else {
                    $image->url="http://gigadb.org/images/data/cropped/no_image.png";
                    $image->location="no_image.jpg";
                    $image->tag="no image icon";
                    $image->license="Public domain";
                    $image->photographer="GigaDB";
                    $image->source="GigaDB";
                }

                if ($dataset->save() && $image->save()) {
                    $dataset->image_id = $image->id;
                    $dataset->save(false);

                    // semantic kewyords update, using remove all and re-create approach
                    if (isset($_POST['keywords'])) {
                        $attribute_service = Yii::app()->attributeService;
                        $attribute_service->replaceKeywordsForDatasetIdWithString($dataset->id, $_POST['keywords']);
                    }

                    if (isset($_POST['datasettypes'])) {
                        $types = DatasetType::storeDatasetTypes($dataset->id, $_POST['datasettypes']);
                        if (!$types) {
                            $transaction->rollback();
                            $this->redirect('/');
                        }
                    }
                    $transaction->commit();
                    $this->redirect(array('/datasetSubmission/authorManagement', 'id'=>$dataset->id));
                }
            } catch (Exception $e) {
                $message = $e->getMessage();
                Yii::log(print_r($message, true), 'error');
                $transaction->rollback();
                $this->redirect('/');
            }
        }

        $this->render('create1', array('model' => $dataset, 'image'=>$image));
    }

    public function actionDatasetManagement()
    {
        if (!isset($_GET['id'])) {
            $this->redirect("/user/view_profile");
        } else {
            $dataset = Dataset::model()->findByPk($_GET['id']);

            if (!$dataset) {
                $this->redirect("/user/view_profile");
            }

            // set dataset types
            $dataset->types = $dataset->typeIds;

            if ($dataset->submitter_id != Yii::app()->user->id) {
                Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                $this->redirect("/user/view_profile");
            }

            if (!$dataset->image) {
                $image = new Images;
            } else {
                $image = $dataset->image;
            }

            $is_new_image = $image->isNewRecord;

            if (isset($_POST['Dataset']) && isset($_POST['Images'])) {
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    $attrs = $_POST['Dataset'];
                    $dataset->title = $attrs['title'];
                    $dataset->description = $attrs['description'];
                    // save dataset types
                    if (isset($_POST['datasettypes'])) {
                        $dataset->types = $_POST['datasettypes'];
                    }


                    if ($_POST['Dataset']['union']=='B') {
                        $dataset->dataset_size=$_POST['Dataset']['dataset_size'];
                    } elseif ($_POST['Dataset']['union']=='M') {
                        $dataset->dataset_size=$_POST['Dataset']['dataset_size']*1024*1024;
                    } elseif ($_POST['Dataset']['union']=='G') {
                        $dataset->dataset_size=$_POST['Dataset']['dataset_size']*1024*1024*1024;
                    } elseif ($_POST['Dataset']['union']=='T') {
                        $dataset->dataset_size=$_POST['Dataset']['dataset_size']*1024*1024*1024*1024;
                    }

                    #save image
                    if (!$_POST['Images']['is_no_image']) {
                        $uploadedFile = CUploadedFile::getInstance($image, 'image_upload');
                        $fileName = "{$uploadedFile}";
                        $path = Yii::getPathOfAlias('webroot') ."/images/uploads/".$fileName;

                        $image->image_upload = $uploadedFile;
                        $image->url = $path;
                        $image->location = $fileName;
                        $image->tag = $_POST['Images']['tag'];
                        $image->license = $_POST['Images']['license'];
                        $image->photographer = $_POST['Images']['photographer'];
                        $image->source = $_POST['Images']['source'];
                    } else {
                        $image->url="http://gigadb.org/images/data/cropped/no_image.png";
                        $image->location="no_image.jpg";
                        $image->tag="no image icon";
                        $image->license="Public domain";
                        $image->photographer="GigaDB";
                        $image->source="GigaDB";
                    }

                    if ($dataset->save() && $image->save()) {
                        if (isset($_POST['keywords'])) {
                            $attribute_service = Yii::app()->attributeService;
                            $attribute_service->replaceKeywordsForDatasetIdWithString($dataset->id, $_POST['keywords']);
                        }

                        if ($is_new_image) {
                            $dataset->image_id = $image->id;
                            $dataset->save(false);
                        }

                        if (isset($_POST['datasettypes'])) {
                            $types = DatasetType::storeDatasetTypes($dataset->id, $_POST['datasettypes']);
                            if (!$types) {
                                $transaction->rollback();
                                $this->redirect('/');
                            }
                        }
                        $transaction->commit();
                        $this->redirect(array('/datasetSubmission/authorManagement', 'id'=>$dataset->id));
                    }
                } catch (Exception $e) {
                    $message = $e->getMessage();
                    Yii::log(print_r($message, true), 'error');
                    $transaction->rollback();
                    $this->redirect('/');
                }
            }

            $this->render('datasetManagement', array('model' => $dataset,'image'=>$image));
        }
    }

    public function actionAuthorManagement()
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

            $das = DatasetAuthor::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'rank asc'));

            $this->render('authorManagement', array('model' => $dataset,'das'=>$das));
        }
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

    public function actionSampleManagement()
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

            $dss = DatasetSample::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'sample_id asc'));

            $this->render('sampleManagement', array('model' => $dataset,'dss'=>$dss));
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
}
