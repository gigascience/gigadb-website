<?php

class ApiController extends Controller
{
    // Members
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
        
        
        public function actionDataset()
	{

                $id = Yii::app()->request->getParam('id');
                $doi= Yii::app()->request->getParam('doi');
                if(isset($id))
                {
                   echo $id; 
                   $model=  Dataset::model()->findByAttributes(array('id'=>$id));
                }
                else{
                   $model=  Dataset::model()->findByAttributes(array('identifier'=>$doi));
                   echo $doi;
                }
                
                ob_end_clean();
                $this->renderPartial('singledataset',array(
			'model'=>$model,
		));
	}
        
        public function actionFile()
	{
		$id = Yii::app()->request->getParam('id');
                $doi= Yii::app()->request->getParam('doi');
                if(isset($id))
                {
                   echo $id; 
                   $model=  Dataset::model()->findByAttributes(array('id'=>$id));
                }
                else{
                   $model=  Dataset::model()->findByAttributes(array('identifier'=>$doi));
                   echo $doi;
                }
                
                ob_end_clean();
                $this->renderPartial('singlefile',array(
			'model'=>$model,
		));

	}
        
         public function actionSample()
	{
		$id = Yii::app()->request->getParam('id');
                $doi= Yii::app()->request->getParam('doi');
                if(isset($id))
                {
                   echo $id; 
                   $model=  Dataset::model()->findByAttributes(array('id'=>$id));
                }
                else{
                   $model=  Dataset::model()->findByAttributes(array('identifier'=>$doi));
                   echo $doi;
                }
                
                ob_end_clean();
                $this->renderPartial('singlesample',array(
			'model'=>$model,
		));
                

	}
        
        
         public function actionSearch()
	{
		echo "hello";
                $keyword = Yii::app()->request->getParam('keyword');
                $result= Yii::app()->request->getParam('result');
                $taxno= Yii::app()->request->getParam('taxno');
                $author= Yii::app()->request->getParam('author');
                $manuscript= Yii::app()->request->getParam('manuscript');
                $datasettype= Yii::app()->request->getParam('datasettype');
                $project= Yii::app()->request->getParam('project');
                if(!isset($result))
                {
                  $result='dataset'; 
                }
                echo $result;
                echo $keyword;
                echo $project;

	}
        
        public function actionView()
	{
		$this->render('view',array(
			'model'=>$this->loadModel(),
		));
	}
        
}

