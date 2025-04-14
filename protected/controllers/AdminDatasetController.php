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
        $isStatusAvailable = true;

        // setting DatasetUpload, the busisness object for File uploading
        $datasetUpload = $this->getDatasetUpload($model->identifier);

        if ($uploadStatus && $uploadStatus !== $previousUploadStatus) {
            $isStatusAvailable = $this->checkAndSetTransition($datasetUpload, $model, $uploadStatus);
        }

        if (!$isStatusAvailable) {
            Yii::app()->user->setFlash('updateError', 'Fail to update status!');
            Yii::log(sprintf('Failed to change status to %s', $uploadStatus), 'error');

            return $this->render('update', array(
                'model' => $model,
                'datasetPageSettings' => $datasetPageSettings,
                'curationlog'=> $dataProvider,
                'dataset_id'=> $id,
            ));
        }

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
                Yii::app()->user->setFlash('updateError', 'Fail to update your types. You need to select at least one type');
                $hasPartialError = true;
            } else {
                $model->updateDatasetTypes($postDatasetTypes);
            }

            if ($uploadStatus && $uploadStatus !== $previousUploadStatus) {
                Yii::log('Status changed to '.$uploadStatus, 'info');
                $this->renderNotificationsAccordingToStatus($datasetUpload, $model);
            }

            // semantic keywords update, using remove all and re-create approach
            $postKeywords = Yii::$app->request->post('keywords', '');
            $attribute_service = Yii::app()->attributeService;
            $attribute_service->replaceKeywordsForDatasetIdWithString($id, $postKeywords);

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
        $id = Yii::$app->request->get('identifier');
        $model= Dataset::model()->find("identifier=?", array($id));
        $datasetPageSettings = new DatasetPageSettings($model);
        $pageType = $datasetPageSettings->getPageType();

        if (!in_array($pageType, ['invalid', 'public', 'hidden', 'draft', 'mockup'])) {
            throw new CHttpException(404, 'Page type not found');
        }

        if ("invalid" === $pageType) {
            $this->redirect('/site/index');
        } elseif ("public" === $pageType) {
            $this->redirect('/dataset/'.$model->identifier);
        } else {
            $model->token = Yii::$app->security->generateRandomString(16);

            if (!$model->save()) {
                throw new CHttpException(500, 'Fail to update dataset token');
            }

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
        $user = User::model()->findByPk(Yii::app()->user->id);

        if (!$user) {
            $result['error'] = 'An error occurred';
            echo json_encode($result);
            Yii::app()->end();
        }

        $userName = sprintf('%s %s', $user->first_name, $user->last_name);

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

        $action = 'DOI Minting';
        $log = sprintf('Dataset %s', $doi);
        $doiResponse = $client->request('GET', $mds_doi_url . '/' . $mds_prefix . '/' . $doi, [
            'http_errors' => false,
            'auth'        => [$mds_username, $mds_password]
        ]);
        $result['doi_response'] = $doiResponse->getBody()->getContents();
        $result['check_doi_status'] = $doiResponse->getStatusCode();
        $isPresent = in_array($result['check_doi_status'], [200, 204]);
        $log .= sprintf(' | Check DOI: %s', $isPresent ? "OK" : "DOI doesn't exist");

        if ($isPresent || $result['check_doi_status'] === 404) {
            if (!$xml_data = $dataset->toXML()) {
                $result['error'] = 'An error occurred while transforming the dataset as xml';
                $log .= ' ERROR: An error occurred while transforming the dataset as xml';

                echo json_encode($result);
                Yii::app()->end();
            }
            $options = [
                'headers'     => [
                    'Content-Type' => 'text/xml;charset=UTF8',
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
            $log .= sprintf(' | %s metadata response: %s', $result['check_doi_status'] === 200 ? 'update' : 'create', 201 === $result[$keyStatus] ? "OK" : $result[$keyResponse]);

            $logMessageXml = 201 === $result[$keyStatus] ? 'Sent DataCite XML' : 'Failed to send DataCite XML';
            CurationLog::createGeneralCurationLogEntry($dataset->id, $logMessageXml, $xml_data, $userName);

            if (201 === $result[$keyStatus] && 404 === $result['check_doi_status']) {
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
                $log .= sprintf(' | Create DOI: %s', $result['create_doi_status'] === 201 ? 'OK' : $result['create_doi_response']);
            }
        }

        $curationLog = CurationLog::model()->searchByDatasetId($dataset->id);
        CurationLog::createGeneralCurationLogEntry($dataset->id, $action, $log, $userName);

        $result['html'] = $this->renderPartial('curationLog', array('dataset_id' => $dataset->id, 'model' => $curationLog), true);
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

    private function getDatasetUpload(string $identifier): DatasetUpload
    {
        // setting DatasetUpload, the busisness object for File uploading
        $webClient = \Yii::$container->get('guzzleHttpClient');
        $fileUploadSrv = Yii::app()->fileUploadService->getFileUploadService($webClient, $identifier);

        return new DatasetUpload(
            $fileUploadSrv->dataset,
            $fileUploadSrv,
            Yii::$app->params['dataset_upload']
        );
    }

    private function checkAndSetTransition(DatasetUpload $datasetUpload, Dataset $model, string $newStatus): bool
    {
        switch ($newStatus) {
            case 'Submitted':
                return $datasetUpload->setStatusToSubmitted($model->upload_status);

            case 'DataPending':
                return $datasetUpload->setStatusToDataPending($model->upload_status);

            default:
                return true;
        }
    }

    private function renderNotificationsAccordingToStatus(DatasetUpload $datasetUpload, Dataset $model)
    {
        switch ($model->upload_status) {
            case 'Submitted':
                $contentToSend = $datasetUpload->renderNotificationEmailBody('Submitted');
                $statusIsSet = $datasetUpload->sendNotificationEmailBody($contentToSend, $model->upload_status);

                break;
            case 'DataPending':
                $contentToSend = ($emailBody = Yii::$app->request->post('Dataset')['emailBody']) ?
                    $this->processTemplateString($emailBody, ['identifier' => $model->identifier]) : $datasetUpload->renderNotificationEmailBody('DataPending');

                $statusIsSet = $datasetUpload->sendNotificationEmailBody($contentToSend, $model->upload_status, $model->submitter->email);

                break;
            default:
                $statusIsSet = true;
        }

        if ($statusIsSet) {
            CurationLog::createlog($model->upload_status, $model->id);
        }
    }
}

