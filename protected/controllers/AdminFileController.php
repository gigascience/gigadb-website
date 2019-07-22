<?php

class AdminFileController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout='//layouts/column2';

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
            array('allow', // admin only
                    'actions'=>array('linkFolder','admin','delete','index','view','create','update','update1', 'editAttr', 'uploadAttr'),
                    'roles'=>array('admin'),
            ),
                            array('allow',
                                    'actions' => array('create1', 'getFiles', 'updateFiles', 'uploadFiles'),
                                    'users' => array('@'),
                            ),
                            array('allow',  // allow all users
                                'actions' => array('downloadCount'),
                                'users'=>array('*'),
                            ),
            array('deny',  // deny all users
                    'users'=>array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view',array(
            'model'=>$this->loadModel($id),
        ));
    }

    public function getFilesInfo($conn_id, $ftp_dir, $ftp, &$model, &$count) {

        ftp_pasv($conn_id, true);
        $buff = ftp_rawlist($conn_id, $ftp_dir);
        if(!$buff) {
            return false;
        }
        $date = new DateTime("2050-01-01");
        $date = $date->format("Y-m-d");
        foreach (array_values($buff) as $value) {
            $info = preg_split("/\s+/", $value);
            $name = $info[8];
            $new_dir = $ftp_dir . "/" . $name;
            if ($this->is_dir($conn_id, $new_dir)) {
                $new_ftp = $ftp . "/" . $name;
                if (!$this->getFilesInfo($conn_id, $new_dir, $new_ftp, $model, $count))
                    return false;
            } else {
                $count++;
                //var_dump($info);
                $size = $info[4];
                $stamp = date("F d Y", ftp_mdtm($conn_id, $name));
                // var_dump($name);
                $file = new File;
                $file->dataset_id = $model->dataset_id;
                $file->name = $name;
                $file->size = $size;
                $file->location = $ftp . "/" . $name;
                $file->code = "None";
                $file->date_stamp = $date;
                $extension = "";
                $format = "";
                $this->getFileExtension($file->name, $extension, $format);
                $file->extension = $extension;
                $fileformat = FileFormat::model()->findByAttributes(array('name' => $format));
                if ($fileformat != null)
                    $file->format_id = $fileformat->id;
                $file->type_id = 1;
                $file->date_stamp = $stamp;
                if (!$file->save()) {
                    $model->addError('error', "Files are not saved correctly");
                    return false;
                    //how to 
//                    var_dump($file->name);
                } else {
                    $this->setAutoFileAttributes($file);
                }
            }
        }
        return true;
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new File;
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['File'])) {
            $_POST['File'] = array_filter($_POST['File']);
            $model->attributes = $_POST['File'];

            // save file attributes from location
            $model->setSizeValue();
            if ($model->save()) {
                if(isset($_POST['File']['sample_name'])) {
                    $fs = new FileSample;
                    $sample = Sample::model()->findbyAttributes(array('name'=>$_POST['File']['sample_name']));
                    $fs->sample_id = $sample->id;
                    $fs->file_id = $model->id;
                    $fs->save(false);
                }
                if ($model->location) {
                    $extension = $format = null;
                    $this->getFileExtension($model->name, $extension, $format);
                   // $this->setAutoFileAttributes($model);
                }
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate1()
    {
        if (!isset($_GET['id']))
            throw new CHttpException(404, 'The requested page does not exist.');

        $dataset_id = $_GET['id'];
        $defaultFileSortColumn = 'dataset.name';
        $defaultFileSortOrder = CSort::SORT_DESC;
        if (isset($_GET['filesort'])) {
            // use new sort and save to cookie
            // check if desc or not
            $order = substr($_GET['filesort'], strlen($_GET['filesort']) - 5, 5);
            $columnName = $defaultFileSortColumn;
            if ($order == '.desc') {
                $columnName = substr($_GET['filesort'], 0, strlen($_GET['filesort']) - 5);
                $order = 1;
            } else {
                $columnName = $_GET['filesort'];
                $order = 0;
            }
            $defaultFileSortColumn = $columnName;
            $defaultFileSortOrder = $order;
            Yii::app()->request->cookies['file_sort_column'] = new CHttpCookie('file_sort_column', $columnName);
            Yii::app()->request->cookies['file_sort_order'] = new CHttpCookie('file_sort_order', $order);
        } else {
            // use old sort if exists
            if (isset(Yii::app()->request->cookies['file_sort_column'])) {
                $cookie = Yii::app()->request->cookies['file_sort_column']->value;
                $defaultFileSortColumn = $cookie;
            }
            if (isset(Yii::app()->request->cookies['file_sort_order'])) {
                $cookie = Yii::app()->request->cookies['file_sort_order']->value;
                $defaultFileSortOrder = $cookie;
            }
        }

        $fsort = new MySort;
        $fsort->attributes = array('*');
        $fsort->attributes[] = "dataset.identifier";
        $fsort->defaultOrder = array($defaultFileSortColumn => $defaultFileSortOrder);

        $fpagination = new CPagination;
        $fpagination->pageVar = 'files_page';
        $files = new CActiveDataProvider('File', array(
            'criteria' => array(
                'condition' => "dataset_id = " . $dataset_id,
                'join' => 'JOIN dataset ON dataset.id = t.dataset_id',
                'order' => 'id asc'
            ),
            'sort' => $fsort,
            'pagination' => $fpagination
        ));
        $updateAll = 0;
        if (isset($_POST['File'])) {
            if (isset($_POST['files'])) {
                $updateAll = 1;
                $count = count($_POST['File']);
                for ($i = 0; $i < $count; $i++) {
                    if ($updateAll == 0 && !isset($_POST[$i])) {
                        continue;
                    }
                    $model = $this->loadModel($_POST['File'][$i]['id']);
                    $model->attributes = $_POST['File'][$i];
                    if ($model->date_stamp == "")
                        $model->date_stamp = NULL;
                    //$model->dataset_id = $_POST['File']['dataset_id'];
                    if (!$model->save()) {
                        var_dump($_POST['File'][$i]);
                    }
                }
            }
        }


        $fileModels = File::model()->findAll("dataset_id=" . $dataset_id);
        $identifier = Dataset::model()->findByAttributes(array('id' => $dataset_id))->identifier;

        $model = new File;

        $samples_data = array();
        //add none and All , Multiple
        $samples_data['none']='none';
        $samples_data['All']='All';
        $samples_data['Multiple']='Multiple';

        $this->render('update1', array('files' => $files,
                    'fileModels' => $fileModels,
                    'identifier' => $identifier,
                    'model' => $model,
                    'samples_data'=>$samples_data
                    ));
    }

    public function updateFile($f, $data) {

        $f->name = $data[0];
        $f->code = $data[1];
        $type = $f->type;
        $format = $f->format;
        if($data[2] != $type->name) {
            $nt = FileType::model()->find(array('condition'=>'lower(name) = :name',
                'params'=>array(':name'=>strtolower($data[2]))));

            if($nt) {
                $f->type_id = $nt->id;
            }
            else {
                $nt = FileType::model()->findByAttributes(array('name'=>'Other'));
                if(!$nt) {
                    $nt = new FileType;
                    $nt->name = 'Other';
                    $nt->save();
                }
                $f->type_id = $nt->id;
            }
        }

        if($data[3] != $format->name) {
            $nf = FileFormat::model()->find(array('condition'=>'lower(name) =:name', 
                'params'=>array(':name'=>strtolower($data[3]))));
            if($nf) {
                $f->format_id = $nf->id;
            }
            else {
                $nf = FileFormat::model()->findByAttributes(array('name'=> 'UNKNOWN'));
                if(!$nf) {
                    $nf->name = 'UNKNOWN';
                    $nf->save();
                }
                $f->format_id = $nf->id;
            }
        }
        $f->description = $data[4];
        if(!$f->save())
            Yii::log(print_r($f->getErrors(), true), 'debug');
        return;
    }

    public function actionUploadAttr()
    {
        if (!Yii::app()->request->isPostRequest)
            throw new CHttpException(404, 'The requested page does not exist.');
        $id = $_GET['id'];
        $dataset = Dataset::model()->findByAttributes(array('identifier' => $id));
        $filelist = File::model()->findAll(array('condition' => 'dataset_id =:id', 'order' => 'id asc', 'params' => array(':id' => $dataset->id)));

        $file = CUploadedFile::getInstanceByName('file_info');
        if ($file) {
            $name = $file->name;
            $path = '/tmp/' . $name;
            $file->saveAs($path);
            // tab character char(9)
            $datas = Utils::readCsv($path, chr(9));
            if ($datas) {
                foreach ($filelist as $idx => $f) {
                    if (isset($datas[$idx])) {
                        $data = $datas[$idx];
                        $this->updateFile($f, $data);
                    } else
                        break;
                }
            }
        }
        $this->redirect(array('/adminFile/update1', 'id' => $dataset->id));
    }

    public function getFileExtension($file_name, &$extension, &$format)
    {
        //extensions is <extension, format> array
        $extensions = array('agp' => 'AGP', 'bam' => 'BAM', 'chain' => 'CHAIN', 'contig' => 'CONTIG',
            'xls' => 'EXCEL', 'xlsx' => 'EXCEL', 'chr' => 'FASTA', 'fasta' => 'FASTA', 'fa' => 'FASTA',
            'seq' => 'FASTA', 'cds' => 'FASTA', 'pep' => 'FASTA', 'scanffold' => 'FASTA', 'scafseq' => 'FASTA',
            'fq' => 'FASTQ', 'fastq' => 'FASTQ', 'gff' => 'GFF', 'ipr' => 'IPR', 'kegg' => 'KEGG', 'maf' => 'MAF',
            'md5' => 'MD5', 'net' => 'NET', 'pdf' => 'PDF', 'png' => 'PNG', 'qmap' => 'QMAP', 'rpkm' => 'RPKM',
            'sam' => 'SAM', 'tar' => 'TAR', 'readme' => 'TEXT', 'doc' => 'TEXT', 'text' => 'TEXT', 'txt' => 'TEXT', 'vcf' => 'VCF',
            'wego' => 'WEGO', 'wig' => 'WIG', 'iprscan' => 'IPR', 'stat' => 'UNKNOWN', 'qual' => 'QUAL'
        );


        $extensionArray = explode(".", $file_name);

        $extension = "";
        $length = count($extensionArray);
        if ($length == 1) {
            $extension = 'UNKNOWN';
        }
        // the first one shouldn't be extension
        foreach ($extensionArray as $temp) {
            $temp = trim($temp);
            // all extension are lower case in map, so when camparing,
            // I need to change temp to lowercase
            // if readme then the extension before it is removed
            if ($temp == "readme") {
                $extension = "";
                continue;
            }
            if (array_key_exists(strtolower($temp), $extensions)) {
                if ($extension != "" && $temp == "txt")
                    continue;
                $extension = $temp;
            }
        }
        if ($extension == "") {
            $index = $length - 1;
            while (in_array(strtolower($extensionArray[$index]), $extensions
            ))
                $index = $index - 1;
            $extension = $extensionArray[$index];
        }
        if (array_key_exists($extension, $extensions))
            $format = $extensions[$extension];
        else
            $format = 'UNKNOWN';
        return;
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);
        $model->sample_name = ($model->sample)? $model->sample->id : '';

        $attribute = new FileAttributes;
        $attribute->file_id = $model->id;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        if (isset($_POST['edit_attr'])) {
            $args = $_POST['FileAttributes'];
            $fa = FileAttributes::model()->findByPk($args['id']);
            if($fa) {
                $fa->attribute_id = $args['attribute_id'];
                $fa->value = $args['value'];
                if($args['unit_id'])
                    $fa->unit_id = $args['unit_id'];

                if($fa->validate()) {
                    if($fa->save())
                        $this->redirect(array('update','id'=>$model->id));
                    else
                        Yii::log('save attr failed', 'debug');
                } else
                    Yii::log(print_r($fa->getErrors(), true), 'debug');
            }
        }
        elseif(isset($_POST['submit_attr'])) {
            $attrs = $_POST['FileAttributes'];
            $attribute->attribute_id = $attrs['attribute_id'];
            $attribute->value = $attrs['value'];
            if($attrs['unit_id'])
                $attribute->unit_id = $attrs['unit_id'];

            if($attribute->validate()) {
                $attribute->save();
                $this->redirect(array('update', 'id' => $model->id));
            }
        } elseif (isset($_POST['File'])) {
            $model->attributes = $_POST['File'];

            $model->setSizeValue();
            if ($model->validate()) {
                $model->save();

                if(isset($_POST['File']['sample_name']) && !empty($_POST['File']['sample_name'])) {
                    $fs = $model->fileSamples;
                    if(!isset($fs[0])) {
                        $fs = new FileSample;
                    } else {
                        $fs = $fs[0];
                    }
                    $sample = Sample::model()->findbyAttributes(array('name'=>$_POST['File']['sample_name']));
                    $fs->sample_id = $sample->id;
                    $fs->file_id = $model->id;
                    if( $fs->sample_id !='None'&& $fs->sample_id !="" )
                    {
                    $fs->save(false);
                    }
                    $temp=$fs->find('file_id=:file_id', array(':file_id'=>$model->id));
                    if($fs->sample_id =="" && $temp != null)
                    {
                      $temp->delete();
                    }
                }
                /*
                // save file attributes from location
                if (isset($model->location)) {
                    $this->setAutoFileAttributes($model, true);
                }*/

                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('update', array(
            'model' => $model,
            'attribute' => $attribute
        ));
    }

    public function actionEditAttr()
    {
        if (!Yii::app()->request->isPostRequest)
            throw new CHttpException(404, "The requested page does not exist.");

        if (isset($_POST['id'])) {
            $attribute = FileAttributes::model()->findByPk($_POST['id']);
            if ($attribute) {
                $data = $this->renderPartial('_attr', array('attribute' => $attribute), true, false);
                echo CJSON::encode(array('success' => true, 'data' => $data));
                Yii::app()->end();
            }
        }
        echo CJSON::encode(array('success' => false));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
   public function actionDelete($id)
    {
        
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
           $file = File::model()->findByPk($id);
           
          // $file->fileSamples->delete();
          foreach ($file->fileAttributes as $fileattributes) {
              print_r($fileattributes);
              $fileattributes->delete();
              
          }
         foreach ($file->fileSamples as $filesample) {
              print_r($filesample);
              $filesample->delete();
              
          }
      
           $file->delete();
           
            

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
           
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
      
    }


    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider('File');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new File('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['File']))
            $model->attributes = $_GET['File'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Link files through a folder 
     */
    public function actionLinkFolder()
    {
        $model = new Folder;
        $buff = array();
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_GET['id'])) {

            $model->dataset_id = $_GET['id'];
        }
        if (isset($_POST['Folder'])) {
            $model->attributes = $_POST['Folder'];

            if (!$model->validate()) {
                $this->render('linkFolder', array(
                    'model' => $model, 'buff' => $buff
                ));
                return;
            }
            $ftp = $model->folder_name;
            $ftps = explode("/", $ftp, 2);
            $ftp_server = $ftps[0];
            $ftp_dir = isset($ftps[1]) ? "/" . $ftps[1] : "/";

//            if($ftp_dir=="")
//                $ftp_dir=
            $ftp_user_name = $_POST['Folder']['username'];
            $ftp_user_pass = $_POST['Folder']['password'];

            // set up basic connection
            $conn_id = @ftp_connect($ftp_server);
            if ($conn_id === false) {
                $model->addError('error', 'Unable to connect to ' . $ftp_server);
                $this->render('linkFolder', array(
                    'model' => $model, 'buff' => $buff
                ));
                return;
            }
            // login with username and password
            $login_result = @ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
            if ($login_result !== true) {
                $model->addError('error', "Couldn't connect as $ftp_user_name\n");
                $this->render('linkFolder', array(
                    'model' => $model, 'buff' => $buff
                ));
                return;
            }
            $file_count = 0;
            $transaction = Yii::app()->db->beginTransaction();

            $ok = $this->getFilesInfo($conn_id, $ftp_dir, $ftp, $model, $file_count);
            ftp_close($conn_id);

            //email 
            if ($ok && $file_count > 0) {
                $user = User::model()->findByPk(Yii::app()->user->id);

                $from = Yii::app()->params['app_email_name'] . " <" . Yii::app()->params['app_email'] . ">";
                $dataset = Dataset::model()->findByattributes(array('id' => $model->dataset_id));
                $dataset->upload_status = 'Curation';
                
                if (!$dataset->save()) {
                    $transaction->rollback();
                    $model->addError('error', "Failure: Dataset status is not updated successfully.");
                    $this->render('linkFolder', array(
                        'model' => $model, 'buff' => $buff
                    ));
                    return;
                }
                $transaction->commit();

                $submitter = $dataset->submitter;
                $to = $dataset->submitter->email;

                // $subject = "Files are added to Your dataset: " . $model->dataset_id;
                //$subject= 
                $subject = "GigaDB submission \"" . $dataset->title . '"' . ' [' . $dataset->id . ']';
                $receiveNewsletter = $user->newsletter ? 'Yes' : 'No';
                $link = Yii::app()->params['home_url'] . "/adminFile/create1/id/" . $model->dataset_id;
                $message = <<<EO_MAIL
Dear $submitter->first_name,<br/><br/>

$file_count Files have been added to your GigaDB submission "$dataset->title".<br/><br/>

Please complete the submission by clicking the
    link below and adding the sample(s) from which each file was generated, 
        along with the File type, File format and a description of the file.
            Once all file information has been added, click the “Complete submission” button
                to let the curator know that you have completed the required information.<br/><br/>

Please review the files here: $link<br/><br/>

Kind regards<br/>
GigaDB team
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
                $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
                $message .= "--{$mime_boundary}\n";

                $message .= "--{$mime_boundary}--";
                $returnpath = "-f" . Yii::app()->params['adminEmail'];

                $ok = @mail($to, $subject, $message, $headers, $returnpath);

                //send to database@gigasciencejournal.com
                $from = Yii::app()->params['app_email_name'] . " <" . Yii::app()->params['app_email'] . ">";

                $to = Yii::app()->params['app_email'];
                $subject = "Files are added to  dataset: " . $model->dataset_id;
                $receiveNewsletter = $user->newsletter ? 'Yes' : 'No';
                $link = Yii::app()->params['home_url'] . "/adminDataset/update/id/" . $model->dataset_id;
                $message = <<<EO_MAIL
Dear GigaDB,<br/><br/>

Files have been updated by:<br/>
User: $user->id<br/>
Email: $user->email<br/>
First Name: $user->first_name<br/>
Last Name: $user->last_name<br/>
Affiliation: $user->affiliation<br/>
Submission ID: $model->dataset_id<br/>
$link<br/><br/>                    
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

                $ok = @mail($to, $subject, $message, $headers, $returnpath);

                $this->redirect("/adminFile/update1/?id=" . $model->dataset_id);
                return;
            } else {
                $transaction->rollback();
                $model->addError('error', "Files are not saved!\n");
            }
        }

        $this->render('linkFolder', array(
            'model' => $model, 'buff' => $buff
        ));
    }

    public function actionCreate1()
    {
        if (isset($_GET['id'])) {
            $dataset_id = $_GET['id'];
        } else {
            Yii::app()->user->setFlash('error', "Can't retrieve the files");
            $this->redirect("/user/view_profile");
        }

        $dataset = Dataset::model()->findByAttributes(array('id' => $dataset_id));

        $defaultFileSortColumn = 'dataset.name';
        $defaultFileSortOrder = CSort::SORT_DESC;
        if (isset($_GET['filesort'])) {
            // use new sort and save to cookie
            // check if desc or not
            $order = substr($_GET['filesort'], strlen($_GET['filesort']) - 5, 5);
            $columnName = $defaultFileSortColumn;
            if ($order == '.desc') {
                $columnName = substr($_GET['filesort'], 0, strlen($_GET['filesort']) - 5);
                $order = 1;
            } else {
                $columnName = $_GET['filesort'];
                $order = 0;
            }
            $defaultFileSortColumn = $columnName;
            $defaultFileSortOrder = $order;
            Yii::app()->request->cookies['file_sort_column'] = new CHttpCookie('file_sort_column', $columnName);
            Yii::app()->request->cookies['file_sort_order'] = new CHttpCookie('file_sort_order', $order);
        } else {
            // use old sort if exists
            if (isset(Yii::app()->request->cookies['file_sort_column'])) {
                $cookie = Yii::app()->request->cookies['file_sort_column']->value;
                $defaultFileSortColumn = $cookie;
            }
            if (isset(Yii::app()->request->cookies['file_sort_order'])) {
                $cookie = Yii::app()->request->cookies['file_sort_order']->value;
                $defaultFileSortOrder = $cookie;
            }
        }

        $fsort = new MySort;
        $fsort->attributes = array('*');
        $fsort->attributes[] = "dataset.identifier";
        $fsort->defaultOrder = array($defaultFileSortColumn => $defaultFileSortOrder);

        $fpagination = new CPagination;
        $fpagination->pageVar = 'files_page';
        $files = new CActiveDataProvider('File', array(
            'criteria' => array(
                'condition' => "dataset_id = " . $dataset_id,
                'join' => 'JOIN dataset ON dataset.id = t.dataset_id',
                'order' => 't.id'
            ),
            'sort' => $fsort,
            'pagination' => $fpagination
        ));

        if (isset($_POST['File'])) {
            $page = $_POST['page'];
            $pageCount = $_POST['pageCount'];
            if ($page < $pageCount) {
                $page++;
                $files->getPagination()->setCurrentPage($page);
            }
        }

        $identifier = $dataset->identifier;
        $action = 'create1';

        $this->render($action, array('files' => $files, 'identifier' => $identifier,'model'=>$dataset));
    }

    public function is_dir($conn_id, $dir)
    {
        // get current directory
        $original_directory = ftp_pwd($conn_id);
        // test if you can change directory to $dir
        // suppress errors in case $dir is not a file or not a directory
        if (@ftp_chdir($conn_id, $dir)) {
            // If it is a directory, then change the directory back to the original directory
            ftp_chdir($conn_id, $original_directory);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function actionGetFiles()
    {
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['dataset_id'])) {
            $dataset = $this->getDataset($_POST['dataset_id']);

            $files = FtpHelper::getListOfFilesWithSizes($_POST['username'], $_POST['password']);

            $html = '';
            $i = 0;
            foreach ($files as $fileName => $fileSize) {
                $file = File::model()->findByAttributes(array('dataset_id'=> $_POST['dataset_id'], 'name' => $fileName));
                if (!$file) {
                    $file = new File();
                    $file->name = $fileName;
                    $file->extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $file->size = $fileSize;
                    $file->dataset_id = $_POST['dataset_id'];
                    $file->id = 'fake' . time();
                    $file->prepareFormatId();
                }

                $html .= $this->renderPartial('_file_tr', array(
                    'model' => $dataset,
                    'file' => $file,
                    'i' => $i,
                ), true);
                $i++;
            }

            Util::returnJSON(array(
                "success"=>true,
                "html"=> $html
            ));
        }

        Util::returnJSON(array("success"=>false,"message"=>"Data is empty."));
    }

    /**
     * @throws \yii\web\BadRequestHttpException
     * @throws Exception
     */
    public function actionUpdateFiles()
    {
        if (isset($_POST['File']) && isset($_POST['dataset_id'])) {
            $dataset = $this->getDataset($_POST['dataset_id']);

            if (isset($_POST['file_id']) && $_POST['file_id']) {
                foreach ($_POST['File'] as $key => $file) {
                    if ($file['id'] == $_POST['file_id']) {
                        $errors = array();

                        $model = File::model()->findByPk($file['id']);
                        if (!$model) {
                            $model = new File();
                            $model->dataset_id = $dataset->id;
                        }

                        $model->attributes = $file;
                        if ($model->date_stamp == "") {
                            $model->date_stamp = NULL;
                        }

                        if (!$model->validate()) {
                            $errors[$key] = $model->getErrors();

                            Util::returnJSON(array("success"=>false,"errors"=>$errors));
                        } else {
                            $model->save();

                            Util::returnJSON(array("success"=>true, 'file_id' => $model->id));
                        }
                    }
                }
            }

            $errors = File::updateAllByData($_POST['File'], $dataset);
            if ($errors) {
                Util::returnJSON(array("success"=>false,"errors"=>$errors));
            } else {
                Util::returnJSON(array("success"=>true));
            }
        }

        Util::returnJSON(array("success"=>false,"message"=>"Data is empty."));
    }

    /**
     * @throws Exception
     */
    public function actionUploadFiles()
    {
        if ($_POST) {
            $files = CUploadedFile::getInstanceByName('files');
            if($files) {
                $rows = CsvHelper::parse($files->getTempName(), $files->getExtensionName());

                foreach ($rows as $key => $row) {
                    $number = $key + 1;
                    if (!isset($row[0]) || !$row[0]) {
                        Util::returnJSON(array(
                            "success"=>false,
                            "message"=>"Row $number: File Name cannot be empty."
                        ));
                    }
                    if (!isset($row[1]) || !$row[1]) {
                        Util::returnJSON(array(
                            "success"=>false,
                            "message"=>"Row $number: Data Type cannot be empty."
                        ));
                    } else {
                        $type = FileType::model()->findByAttributes(array('name' => $row[1]));
                        if (!$type) {
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=>"Row $number: Data Type is invalid."
                            ));
                        }

                        $rows[$key][1] = $type->id;
                    }
                    if (!isset($row[2]) || !$row[2]) {
                        Util::returnJSON(array(
                            "success"=>false,
                            "message"=>"Row $number: Description cannot be empty."
                        ));
                    }
                }

                Util::returnJSON(array(
                    "success"=>true,
                    'rows' => $rows
                ));
            }
        }
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=File::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='file-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionDownloadCount() {
        if(isset($_POST['file_href'])) {
            $file = File::model()->findByAttributes(array('location'=>$_POST['file_href']));
            $file->download_count += 1;
            if(!$file->saveAttributes(array('download_count'=>$file->download_count))) {
                Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Add Count Error.")));
            }
            Util::returnJSON(array("success"=>true));
        }
    }
    
    /**
     * Save file attributes from File
     * @param File $file
     * @param boolean $update
     */
    private function setAutoFileAttributes($file, $update = false)
    {
        // We download the file first in /tmp/ folder
        if (!file_exists(ReadFile::TEMP_FOLDER . $file->name)) {
            ReadFile::downloadRemoteFile($file->location, $file->name);
        }
        
        // from update page, we delete all auto attributes and make them again
        if ($update) {
            $criteria = new CDbCriteria;
            $criteria->addCondition('file_id = ' . $file->id);
            $fileAttributes = FileAttributes::model()->findAll($criteria);
            foreach ($fileAttributes as $fileAttribute) {
                if (strpos(Attribute::AUTO_ATTRIBUTE, $fileAttribute->attribute->structured_comment_name)) {
                    $fileAttribute->delete();
                }
            }
        }
        
        $fileAttribute = new FileAttributes;
        $fileAttribute->file_id = $file->id;
        /*$name = strpos($file->name, '.') ? explode('.', $file->name) : $file->name;
        $name = is_array($name) ? $name[0] : $name;*/

        $extension = str_replace('.', '', $file->extension);
        switch ($extension) {
            case 'fasta';
            case 'fa';
            case 'fastq';
            case 'seq';
            case 'bam';
            case 'sam';
            case 'cram';
                // Launche the python script to get the result
                list($aminoAcids, $nucleotides) = ReadFile::readPythonFile($file->name);
                
                // Number of Amino Acids
                $numberAminoAcids = clone $fileAttribute;
                $attribute = Attribute::model()->findByAttributes(array('structured_comment_name' => 'num_amino_acids'));
                $numberAminoAcids->attribute_id = $attribute->id;
                $numberAminoAcids->value = $aminoAcids;
                $numberAminoAcids->save();

                // Number of nucleotides
                $numberNucleotides = clone $fileAttribute;
                $attribute = Attribute::model()->findByAttributes(array('structured_comment_name' => 'num_nucleotides'));
                $numberNucleotides->value = $nucleotides;
                $numberNucleotides->attribute_id = $attribute->id;
                $numberNucleotides->save();
                break;

            case 'txt';
            case 'text';
            case 'doc';
            case 'docx';
            case 'rtf';
            case 'odt';
            case 'wpd';
            case 'lwd';
            case 'readme';
            case 'README';
            case 'pdf';
            case '';
                
                $result = null;
                if (in_array($extension, array('txt', 'text', 'readme', 'README', '', 'wpd', 'lwd'))) {
                    $result = ReadFile::readTextFile($file->name);
                }
                if (in_array($extension, array('docx', 'odt'))) {
                    $result = ReadFile::readDocxOdtFile($file->name);
                }
                if ($extension == 'doc') {
                    $result = ReadFile::readDocFile($file->name);
                }
                if ($extension == 'rtf') {
                    $result = ReadFile::readRtfFile($file->name);
                }
                if ($extension == 'pdf') {
                    $result = ReadFile::readPdfFile($file->name);
                }
                $numberOfLines = $numberOfWords = 0;
                $numberOfWords = str_word_count($result);
                $numberOfLines = substr_count($result, "\n");

                // Number of words
                $numberWords = clone $fileAttribute;
                $attribute = Attribute::model()->findByAttributes(array('structured_comment_name' => 'num_words'));
                $numberWords->attribute_id = $attribute->id;
                $numberWords->value = $numberOfWords;
                $numberWords->save();

                // Number of lines
                $numberLines = clone $fileAttribute;
                $attribute = Attribute::model()->findByAttributes(array('structured_comment_name' => 'num_lines'));
                $numberLines->attribute_id = $attribute->id;
                $numberLines->value = $numberOfLines;
                $numberLines->save();
                break;

            case 'csv';
            case 'tsv';
            case 'xls';
            case 'xlsx';
            case 'tab';
            case 'sdc';
            case 'ods';
            case 'gff';
            case 'gff3';
            case 'snp';
            case 'vcf';
            case 'ipr';
                $rows = $columns = 0;
                
                if ($extension == 'xls') {
                    list($rows, $columns) = ReadFile::readXlsFile($file->name);
                }
                if ($extension == 'ods') {
                    list($rows, $columns) = ReadFile::readOdsFile($file->name);
                }
                if ($extension == 'xlsx') {
                    list($rows, $columns) = ReadFile::readXlsxFile($file->name);
                }
                if (in_array($extension, array('csv', 'tsv', 'vcf', 'gff'))) {
                    list($rows, $columns) = ReadFile::readTableFile($file->name, $extension);
                }
                // Number of rows
                $numberRows = clone $fileAttribute;
                $attribute = Attribute::model()->findByAttributes(array('structured_comment_name' => 'num_rows'));
                $numberRows->attribute_id = $attribute->id;
                $numberRows->value = $rows;
                $numberRows->save();

                // Number of columns
                $numberColumns = clone $fileAttribute;
                $attribute = Attribute::model()->findByAttributes(array('structured_comment_name' => 'num_columns'));
                $numberColumns->value = $columns;
                $numberColumns->attribute_id = $attribute->id;
                $numberColumns->save();
                break;
        }
    }

    /**
     * @param $id
     * @return array|Dataset|mixed|null
     * @throws \yii\web\BadRequestHttpException
     */
    protected function getDataset($id)
    {
        $dataset = Dataset::model()->findByPk($id);

        if (!$dataset) {
            throw new \yii\web\BadRequestHttpException('Dataset ID is invalid.');
        }

        if ($dataset->submitter_id != Yii::app()->user->id) {
            throw new \yii\web\BadRequestHttpException('Access denied.');
        }

        return $dataset;
    }
}
