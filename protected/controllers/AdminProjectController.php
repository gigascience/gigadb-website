<?php

use Ramsey\Uuid\Uuid;

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
		Yii::log("actionCreate: Starting project creation", "info");
		$model = new Project;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Project']))
		{
			Yii::log("actionCreate: POST data received - " . print_r($_POST['Project'], true), "info");
			$model->attributes=$_POST['Project'];
      $storage = Yii::$app->cloudStore;
      $tempImageLocation = $model->image_location;
      $model->image_location = null;

      Yii::log("actionCreate: Project attributes - " . print_r($model->attributes, true), "info");
      Yii::log("actionCreate: Temp image location - " . $tempImageLocation, "info");

			if($model->save()) {
				Yii::log("actionCreate: Project saved successfully with ID " . $model->id, "info");
         // first we need to get the project id, and then we create the logo path deterministically
          if ($tempImageLocation) {
            Yii::log("actionCreate: Processing temp image at " . $tempImageLocation, "info");
            // get file from url and save it in project folder (instance dependent, deterministic path)
            $logoUrl = $model->writeLogoFromUrl($storage, $tempImageLocation);
            Yii::log("actionCreate: Logo URL after writeLogoFromUrl - " . ($logoUrl ?: 'null'), "info");
            $tempLogoPath = str_replace(Project::getStorageBasePath() . '/', '', $tempImageLocation);
            $tempDirectory = dirname($tempLogoPath);

            // delete file from payload url (it's always temp url)
            if ($storage->has($tempLogoPath)) {
                Yii::log("actionCreate: Attempting to delete temp file at " . $tempLogoPath, "info");
                if ($storage->deleteDir($tempDirectory)) {
                    Yii::log("actionCreate: Deleted temp dir " . $tempDirectory, "info");
                } else {
                    Yii::log("actionCreate: Failed to delete temp dir " . $tempDirectory, "warning");
                }
            } else {
                Yii::log("actionCreate: Temp image not found at " . $tempImageLocation, "warning");
            }

            if ($logoUrl) {
                Yii::log("actionCreate: Updating project with new logo URL - " . $logoUrl, "info");
                // Update only the image_location and skip validation of other fields
                $model->image_location = $logoUrl;
                // Use updateByPk to bypass validation
                Project::model()->updateByPk($model->id, array('image_location' => $logoUrl));
                Yii::log("actionCreate: Project updated with logo URL successfully", "info");
            }
          }
				$this->redirect(array('view','id'=>$model->id));
      } else {
          Yii::log("actionCreate: Failed to save project - " . print_r($model->getErrors(), true), "error");
      }
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

  public function actionUploadTempLogo() {
    Yii::log("actionUploadTempLogo: ", "info");
    if (!isset($_FILES['logo_image'])) {
      $this->makeResponse(400, 'Invalid request. No file was uploaded.');
    }

    // get multipart data file from request
    $uploadedLogoFile = CUploadedFile::getInstanceByName('logo_image');

    if ($uploadedLogoFile->getSize() > 1_000_000) {
      $message = 'Invalid request. Logo image size should be less than 1MB';
      Yii::app()->user->setFlash('updateError', $message);
      $this->makeResponse(400, $message);
    }

    $storage = Yii::$app->cloudStore;

    // save file to temp folder
    if ($uploadedLogoFile) {
      $image_location = Project::writeLogoFromFile($storage, Project::getTempLogoPath(), $uploadedLogoFile);
    }

    if (!$image_location) {
      $message = 'Failed to save your logo image';
      Yii::app()->user->setFlash('updateError', $message);
      $this->makeResponse(500, $message);
    }

    // return temp file url
    $this->makeResponse(200, 'Logo uploaded successfully', [
      'image_location' => $image_location
    ]);
  }

  /**
   * Make a JSON response with the given code, message, and payload.
   * @param int $code The HTTP status code.
   * @param string $message The message to include in the response.
   * @param array $payload Additional data to include in the response.
   */
  private function makeResponse($code, $message, $payload = []) {
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
		Yii::log("actionUpdate: Starting update for project ID: $id", "info");
		$model = $this->loadModel($id);
		Yii::log("actionUpdate: Loaded model with name: " . $model->name, "info");

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Project']))
		{
      Yii::log("actionUpdate: Processing POST data for project", "info");
      $prevAttributes = $model->attributes;
      $newAttributes = $_POST['Project'];
      Yii::log("actionUpdate: Previous image_location: " . $prevAttributes['image_location'], "info");
      Yii::log("actionUpdate: New image_location: " . $newAttributes['image_location'], "info");

			$model->attributes = $newAttributes;
      $storage = Yii::$app->cloudStore;

      // if there is a logo url and it's different from the previous one
      if ($model->image_location && $model->image_location !== $prevAttributes['image_location']) {
          Yii::log("actionUpdate: Detected logo change, processing new logo", "info");

          // Delete old logo if it exists
          if ($prevAttributes['image_location']) {
              $oldPath = str_replace(Project::getStorageBasePath() . '/', '', $prevAttributes['image_location']);
              $oldDir = dirname($oldPath);
              if ($storage->has($oldPath)) {
                  $deleteResult = $storage->deleteDir($oldDir);
                  Yii::log("actionUpdate: Old logo deletion result: " . ($deleteResult ? 'success' : 'failed'), "info");
              } else {
                  Yii::log("actionUpdate: Old logo file not found at: " . $oldPath, "warning");
              }
          }

          // save new file
          $logoUrl = $model->writeLogoFromUrl($storage, $model->image_location);
          Yii::log("actionUpdate: New logo URL: " . ($logoUrl ?: 'failed to generate'), "info");
          if ($logoUrl) {
              $model->image_location = $logoUrl;
          } else {
              Yii::log("actionUpdate: Failed to write new logo from URL", "error");
          }

          // TODO refactor into helper function
          $tempImageLocation = $newAttributes['image_location'];
          $tempLogoPath = str_replace(Project::getStorageBasePath() . '/', '', $tempImageLocation);
          $tempDirectory = dirname($tempLogoPath);

          // delete temp file
          if ($storage->has($tempLogoPath)) {
              Yii::log("actionCreate: Attempting to delete temp file at " . $tempLogoPath, "info");
              if ($storage->deleteDir($tempDirectory)) {
                  Yii::log("actionCreate: Deleted temp dir " . $tempDirectory, "info");
              } else {
                  Yii::log("actionCreate: Failed to delete temp dir " . $tempDirectory, "warning");
              }
          } else {
              Yii::log("actionCreate: Temp image not found at " . $tempImageLocation, "warning");
          }
      }

      // NOTE I think saving the model will trigger validation and thus fail if the URL or name are left unchanged
			if($model->save()) {
				Yii::log("actionUpdate: Successfully saved project changes", "info");
				$this->redirect(array('view','id'=>$model->id));
			} else {
				Yii::log("actionUpdate: Failed to save project changes. Errors: " . print_r($model->getErrors(), true), "error");
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
		Yii::log("actionDelete: Starting deletion process for project ID: $id", "info");

		if(Yii::app()->request->isPostRequest)
		{
			Yii::log("actionDelete: Received valid POST request", "info");

			// we only allow deletion via POST request
			$model = $this->loadModel($id);
			Yii::log("actionDelete: Loaded project model with name: " . $model->name, "info");

			try {
				if($model->delete()) {
					Yii::log("actionDelete: Successfully deleted project with ID: $id", "info");
				} else {
					Yii::log("actionDelete: Failed to delete project. Errors: " . print_r($model->getErrors(), true), "error");
				}
			} catch(\Exception $e) {
				Yii::log("actionDelete: Exception while deleting project: " . $e->getMessage(), "error");
				throw $e;
			}

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax'])) {
				$redirectUrl = isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin');
				Yii::log("actionDelete: Redirecting to: " . print_r($redirectUrl, true), "info");
				$this->redirect($redirectUrl);
			}
		}
		else {
			Yii::log("actionDelete: Invalid request method - must be POST", "error");
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
