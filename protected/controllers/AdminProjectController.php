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
				'actions'=>array('admin','delete','index','view','create','update','uploadTempLogo'),
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
          $model->attributes = $_POST['Project'];
          $storage = Yii::$app->cloudStore;
          $tempImageLocation = $model->image_location;
          $model->image_location = null;

          // saving the model first to get an id, then writing the logo in a id-dependent path
          if ($model->save()) {
              if ($tempImageLocation) {
                  $logoUrl = $model->writeLogoFromUrl($storage, $tempImageLocation);

                  if ($logoUrl) {
                      $model->image_location = $logoUrl;
                      // Update only the image_location and skip validation of other fields
                      Project::model()->updateByPk($model->id, array('image_location' => $logoUrl));
                  }
              }

              $this->redirect(array('view','id'=>$model->id));
          }
      }

      $this->render('create',array(
        'model'=>$model,
      ));
	}

  /**
   * Upload a logo file in a temp folder and return the URL of the uploaded file.
   * @return void
   */
  public function actionUploadTempLogo() {
      if (!isset($_FILES['logo_image'])) {
          $this->makeJSONResponse(400, 'Invalid request. No file was uploaded.');
      }

      $uploadedLogoFile = CUploadedFile::getInstanceByName('logo_image');

      if ($uploadedLogoFile->getSize() > 1_000_000) {
          $this->makeJSONResponse(400, 'Invalid request. Logo image size should be less than 1MB');
      }

      if ($uploadedLogoFile) {
          $image_location = Project::writeTmpLogoFromFile($uploadedLogoFile);

          if (!$image_location) {
              $this->makeJSONResponse(500, 'Failed to save logo image');
          }
      }

      $this->makeJSONResponse(200, 'Logo uploaded successfully', [
          'image_location' => $image_location
      ]);
  }

  /**
   * Make a JSON response with the given code, message, and payload.
   * @param int $code The HTTP status code.
   * @param string $message The message to include in the response.
   * @param array $payload Additional data to include in the response.
   */
  private function makeJSONResponse($code, $message, $payload = []) {
      $success = $code < 400;
      header('Content-Type: application/json');
      http_response_code($code);
      echo CJSON::encode(array_merge([
          'success' => $success,
          'message' => $message,
      ], $payload));
      Yii::app()->end();
  }

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
      $model = $this->loadModel($id);

      // Uncomment the following line if AJAX validation is needed
      // $this->performAjaxValidation($model);

      if(isset($_POST['Project']))
      {
          $prevAttributes = $model->attributes;
          $newAttributes = $_POST['Project'];
          $model->attributes = $newAttributes;
          $storage = Yii::$app->cloudStore;

          $hasNewLogo = $model->image_location && $model->image_location !== $prevAttributes['image_location'];

          if ($hasNewLogo) {
              if ($prevAttributes['image_location']) {
                  $model->deleteLogo($storage);
              }

              $newLogoUrl = $model->writeLogoFromUrl($storage, $model->image_location);

              if ($newLogoUrl) {
                  $model->image_location = $newLogoUrl;
              }
          }

          // NOTE Saving the model will trigger validation and thus fail if the URL or name are left unchanged, we might not want that in the case where we need to update only the logo
          if($model->save()) {
            $this->redirect(array('view','id'=>$model->id));
          }
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
          $model = $this->loadModel($id);
          $model->delete();

          // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
          if(!isset($_GET['ajax'])) {
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
          }
      }
      else {
          throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
      }
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
