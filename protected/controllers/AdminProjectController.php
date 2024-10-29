<?php

class AdminProjectController extends Controller
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
			array('allow', // admin only
				'actions'=>array('admin','delete','index','view','create','update','uploadLogo'),
				'roles'=>array('admin'),
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

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new Project;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Project']))
		{
      Yii::log("action Create: project form data exists", "warning");
			$model->attributes = $_POST['Project'];

      Yii::log("action Create: project form data - " . print_r($model->attributes, true), "warning");

      Yii::log("action Create: image_logo - " . print_r($model->image_logo, true), "warning");

      $uploadedLogo = CUploadedFile::getInstance($model, 'image_logo');

      Yii::log("action Create: uploaded file - " . $uploadedLogo, "warning");

      if ($uploadedLogo) {
        Yii::log("action Create: image form data exists", "warning");
        if ($model->writeLogo(Yii::$app->cloudStore, $uploadedLogo)) {
          Yii::log("action Create: logo uploaded successfully", "warning");
            // Logo uploaded successfully
        } else {
            Yii::log("action Create: Failed to write logo to storage for project " . $model->id, "error");
        }
      }
      // $this->redirect(array('create'));

			if($model->save()) {
				$this->redirect(array('view','id'=>$model->id));
      }

		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

  private function validateFileSize($file, $maxSizeInBytes) {
    return $file->getSize() <= $maxSizeInBytes;
  }

  // this method should be called by the uploader
  public function actionUploadLogo() {
    if (!isset($_FILES['logo_image'])) {
      Yii::log("No logo_image file received in upload request", "warning");

      // TODO research if this is the actual way to respond with 400
      header('Content-Type: application/json');
      http_response_code(400);
      echo CJSON::encode([
        'success' => false,
        'message' => 'No file was uploaded',
      ]);
      Yii::app()->end();
    }

    $uploadedLogo = CUploadedFile::getInstanceByName('logo_image');

    // Yii::log("action UploadLogo: uploaded file - " . print_r($uploadedLogo, true), "warning");
    // action UploadLogo: uploaded file - CUploadedFile Object
    // (
    //     [_name:CUploadedFile:private] => G10Klogo_renamed.jpg
    //     [_tempName:CUploadedFile:private] => /tmp/phpe2H3JK
    //     [_type:CUploadedFile:private] => image/jpeg
    //     [_size:CUploadedFile:private] => 7702
    //     [_error:CUploadedFile:private] => 0
    //     [_e:CComponent:private] =>
    //     [_m:CComponent:private] =>
    // )

    // TODO validate file size server side (file size < 1MB)
    if (!$this->validateFileSize($uploadedLogo, 1_000_000)) {
      Yii::log("action UploadLogo: file size is greater than 1MB", "warning");
      header('Content-Type: application/json');
      http_response_code(400);
      echo CJSON::encode([
        'success' => false,
        'message' => 'File size should be less than 1MB',
      ]);
      Yii::app()->end();
    }
    // TODO store file in S3 bucket
    // TODO if a new file is uploaded, delete the old one
    // Yii::log(print_r($_FILES['logo_image'], true), "warning");
    /**
     * (
     *     [name] => G10Klogo_renamed.jpg
     *     [type] => image/jpeg
     *     [tmp_name] => /tmp/php4humoH
     *     [error] => 0
     *     [size] => 7702
     * )
     */

    // Mock async process
    sleep(1); // Simulate 1 second processing delay

    // Mock response for now
    $response = [
        'success' => true,
        'image_location' => 'https://assets.gigadb-cdn.net/assets/images/' . $_FILES['logo_image']['name']
    ];
    // TODO call writeLogo method that writes image to S3 bucket

    // Send JSON response
    header('Content-Type: application/json');
    echo CJSON::encode($response);
    Yii::app()->end();
  }

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Project']))
		{
			$model->attributes=$_POST['Project'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Project');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Project('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Project']))
			$model->setAttributes($_GET['Project']);

		$this->loadBaBbqPolyfills = true;
		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Project::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='project-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
