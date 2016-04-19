<?php

class DatasetFunderController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;

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
				'users'=>array('@'),
				'roles'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 */
	public function actionView()
	{
		$this->render('view',array(
			'model'=>$this->loadModel(),
		));
	}

	public function getDatasetIds() {
		$dois = Util::getDois();
		$l = array();
		foreach($dois as $doi) {
			$l[$doi['id']] = $doi['identifier'];
		}
		return $l;
	}

	public function getFunderIds() {
		$funders = Yii::app()->db->createCommand()
					->select("id, primary_name_display")
					->from("funder_name")
					->order("primary_name_display asc")
					->queryAll();
		$l = array();
		foreach($funders as $funder) {
			$l[$funder['id']] = $funder['primary_name_display'];
		}
		return $l;
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new DatasetFunder;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$datasets = $this->getDatasetIds();
		$funders = $this->getFunderIds();

		if(isset($_POST['DatasetFunder']))
		{
			$attrs = $_POST['DatasetFunder'];
			$model->attributes = $attrs;
			$model->grant_award = Util::trimText($attrs['grant_award']);
			$model->comments = Util::trimText($attrs['comments']);

			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
			'funders'=>$funders,
			'datasets'=>$datasets,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionUpdate()
	{
		$model=$this->loadModel();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$datasets = $this->getDatasetIds();
		$funders = $this->getFunderIds();

		if(isset($_POST['DatasetFunder']))
		{
			$attrs = $_POST['DatasetFunder'];
			$model->attributes = $attrs;
			$model->grant_award = Util::trimText($attrs['grant_award']);
			$model->comments = Util::trimText($attrs['comments']);

			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
			'datasets' => $datasets,
			'funders' => $funders,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 */
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel()->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('DatasetFunder');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new DatasetFunder('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['DatasetFunder']))
			$model->setAttributes($_GET['DatasetFunder'],true);

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id']))
				$this->_model=DatasetFunder::model()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='dataset-funder-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
