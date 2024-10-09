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

    private function processTemplateString(string $inputString, array $vars): string {
        foreach ($vars as $key => $value) {
            $pattern = "/{{\s*" . preg_quote($key, '/') . "\s*}}/";
            $inputString = preg_replace($pattern, $value, $inputString);
        }
        return $inputString;
    }

    protected function registerTooltipScript() {
        // Check if the script has already been registered
        if (!Yii::app()->clientScript->isScriptRegistered('bootstrap-tooltip-init')) {
            $jsFile = Yii::getPathOfAlias('application.js.bootstrap-tooltip-init') . '.js';
            $jsUrl = Yii::app()->assetManager->publish($jsFile);
            Yii::app()->clientScript->registerScriptFile($jsUrl, CClientScript::POS_END);
        }
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
        $hasPartialError = false;
        $model = $this->loadModel($id);
        $datasetPageSettings = new DatasetPageSettings($model);
        $dataProvider = CurationLog::model()->searchByDatasetId($id);

        if (!$postDataset = Yii::$app->request->post('Dataset')) {
            $this->loadBaBbqPolyfills = true;

            return $this->render('update', array(
                'model' => $model,
                'datasetPageSettings' => $datasetPageSettings,
                'curationlog'=> $dataProvider,
                'dataset_id'=> $id,
            ));
        }

        Yii::log('**** new attributes: ' . print_r($postDataset, true), 'warning');
        $uploadStatus = $postDataset['upload_status'];
        $previousUploadStatus = $model->upload_status;

        //curator
        $curatorId = $postDataset['curator_id'];
        if ($curatorId !== $model->curator_id) {
            CurationLog::createlog_assign_curator($id, $curatorId);
            $model->curator_id = $curatorId;
        }

        $model->manuscript_id = $postDataset['manuscript_id'] ?? null;

        $model->setAttributes($postDataset);

        // Image information
        $datasetImage = CUploadedFile::getInstanceByName('datasetImage');
        if ($model->image){
            $isUpdated = $model->updateImageAndMetafields($datasetImage);
            $hasPartialError = !$isUpdated || $hasPartialError;
        } else {
            Yii::log(print_r($model->image->getErrors(), true), 'error');
        }

        $model->nullifyDateValueIfEmpty();

        if ($model->save()) {
            $postDatasetTypes = array_keys(Yii::$app->request->post('datasettypes'));
            if (!$postDatasetTypes) {
                Yii::app()->user->setFlash('updateError', 'Fail to update your types');
                $hasPartialError = true;
            } else {
                $model->updateDatasetTypes($postDatasetTypes);
            }

            if ($uploadStatus !== $previousUploadStatus) {
                $this->renderNotificationsAccordingToStatus($uploadStatus, $previousUploadStatus, $model);
            }

            // semantic kewyords update, using remove all and re-create approach
            if ($postKeywords = Yii::$app->request->post('keywords')) {
                $attribute_service = Yii::app()->attributeService;
                $attribute_service->replaceKeywordsForDatasetIdWithString($id, $postKeywords);
            }

            $urlToRedirect = Yii::$app->request->post('urltoredirect');
            // retrieve existing redirect
            $criteria = new CDbCriteria(array('order'=>'id ASC'));
            $urlToRedirectAttr = Attributes::model()->findByAttributes(array('attribute_name'=>'urltoredirect'));
            $urlToRedirectDatasetAttribute = DatasetAttributes::model()->findByAttributes(array('dataset_id'=>$id,'attribute_id'=>$urlToRedirectAttr->id), $criteria);

            // update with value from form if value has changed.
            if ($urlToRedirectDatasetAttribute && $urlToRedirect !== $urlToRedirectDatasetAttribute->value) {
                $urlToRedirectDatasetAttribute->value = $urlToRedirect;
                $urlToRedirectDatasetAttribute->save();
            } elseif ($urlToRedirect) {
                $urlToRedirectDatasetAttribute = new DatasetAttributes();
                $urlToRedirectDatasetAttribute->attribute_id = $urlToRedirectAttr->id;
                $urlToRedirectDatasetAttribute->dataset_id = $id;
                $urlToRedirectDatasetAttribute->value = $urlToRedirect;
                $urlToRedirectDatasetAttribute->save();
            }

            if ($hasPartialError) {
                 $this->redirect(array('/adminDataset/update/id/' . $model->id));
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

        $this->loadBaBbqPolyfills = true;
        $this->registerTooltipScript();
        $this->render('update', array(
            'model' => $model,
            'datasetPageSettings' => $datasetPageSettings,
            'curationlog'=> $dataProvider,
            'dataset_id'=> $id,
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
        if (!$doi = Yii::$app->request->post('doi')) {
            $result['error'] = 'You need to provide a DOI';
            echo json_encode($result);
            Yii::app()->end();
        }

        $status_array = array('Submitted', 'UserStartedIncomplete', 'Curation');

        $mds_metadata_url= Yii::app()->params['mds_metadata_url'];
        $mds_doi_url= Yii::app()->params['mds_doi_url'];
        $mds_username = Yii::app()->params['mds_username'];
        $mds_password = Yii::app()->params['mds_password'];
        $mds_prefix = Yii::app()->params['mds_prefix'];

        $result = [];

        if (stristr($doi, "/")) {
            $temp = explode("/", $doi);
            $doi = $temp[1];
        }

        $doi = trim($doi);
        $dataset = Dataset::model()->find("identifier=?", array($doi));
        $client = Yii::$container->get('guzzleHttpClient');

        if (!$dataset || in_array($dataset->upload_status, $status_array)) {
            $result['error'] = 'Please, check the dataset and the status';
            echo json_encode($result);
            Yii::app()->end();
        }

        $doiResponse = $client->request('GET', $mds_doi_url . '/' . $mds_prefix . '/' . $doi, [
            'http_errors' => false,
            'auth'        => [$mds_username, $mds_password]
        ]);
        $result['doi_response'] = $doiResponse->getBody()->getContents();
        $result['check_doi_status'] = $doiResponse->getStatusCode();

        if ($result['check_doi_status'] === 200 || $result['check_doi_status'] === 204  || $result['check_doi_status'] === 404) {
            $xml_data = $dataset->toXML();
            $options = [
                'headers'     => [
                    'Content-Type' => 'text/xml; charset=UTF8',
                ],
                'auth'        => [$mds_username, $mds_password],
                'body'        => $xml_data,
                'http_errors' => false
            ];
            $updateMdResponse = $client->request('POST', $mds_metadata_url . '/' . $mds_prefix . '/' . $doi, $options);

            $keyResponse = sprintf('%s_md_response', $result['check_doi_status'] === 200 ? 'update' : 'create');
            $keyStatus = sprintf('%s_md_status', $result['check_doi_status'] === 200 ? 'update' : 'create');
            $result[$keyResponse] = $updateMdResponse->getBody()->getContents();
            $result[$keyStatus] = $updateMdResponse->getStatusCode();

            if (201 === $updateMdResponse->getStatusCode() && 404 === $result['check_doi_status']) {
                $result['doi_data'] = 'doi=' . $mds_prefix . '/' . $doi . "\n" . 'url=http://gigadb.org/dataset/' . $doi;
                $options = [
                    'headers'     => [
                        'Content-Type' => 'text/plain; charset=UTF-8',
                    ],
                    'auth'        => [$mds_username, $mds_password],
                    'body'        => $result['doi_data'],
                    'http_errors' => false
                ];

                $response = $client->request('PUT', $mds_doi_url. '/' . $mds_prefix . '/' . $doi, $options);

                $result['create_doi_response'] = $response->getBody()->getContents();
                $result['create_doi_status'] = $response->getStatusCode();
            }
        }

        if (!$result) {
            $result['error'] = 'An error occurred';
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
        $model = Dataset::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    private function renderNotificationsAccordingToStatus($uploadStatus, $previousStatus, $model)
    {
        // setting DatasetUpload, the busisness object for File uploading
        $webClient = new \GuzzleHttp\Client();
        $fileUploadSrv = Yii::app()->fileUploadService->getFileUploadService($webClient, $model->identifier);
        $datasetUpload = new DatasetUpload(
            $fileUploadSrv->dataset,
            $fileUploadSrv,
            Yii::$app->params['dataset_upload']
        );

        switch ($uploadStatus) {
            case 'Submitted':
                $contentToSend = $datasetUpload->renderNotificationEmailBody('Submitted');
                $statusIsSet = $datasetUpload->setStatusToSubmitted($contentToSend, $previousStatus);

                break;
            case 'DataPending':
                $contentToSend = $datasetUpload->renderNotificationEmailBody('DataPending');

                // If formdata has a defined custom email body, user it instead of the twig template
                if (isset($_POST['Dataset']['emailBody']) && $_POST['Dataset']['emailBody'] != '') {
                    $contentToSend = $this->processTemplateString($_POST['Dataset']['emailBody'], [
                        'identifier' => $model->identifier
                    ]);
                }

                $statusIsSet = $datasetUpload->setStatusToDataPending(
                    $contentToSend, $model->submitter->email, $previousStatus
                );

                break;
            default:
                $statusIsSet = true;
        }

        if ($statusIsSet) {
            CurationLog::createlog($uploadStatus, $model->id);
        }
    }
}
?>
