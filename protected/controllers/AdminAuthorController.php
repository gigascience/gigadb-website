<?php

class AdminAuthorController extends Controller
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
				'actions'=>array('admin','delete','index','view','create','update','prepareUserLink','prepareAuthorMerge','linkUser','unlinkUser','mergeAuthors','identicalAuthorsGraph','unmerge'),
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
		$this->layout = 'new_datasetpage';
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
		$model=new Author;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Author']))
		{
			$model->attributes=$_POST['Author'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->layout = 'new_datasetpage';
		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
                $model->custom_name= $model->getDisplayName();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Author']))
		{
			$model->attributes=$_POST['Author'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->layout = 'new_datasetpage';
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
		$dataProvider=new CActiveDataProvider('Author');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Create a session to allow admin to search an author to link to the session-saved user
	 */
	public function actionPrepareUserLink($user_id, $abort="no") {
		if( null != $user_id && "no" == $abort ){
			if (preg_match("/^\d+$/", $user_id)) {
				Yii::app()->session['attach_user'] = $user_id;
				Yii::log(__FUNCTION__."> new session var: attach_user = ". $user_id, 'info');
				if( !empty(Yii::app()->session['merge_author']) ) {
					unset(Yii::app()->session['merge_author']);
				}
			}
			$this->redirect(array('adminAuthor/admin'));
		}
		else if (null != $user_id && "yes" == $abort) {
				unset(Yii::app()->session['attach_user']);
				Yii::log(__FUNCTION__."> unset session var: attach_user", 'info');
				$this->redirect(array('user/view','id'=>$user_id));
		}
		else {
			Yii::log(__FUNCTION__."> There is a problem with parameters received", 'error');
		}
	}


	/**
	 * Create a session to allow admin to search an author to link to the author
	 */
	public function actionPrepareAuthorMerge($origin_author_id, $abort="no") {
		if( null != $origin_author_id && "no" == $abort ){
			if (preg_match("/^\d+$/", $origin_author_id)) {
				Yii::app()->session['merge_author'] = $origin_author_id;
				Yii::log(__FUNCTION__."> new session var: merge_author = ". $origin_author_id, 'info');
				if( !empty(Yii::app()->session['attach_user']) ) {
					unset(Yii::app()->session['attach_user']);
				}
			}
			$this->redirect(array('adminAuthor/admin'));
		}
		else if (null != $origin_author_id && "yes" == $abort) {
				unset(Yii::app()->session['merge_author']);
				Yii::log(__FUNCTION__."> unset session var: merge_author", 'info');
				$this->redirect(array('adminAuthor/view','id'=>$origin_author_id));
		}
		else {
			Yii::log(__FUNCTION__."> There is a problem with parameters received", 'error');
		}
	}

	public function actionLinkUser($id) {
		$author = $this->loadModel($id);
		if ( isset(Yii::app()->session['attach_user']) ) {
			$user = User::model()->findByPk(Yii::app()->session['attach_user']);
			if (null != $user) {
				$author->gigadb_user_id = $user->id ;
				if( $author->save() ) {
					Yii::log(__FUNCTION__."> author (".$author->id.")/user (.".$user->id.".) linking has been performed",
						'info');
					if ($user->id == Yii::app()->session['attach_user']) {
						unset(Yii::app()->session['attach_user']);
					}
					$this->redirect(array('user/view','id'=>$user->id));
				}
				else {
					Yii::log(__FUNCTION__."> error while updating gigadb_user_id in author. " .implode(" ",$author->getErrors()['gigadb_user_id']), 'error');
				}
			}
			else {
				Yii::app()->user->setFlash('error', "user to link doesn't exist");
				Yii::log(__FUNCTION__."> user to link doesn't exist", 'error');
				$this->render('view',array(
					'model'=>$author,
				));
			}

		}
		else {
			Yii::log(__FUNCTION__."> attach_user is not set in session", 'error');
		}

	}

	public function actionUnlinkUser($id,$user_id) {
		$model = $this->loadModel($id);
		$user = User::model()->findByPk($user_id);
		if (null == $model) {
			Yii::log(__FUNCTION__."> no author model could be loaded",'warning');
			$this->redirect(array('site/admin'));
		}
		else if (null == $user) {
			Yii::log(__FUNCTION__."> no user model could be loaded",'warning');
			$this->redirect(array('user/update','id'=>$user->id));
		}
		else if ($user_id != $model->gigadb_user_id) {
			Yii::log(__FUNCTION__."> mismatch between loaded user and user id in author model",'warning');
			$this->redirect(array('user/update','id'=>$user->id));
		}
		else {
			$model->gigadb_user_id = null ;
			if( $model->save() ) {
				Yii::log(__FUNCTION__."> author (".$model->id.")/user (.".$user->id.".) linking has been removed",
						'info');
				$this->redirect(array('user/update','id'=>$user->id));
			}
			else {
				Yii::log(__FUNCTION__."> error while updating gigadb_user_id in author. " .implode(" ",$model->getErrors()['gigadb_user_id']), 'error');
			}
		}

		$this->redirect(array('site/admin'));
	}

	public function actionMergeAuthors($origin_author,$target_author) {
		$origin = $this->loadModel($origin_author);

		if ( isset(Yii::app()->session['merge_author']) ) {
			$merge_author = Yii::app()->session['merge_author'];
			if ($merge_author == $origin_author || $merge_author == $target_author) {
				if ( $origin->mergeAsIdenticalWithAuthor($target_author) ) {
					Yii::log(__FUNCTION__."> merging author {$origin_author} with {$target_author} was successful",'info');
					Yii::app()->user->setFlash('success', "Merging authors completed successfully.");
					$this->redirect(array('adminAuthor/view','id'=>$origin_author));
				}
				else {
					Yii::log(__FUNCTION__."> merging author {$origin_author} with {$target_author} failed",'error');
				}
			}
			else {
				Yii::log(__FUNCTION__."> merge_author {$merge_author} doesn't match GET parameters ({$origin_author},{$target_author}) to mergeAuthors", 'error');
			}
		}
		else {
			Yii::log(__FUNCTION__."> merge_author is not set in session", 'error');
		}
		$this->redirect(array('adminAuthor/admin'));
	}

	public function actionUnmerge($id) {
		$model = $this->loadModel($id);
		if( $model->unMerge() ) {
			Yii::app()->user->setFlash('success', "author unmerged from other authors");
			$this->redirect(array('adminAuthor/view','id'=>$id));
		}
		else {
			Yii::app()->user->setFlash('error', "unmerging from graph has encountered an error");
			$this->redirect(array('adminAuthor/view','id'=>$id));
		}
	}

	public function actionIdenticalAuthorsGraph($id) {
		$author = $this->loadModel($id);
		if( empty($author) ) {
			echo "";
			Yii::app()->end();
		}
		else {
			$authors = $author->getIdenticalAuthorsDisplayName();
			echo implode(", ",$authors);
			Yii::app()->end();
		}

	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{

		if( isset($_GET['attach_user']) ){
			if (preg_match("/^\d+$/", $_GET['attach_user'])) {
				Yii::app()->session['attach_user'] = $_GET['attach_user'];
			}
			else if ("abort" == $_GET['attach_user']) {
				unset(Yii::app()->session['attach_user']);
				$this->redirect(array('admin'));
			}
		}

		$model=new Author('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Author']))
			$model->setAttributes($_GET['Author']);

		$this->layout = 'new_main';
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
		$model=Author::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='author-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
