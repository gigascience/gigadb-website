<?php

class AdminDatasetProjectController extends Controller
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
				'actions'=>array('admin','delete','index','view','create','update'),
				'roles'=>array('admin'),
			),
                        array('allow',
                                'actions' => array('create1', 'delete1','addProject','deleteProject', 'getProject'),
                                 'users' => array('@'),
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
		$model=new DatasetProject;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['DatasetProject']))
		{
			$model->attributes=$_POST['DatasetProject'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}
        
         public function actionCreate1() {
        $model = new DatasetProject;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $model->dataset_id = 1;

        //update 
        if (!isset($_SESSION['projects']))
            $_SESSION['projects'] = array();

        $projects = $_SESSION['projects'];

        if (isset($_POST['DatasetProject'])) {

            $project_id = $_POST['DatasetProject']['project_id'];

            $model->project_id = $project_id;
            $id = 0;
            if ($this->storeProject($model, $id)) {

                $name = Project::model()->findByAttributes(array('id' => $project_id))->name;

                $newItem = array('id' => $id, 'name' => $name);


                array_push($projects, $newItem);

                $_SESSION['projects'] = $projects;
                
                //$vars = array('projects');
                ////Dataset::storeSession($vars);

                $model = new DatasetProject;
                
            }
        }


        $project_model = new CArrayDataProvider($projects);

        $this->render('create1', array(
            'model' => $model,
            'project_model' => $project_model,
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

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['DatasetProject']))
		{
			$model->attributes=$_POST['DatasetProject'];
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
        
         public function actionDelete1($id) {
        if (isset($_SESSION['projects'])) {
            $info = $_SESSION['projects'];
            foreach ($info as $key => $value) {
                if ($value['id'] == $id) {
                    unset($info[$key]);
                    $_SESSION['projects'] = $info;
                    
                    //$vars = array('projects');
                    ////Dataset::storeSession($vars);
                    $condition = "id=" . $id;
                    DatasetProject::model()->deleteAll($condition);
                    $this->redirect("/adminDatasetProject/create1");
                }
            }
        }
    }

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('DatasetProject');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new DatasetProject('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['DatasetProject']))
			$model->setAttributes($_GET['DatasetProject']);

		$this->render('admin',array(
			'model'=>$model,
		));
	}
        
          public function storeProject(&$model, &$id) {


        if (isset($_SESSION['dataset_id'])) {
            $dataset_id = $_SESSION['dataset_id'];

            $model->dataset_id = $dataset_id;
            if (!$model->save()) {
                $model->addError("error", "save error");
                return false;
            }
         
            $id = $model->id;
        }

        return true;
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=DatasetProject::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='dataset-project-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionAddProject() {
            if(isset($_POST['dataset_id']) && isset($_POST['project_id'])) {
            	$project = Project::model()->findByPk($_POST['project_id']);
            	if(!$project) {
            		Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Cannot find project.")));
            	}

            	$dp = DatasetProject::model()->findByAttributes(array('dataset_id'=>$_POST['dataset_id'], 'project_id'=>$_POST['project_id']));
            	if($dp) {
            		Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "This project has been added already.")));
            	}

            	$dp = new datasetProject;
            	$dp->dataset_id = $_POST['dataset_id'];
            	$dp->project_id = $_POST['project_id'];

            	if($dp->save()) {
            		Util::returnJSON(array("success"=>true));
            	}

                 Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Save Error.")));
            }
        }

        public function actionDeleteProject() {
            if(isset($_POST['dp_id'])) {
                $dp = DatasetProject::model()->findByPk($_POST['dp_id']);
                if($dp->delete()) {
                    Util::returnJSON(array("success"=>true));
                   }
                 Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Delete Error.")));
            }
        }

    public function actionGetProject() {
        if(isset($_POST['dataset_id']) && isset($_POST['project_id'])) {
            $project = Project::model()->findByPk($_POST['project_id']);
            if(!$project) {
                Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Cannot find project.")));
            }

            Util::returnJSON(array(
                "success"=>true,
                'project' => array(
                    'id' => $project->id,
                    'name' => $project->name,
                ),
            ));
        }
    }
}
