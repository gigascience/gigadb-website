<?php

class AdminUserCommandController extends Controller
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
				'actions'=>array('admin','delete','index','view','validate','reject','update'),
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
	 * Validate a claim by linking author with gigadb_user and updating user_command
	 */
	public function actionValidate($id)
	{
		$claim=$this->loadModel($id);
		if($claim) {
			if ("claim_author" == $claim->action_label) {
				$author = Author::model()->findbyPk($claim->actionable_id);
				$requester = User::model()->findbyPk($claim->requester_id);
				$author->gigadb_user_id = $requester->id;
				if($author->save()) {
					Yii::log(__FUNCTION__."> author (".$author->id.")/user (".$requester->id. ") linking has been performed", 'warning');
					$claim->status = "linked";
					$claim->actioner_id = Yii::app()->user->id;
					$now = new Datetime();
					$claim->action_date = $now->format(DateTime::ISO8601);
					if($claim->save()) {
						Yii::log(__FUNCTION__."> claim " . $claim->id . " updated as 'linked'", 'warning');
					}
					$claim->delete(); //claim record when validated is not needed (job done) and there is log/email for audit
				}else {
					Yii::log(__FUNCTION__."> author (".$author->id.")/user (".$requester->id. ") linking failed", 'warning');
					$claim->status = "validation error";
					$claim->actioner_id = Yii::app()->user->id;
					if($claim->save()) {
						Yii::log(__FUNCTION__."> claim " . $claim->id . " updated as 'validation error'", 'warning');
					}
				}


			}
		}

		$this->redirect(array('user/view/','id' => $requester->id));

	}

	/**
	 * Reject a claim
	 */
	public function actionReject($id)
	{
		$claim=$this->loadModel($id);
		if($claim) {  //claim record when rejected needs to be kept to prevent someone repeatedly claiming despite rejection
			if ("claim_author" == $claim->action_label) {
				$claim->status = "rejected";
				$claim->actioner_id = Yii::app()->user->id;
				$now = new Datetime();
				$claim->action_date = $now->format(DateTime::ISO8601);
				if($claim->save()) {
					Yii::app()->user->setFlash('success', "Claimed rejected. No linking performed");
					Yii::log(__FUNCTION__."> claim " . $claim->id . " updated as 'rejected'", 'warning');
				}
				// $author = Author::model()->findbyPk($claim->actionable_id);
				// if( (null != $author) && ($claim->requester_id == $author->gigadb_user_id) ){
				// 	$author->gigadb_user_id = null;
				// 	if($author->save()) {
				// 		Yii::log(__FUNCTION__."> author ".$author->id." has been unlinked from gigadb_user_id: ".$claim->requester_id , 'warning');
				// 	}
				// 	else {
				// 		Yii::log(__FUNCTION__."> author couldnt be saved",'warning');
				// 	}
				// }
				// else {
				// 	Yii::log(__FUNCTION__."> author ". $claim->actionable_id . " couldnt be found", 'warning');
				// 	Yii::log(__FUNCTION__."> claim->requester_id == author->gigadb_user_id ? " .$claim->requester_id." == " . $author->gigadb_user_id, 'warning');
				// }
			}
		}

		$this->redirect(array('user/update','id' => $claim->requester_id));

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

		if(isset($_POST['UserCommand']))
		{
			$model->attributes=$_POST['UserCommand'];
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
		$dataProvider=new CActiveDataProvider('UserCommand');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new UserCommand('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['UserCommand']))
			$model->setAttributes($_GET['UserCommand']);

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
		$model=UserCommand::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-command-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
