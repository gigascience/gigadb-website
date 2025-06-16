<?php

declare(strict_types=1);

class DatasetLogController extends Controller
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
				'actions'=>array('admin','delete','index','view','create','update'),
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
	public function actionView()
	{
		if (!$id = Yii::$app->request->get('id')) {
            throw new CHttpException(400,'Invalid request. No id provided.');
        }

        $this->render('view', array('model' => $this->loadModel($id)));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new DatasetLog;

		if ($datasetLog = Yii::$app->request->post('DatasetLog')) {
			$model->attributes = $datasetLog;
			if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->id));
            }
		}

        $this->render('create', array('model' => $model));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate()
	{
		if (!$id = Yii::$app->request->get('id')) {
            throw new CHttpException(400,'Invalid request. No id provided.');
        }

        $model = $this->loadModel($id);

        if ($datasetLog = Yii::$app->request->post('DatasetLog')) {
            $model->attributes = $datasetLog;
            if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('update', array('model' => $model));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete(int $id)
	{
		if(!Yii::app()->request->isPostRequest) {
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
        }

        // we only allow deletion via POST request
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!Yii::$app->request->get('ajax')) {
            $returnUrl = Yii::$app->request->post('returnUrl');

            $this->redirect($returnUrl ?: array('admin'));
        }
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model = new DatasetLog('search');
		$model->unsetAttributes();  // clear any default values

        if ($datasetLog = Yii::$app->request->get('DatasetLog')) {
            $model->setAttributes($datasetLog);
        }

		$this->loadBaBbqPolyfills = true;

        $this->render('admin', array('model' => $model));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel(int $id): DatasetLog
	{
		$model = DatasetLog::model()->findByPk($id);

        if (!$model) {
            throw new CHttpException(404,'The requested page does not exist.');
        }

        return $model;
	}
}
