<?php

use League\Flysystem\AdapterInterface;

/**
 * Routing, aggregating and composing logic for administrative actions (CRUD) to a Dataset object
 *
 * Create, Read/List (for admin purpose),  Update, Delete
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class AdminDatasetController extends Controller
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
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                  'actions'=>array('create','admin','update','private', 'removeImage','clearImageFile','mint','checkDOIExist', 'assignFTPBox','sendInstructions','saveInstructions','mockup','moveFiles'),
                  'roles'=>array('admin'),
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
        return array(
            'assignFTPBox'=>'application.controllers.adminDataset.AssignFTPBoxAction',
            'sendInstructions'=>'application.controllers.adminDataset.SendInstructionsAction',
            'saveInstructions'=>'application.controllers.adminDataset.SaveInstructionsAction',
            'mockup'=>'application.controllers.adminDataset.MockupAction',
            'moveFiles'=>'application.controllers.adminDataset.MoveFilesAction',
        );
    }

	/**
	 * Manage creation of new dataset object from a form
	 *
	 */
	public function actionCreate()
    {
        $dataset = new Dataset; // needed for the CActiveForm field for dataset model
        $dataset->image = new Image; // needed for the CActiveForm field for image model

        $datasetPageSettings = new DatasetPageSettings($dataset);

        if (!empty($_POST['Dataset']) && !empty($_POST['Image'])) {
        	Yii::log("Processing submitted data", 'info');
        	$dataset_post_data = $_POST['Dataset'];
        	if (isset($dataset_post_data['publication_date']) && $dataset_post_data['publication_date'] == "" ) {
        		$dataset_post_data['publication_date'] = null;
        	}
        	if (isset($dataset_post_data['modification_date']) && $dataset_post_data['modification_date'] == "" ) {
        		$dataset_post_data['modification_date'] = null;
        	}
        	if (isset($dataset_post_data['fairnuse']) && $dataset_post_data['fairnuse'] == "" ) {
        		$dataset_post_data['fairnuse'] = null;
        	}

            $dataset->setAttributes($dataset_post_data, true);
            if( !$dataset->validate() ) {
                Yii::log("Dataset instance is not valid", 'info');
            }

            $datasetImage = CUploadedFile::getInstanceByName('datasetImage');

            if($datasetImage && !empty($_POST['Image'])) { //User has uploaded an image
                Yii::log("action Create: image form data exists and a file has been uploaded, so creating a new image object","warning");
                $dataset->image->attributes = $_POST['Image'];
                Yii::log($datasetImage->getTempName(), "warning");
                if( ! $dataset->image->write(Yii::$app->cloudStore, $dataset->getUuid(), $datasetImage) ) {
                    Yii::log("Error writing file to storage for dataset ".$dataset->identifier, "error");
                }
            } else { //we use the generic image
                $dataset->image = Image::model()->findByPk(Image::GENERIC_IMAGE_ID);
                Yii::log("action Create: Using generic image","warning");
            }


           	if ( !$dataset->hasErrors() && $dataset->image->validate('update') ) {
            	Yii::log("Image data associated to new dataset is valid", "info");
                // save image
                if( $dataset->image->save() ) {
	                $dataset->image_id = $dataset->image->id;
                }
                else {
                    Yii::log(print_r($dataset->image->getErrors(), true), "error");
                }

                // save dataset
                if( $dataset->save() ) {
                    // link datatypes
                    if (isset($_POST['datasettypes'])) {
                        $datasettypes = $_POST['datasettypes'];
                        foreach (array_keys($datasettypes) as $id) {
                            $newDatasetTypeRelationship = new DatasetType;
                            $newDatasetTypeRelationship->dataset_id = $dataset->id;
                            $newDatasetTypeRelationship->type_id = $id;
                            $newDatasetTypeRelationship->save();
                        }
                    }

                    Yii::app()->user->setFlash('saveSuccess', 'saveSuccess');
                    if ($dataset->upload_status=='AuthorReview') {
                        $this->redirect('/adminDataset/private/identifier/'.$dataset->identifier);
                    }
                    $this->redirect(array('/dataset/'.$dataset->identifier));
                }
            }

            Yii::log(print_r($dataset->getErrors(), true), 'error');

        }

        $this->layout = 'new_main';
        $this->render('create', array('model'=>$dataset,'datasetPageSettings' => $datasetPageSettings)) ;
    }

    /**
     * List all datasets with call to actions
     */
    public function actionAdmin()
    {

        $criteria=new CDbCriteria(array(
            'order'=>'identifier asc',
        ));

        $dataProvider=new CActiveDataProvider('Dataset', array(
            'criteria'=>$criteria,
        ));

        $model=new Dataset('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Dataset'])) {
            $model->setAttributes($_GET['Dataset']);
        }

        $this->layout = 'new_main';
        $this->loadBaBbqPolyfills = true;
        $this->render('admin', array(
            'model'=>$model,
            'dataProvider'=>$model->search(),
        ));
    }

    /**
     * Updates a Dataset object from web form.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        // setting DatasetUpload, the busisness object for File uploading
        $webClient = new \GuzzleHttp\Client();
        $fileUploadSrv = new FileUploadService([
            "tokenSrv" => new TokenService([
                                  'jwtTTL' => 3600,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => new UserDAO(),
                                  'dt' => new DateTime(),
                                ]),
            "webClient" => $webClient,
            "requesterEmail" => Yii::app()->user->email,
            "identifier"=> $model->identifier,
            "dataset" => new DatasetDAO(["identifier" => $model->identifier]),
            "dryRunMode"=>false,
            ]);
        $datasetUpload = new DatasetUpload(
            $fileUploadSrv->dataset,
            $fileUploadSrv,
            Yii::$app->params['dataset_upload']
        );

        $datasetPageSettings = new DatasetPageSettings($model);

        $dataProvider = new CActiveDataProvider('CurationLog', array(
            'criteria' => array(
                'condition' => "dataset_id=$id",
                'order' => 'id DESC',
            ),
        ));
        if (isset($_POST['Dataset'])) {
            if (isset($_POST['Dataset']['upload_status']) && $_POST['Dataset']['upload_status'] != $model->upload_status) {
                $statusIsSet = false;
                switch( $_POST['Dataset']['upload_status'] )
                {
                    case "Submitted":
                        $contentToSend = $datasetUpload->renderNotificationEmailBody("Submitted");
                        $statusIsSet = $datasetUpload->setStatusToSubmitted($contentToSend);
                        break;
                    case "DataPending":
                        $contentToSend = $datasetUpload->renderNotificationEmailBody("DataPending");
                        $statusIsSet = $datasetUpload->setStatusToDataPending(
                            $contentToSend, $model->submitter->email
                        );
                        break;
                    default:
                        $statusIsSet = true;
                }
                if ($statusIsSet) {
                    CurationLog::createlog($_POST['Dataset']['upload_status'], $id);
                }

            }
            if ($_POST['Dataset']['curator_id'] != $model->curator_id) {
                if ($_POST['Dataset']['curator_id'] != "") {
                    $User1 = User::model()-> find('id=:id', array(':id'=>Yii::app()->user->id));
                    $username1 = $User1->first_name." ".$User1->last_name;
                    $User = User::model()-> find('id=:id', array(':id'=>$_POST['Dataset']['curator_id']));
                    $username = $User->first_name." ".$User->last_name;
                    CurationLog::createlog_assign_curator($id, $username1, $username);
                    $model->curator_id = $_POST['Dataset']['curator_id'];
                } else {
                    $model->curator_id = null;
                }
            }

            if ($_POST['Dataset']['manuscript_id']) {
                $model->manuscript_id = $_POST['Dataset']['manuscript_id'];
            } else {
                $model->manuscript_id = "";
            }

            $datasetAttr = $_POST['Dataset'];
            Yii::log("**** new attributes: ".print_r($datasetAttr,true),"warning");
            $model->setAttributes($datasetAttr, true);

            if ($model->upload_status == 'Published') {
                $files = $model->files;
                if (strpos($model->ftp_site, "10.5524") == false) {
                    $model->ftp_site="ftp://climb.genomics.cn/pub/10.5524/100001_101000/" . $model->identifier;

                    if (count($files) > 0) {
                        foreach ($files as $file) {
                            $origin_location = $file->location;
                            $new_location = "";
                            $location_array = explode("/", $origin_location);
                            $count = count($location_array);
                            if ($count == 1) {
                                $new_location = "ftp://climb.genomics.cn/pub/10.5524/100001_101000/" .
                                        $model->identifier . "/" . $location_array[0];
                            } elseif ($count >= 2) {
                                $new_location = "ftp://climb.genomics.cn/pub/10.5524/100001_101000/" .
                                        $model->identifier . "/" . $location_array[$count - 2] . "/" . $location_array[$count - 1];
                            }
                            $file->location = $new_location;
                            $file->date_stamp = date("Y-m-d H:i:s");
                            if (!$file->save()) {
                                return false;
                            }
                        }
                    }
                }
            }


            if ($model->publication_date == "") {
                $model->publication_date = null;
            }
            if ($model->modification_date == "") {
                $model->modification_date = null;
            }
            if ($model->fairnuse == "") {
                $model->fairnuse = null;
            }

            // Image information

            $datasetImage = CUploadedFile::getInstanceByName('datasetImage');

            if (0 === $model->image->id && !empty($_POST['Image']) && $datasetImage) {
                $model->image =  new Image();
                Yii::log("Generic image was associated with dataset, so creating a new Image object","warning");
            }
            else {
                $model->image->scenario = 'update';
                Yii::log("Dataset is already associated with image :". $model->image->id,"warning");
            }
            $model->image->attributes = $_POST['Image'];

            if($datasetImage) {
                if( ! $model->image->write(Yii::$app->cloudStore, $model->getUuid(), $datasetImage) ) {
                    Yii::log("Error writing file to storage for dataset ".$model->identifier, "error");
                }
                // save image
                if( $model->image->save() ) {
                    $model->image_id = $model->image->id;
                }
                else {
                    Yii::log(print_r($model->image->getErrors(), true), 'error');
                }
            }

            if ($model->save()) {
                if (isset($_POST['datasettypes'])) {
                    $datasettypes = $_POST['datasettypes'];
                }

                $datasetTypeMaps = DatasetType::model()->findAllByAttributes(array('dataset_id' => $id));

                for ($i = 0; $i < count($datasetTypeMaps); ++$i) {
                    $datasetTypeMap = $datasetTypeMaps[$i];
                    if ((isset($datasettypes) && !in_array($datasetTypeMap->type_id, array_keys($datasettypes), true)) || !isset($datasettypes)) {
                        $datasetTypeMap->delete();
                    }
                }

                if (isset($datasettypes)) {
                    foreach (array_keys($datasettypes) as $datasetTypeId) {
                        $currDatasetTypeMap = DatasetType::model()->findByAttributes(array('dataset_id' => $model->id, 'type_id' => $datasetTypeId));
                        if (!$currDatasetTypeMap) {
                            $newDatasetTypeRelationship = new DatasetType;
                            $newDatasetTypeRelationship->dataset_id = $model->id;
                            $newDatasetTypeRelationship->type_id = $datasetTypeId;
                            $newDatasetTypeRelationship->save();
                        }
                    }
                }

                // semantic kewyords update, using remove all and re-create approach
                if (isset($_POST['keywords'])) {
                    $attribute_service = Yii::app()->attributeService;
                    $attribute_service->replaceKeywordsForDatasetIdWithString($id, $_POST['keywords']);
                }


                // retrieve existing redirect
                $criteria = new CDbCriteria(array('order'=>'id ASC'));
                $urlToRedirectAttr = Attributes::model()->findByAttributes(array('attribute_name'=>'urltoredirect'));
                $urlToRedirectDatasetAttribute = datasetAttributes::model()->findByAttributes(array('dataset_id'=>$id,'attribute_id'=>$urlToRedirectAttr->id), $criteria);

                // saving url to redirect as a dataset attribute
                if (isset($urlToRedirectDatasetAttribute) || isset($_POST['urltoredirect'])) {


                    // update with value from form if value has changed.
                    if (isset($urlToRedirectDatasetAttribute) && $_POST['urltoredirect'] != $urlToRedirectDatasetAttribute->value) {
                        $urlToRedirectDatasetAttribute->value = $_POST['urltoredirect'];
                        $urlToRedirectDatasetAttribute->save();
                    }

                    // create a new dataset attribute if there isn't one
                    elseif (isset($_POST['urltoredirect'])) {
                        $urlToRedirectDatasetAttribute = new DatasetAttributes();
                        $urlToRedirectDatasetAttribute->attribute_id = $urlToRedirectAttr->id;
                        $urlToRedirectDatasetAttribute->dataset_id = $id;
                        $urlToRedirectDatasetAttribute->value = $_POST['urltoredirect'];
                        $urlToRedirectDatasetAttribute->save();
                    }
                }

                Yii::app()->user->setFlash('updateSuccess', 'Updated successfully!');
                switch ($datasetPageSettings->getPageType()) {
                    case "draft":
                        $this->redirect('/adminDataset/admin/');
                        break;
                    case "public":
                        $this->redirect('/dataset/' . $model->identifier);
                        break;
                    case "hidden":
                        $this->redirect(array('/adminDataset/update/id/' . $model->id));
                        break;
                }

            } else {
                Yii::app()->user->setFlash('updateError', 'Fail to update!');
                Yii::log(print_r($model->getErrors(), true), 'error');
            }
        }

        $this->layout = 'new_main';
        $this->loadBaBbqPolyfills = true;
        $this->render('update', array(
            'model' => $model,
            'datasetPageSettings' => $datasetPageSettings,
            'curationlog'=>$dataProvider,
            'dataset_id'=>$id,
        ));
    }


    /**
     * One-off access to a private dataset
     *
     */
    public function actionPrivate()
    {
        $id = $_GET['identifier'];
        $model= Dataset::model()->find("identifier=?", array($id));
        $datasetPageSettings = new DatasetPageSettings($model);
        if ( "invalid" === $datasetPageSettings->getPageType() ) {
            $this->redirect('/site/index');
        } elseif ( "public" === $datasetPageSettings->getPageType() ) {
            $this->redirect('/dataset/'.$model->identifier);
        } elseif ( "hidden" === $datasetPageSettings->getPageType() || "draft" === $datasetPageSettings->getPageType() ) {
            $model->token = Yii::$app->security->generateRandomString(16);
            $model->save();
            $this->redirect('/dataset/'.$model->identifier.'/token/'.$model->token);
        }
    }


    /**
     * Remove image file url on the custom image record associated to a dataset
     * @return void
     */
    public function actionClearImageFile()
    {
        $result['status'] = false;
        if (isset($_POST['doi'])) {
            $doi = $_POST['doi'];
            $model = Dataset::model()->findByAttributes([ 'identifier' => $doi ]);

            if ($model->image && $model->image->url && $model->image->deleteFile() )
                $result['status'] = true;
            else
                Yii::log("Failed clearing image file for dataset $doi","error");
        }

        echo json_encode($result);
        Yii::app()->end();
    }

    /**
     * Remove custom image and associate generic image
     */
    public function actionRemoveImage()
    {
        $result['status'] = false;
        if (isset($_POST['doi'])) {
            $model = Dataset::model()->findByAttributes([ 'identifier' => $_POST['doi'] ]);
            $oldImageID = $model->image_id;
            $model->image_id = Image::GENERIC_IMAGE_ID;
            if ($model->save()) {
                try {
                    if ( Image::model()->findByPk($oldImageID)->delete() )
                        $result['status'] = true;
                    else
                        Yii::log("Failed deleting image record $oldImageID", "error");
                } catch (CDbException $e) {
                    Yii::log($e->getMessage(),"error");
                }
            }
            else {
                Yii::log("Failed associating generic image","error");
            }
        }

        echo json_encode($result);
        Yii::app()->end();
    }

    /**
     *	post metadata, mint a new DOI
     *
     */
    public function actionMint()
    {
        $result['status'] = false;
        $status_array = array('Submitted', 'UserStartedIncomplete', 'Curation');

        $mds_metadata_url="https://mds.test.datacite.org/metadata";
        $mds_doi_url="https://mds.test.datacite.org/doi";

        $mds_username = Yii::app()->params['mds_username'];
        $mds_password = Yii::app()->params['mds_password'];
        $mds_prefix = Yii::app()->params['mds_prefix'];

        if (isset($_POST['doi'])) {
            $doi = $_POST['doi'];
            if (stristr($doi, "/")) {
                $temp = explode("/", $doi);
                $doi = $temp[1];
            }

            $doi = trim($doi);
            $dataset = Dataset::model()->find("identifier=?", array($doi));

            if ($dataset && ! in_array($dataset->upload_status, $status_array)) {
                $xml_data = $dataset->toXML();
                $ch= curl_init();
                curl_setopt($ch, CURLOPT_URL, $mds_metadata_url . '/' . $mds_prefix . '/' . $doi);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/xml;charset=UTF-8'));
                curl_setopt($ch, CURLOPT_USERPWD, $mds_username . ":" . $mds_password);
                $curl_response = curl_exec($ch);
                $result['md_curl_response'] = $curl_response;
                $info1 = curl_getinfo($ch);
                $result['md_curl_status'] = $info1['http_code'];
                curl_close($ch) ;
            }

            if ($dataset && $result['md_curl_status'] == 201) { # make sure the metadata has been registered first
                $doi_data = "doi=".$mds_prefix."/".$doi."\n"."url=http://gigadb.org/dataset/";
                $result['doi_data']  = $doi_data;
                $ch2= curl_init();
                curl_setopt($ch2, CURLOPT_URL, $mds_doi_url. '/' . $mds_prefix . '/' . $doi);
                curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch2, CURLOPT_POSTFIELDS, $doi_data);
                curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Content-Type:text/plain;charset=UTF-8'));
                curl_setopt($ch2, CURLOPT_USERPWD, $mds_username . ":" . $mds_password);
                $curl_response = curl_exec($ch2);
                $result['doi_curl_response'] = $curl_response;
                $info2 = curl_getinfo($ch2);
                $result['doi_curl_status'] = $info2['http_code'];
                curl_close($ch2) ;
            }

            if (isset($result['doi_curl_status']) && $result['doi_curl_status'] == 201) {
                $result['status'] = true;
            }
        }

        echo json_encode($result);
        Yii::app()->end();
    }

    /**
     * Check whether the posted DOI exist in database already
     *
     */
    public function actioncheckDOIExist()
    {
        $result = array();
        $result['status'] = false;
        if (isset($_POST['doi'])) {
            $doi = $_POST['doi'];
            if (stristr($doi, "/")) {
                $temp = explode("/", $doi);
                $doi = $temp[1];
            }

            $doi = trim($doi);

            $dataset = Dataset::model()->find("identifier=?", array($doi));
            if ($dataset) {
                $result['status'] = true;
            }
        }
        echo json_encode($result);
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
?>