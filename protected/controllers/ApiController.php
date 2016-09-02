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
		ini_set('log_errors', true);
                ini_set('error_log', dirname(__FILE__).'/php_errors.log');
                $keyword = Yii::app()->request->getParam('keyword');
                $result= Yii::app()->request->getParam('result');
                $taxno= Yii::app()->request->getParam('taxno');
                $taxname= Yii::app()->request->getParam('taxname');
                $author= Yii::app()->request->getParam('author');
                $manuscript= Yii::app()->request->getParam('manuscript');
                $datasettype= Yii::app()->request->getParam('datasettype');
                $project= Yii::app()->request->getParam('project');
                $connection=Yii::app()->db;
                if(!isset($result))
                {
                  $result='dataset'; 
                }
                
                if(isset($keyword))
                {
                    if(strpos($keyword, ':'))
                    {
                        $pieces = explode(":", $keyword);
                        $sql="SELECT * from dataset where ".$pieces[0]." like '%".$pieces[1]."%'";                      
                        $models= Dataset::model()->findAllBySql($sql);
                        
                    }else {
                        
                        $ds = new DatabaseSearch();        
                        $data = $ds->searchByKey($keyword);
                        $datasets = $data['datasets']['data'];
                        $invalue='';
                        foreach($datasets as $dataset)
                        {
                           $invalue=$invalue.$dataset.",";
 
                        }
                        $invalue = trim($invalue,',');
                        $sql="SELECT * from dataset where id in (".$invalue.")";
                        $models= Dataset::model()->findAllBySql($sql);
                        
                    }
                    ob_end_clean();
                    
                    switch ($result) {
                        case "dataset":
                            
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":
                          
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":
                            
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
               
                }
                
                if(isset($taxno))
                {
                        
                    
                    $sql='select DISTINCT dataset.id from dataset,dataset_sample,sample,species where dataset.id=dataset_sample.dataset_id and dataset_sample.sample_id=sample.id and sample.species_id=species.id and species.tax_id=:taxno;';
                    $command=$connection->createCommand($sql);
                    $command->bindParam(":taxno",$taxno,PDO::PARAM_STR); 
                    $rows=$command->queryAll();
                    
                    
                    $dataset_ids="";
                    
                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    ob_end_clean();

                    switch ($result) {
                        case "dataset":
                            
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":
                          
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":
                            
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
               
                }
                
                if(isset($taxname))
                {
                    
                    $sql="select DISTINCT dataset.id from dataset,dataset_sample,sample,species where dataset.id=dataset_sample.dataset_id and dataset_sample.sample_id=sample.id and sample.species_id=species.id and species.scientific_name=:scientific_name;";
                    
                    $command=$connection->createCommand($sql);
                    $command->bindParam(":scientific_name",$taxname,PDO::PARAM_STR);    
                    $rows=$command->queryAll();
                    
                    
                    $dataset_ids="";
                   
                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');
                    
                    
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    
                    ob_end_clean();

                    switch ($result) {
                        case "dataset":                         
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":                         
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":                           
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
                    
                }
                if(isset($author))
                {
                    
                    $names=  explode(" ", $author);
                    if(count($names)>2)
                    {
                      $surname=$names[0]; 
                      $middlename=$names[1];
                      $firstname=$names[2];  
                      $sql='select DISTINCT dataset.id from dataset,dataset_author,author where dataset.id=dataset_author.dataset_id and dataset_author.author_id=author.id and author.surname=:surname and author.first_name=:firstname and author.middle_name=:middlename;';
                      $command=$connection->createCommand($sql);
                      $command->bindParam(":surname",$surname,PDO::PARAM_STR); 
                      $command->bindParam(":firstname",$firstname,PDO::PARAM_STR); 
                      $command->bindParam(":middlename",$middlename,PDO::PARAM_STR); 
                      $rows=$command->queryAll();
                      $dataset_ids="";
                   
                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');
                    
                    
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    
                    ob_end_clean();

                    switch ($result) {
                        case "dataset":                         
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":                         
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":                           
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
                      
                      
                      
                      
                      
                    }
                    else{
                      $surname=$names[0];                   
                      $firstname=$names[1];  
                      $sql='select DISTINCT dataset.id from dataset,dataset_author,author where dataset.id=dataset_author.dataset_id and dataset_author.author_id=author.id and author.surname=:surname and author.first_name=:firstname;';
                      $command=$connection->createCommand($sql);
                      $command->bindParam(":surname",$surname,PDO::PARAM_STR); 
                      $command->bindParam(":firstname",$firstname,PDO::PARAM_STR); 
                      $rows=$command->queryAll();
                      $dataset_ids="";
                   
                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');
                    
                    
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    
                    ob_end_clean();

                    switch ($result) {
                        case "dataset":                         
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":                         
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":                           
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
  
                    }
  
                }
                if(isset($manuscript))
                {
                    
                      $sql='select DISTINCT dataset.id from dataset,manuscript where manuscript.dataset_id=dataset.id and manuscript.identifier=:manuscript;';
                      $command=$connection->createCommand($sql);
                      $command->bindParam(":manuscript",$manuscript,PDO::PARAM_STR); 
                       
                      $rows=$command->queryAll();
                      $dataset_ids="";
                   
                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');
                    
                    
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    
                    ob_end_clean();

                    switch ($result) {
                        case "dataset":                         
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":                         
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":                           
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
                    
                    
                }
                if(isset($datasettype))
                {
                    
                      $sql='select DISTINCT dataset.id from dataset,dataset_type,type where dataset_type.dataset_id=dataset.id and dataset_type.type_id=type.id and type.name=:datasettype;';
                      $command=$connection->createCommand($sql);
                      $command->bindParam(":datasettype",$datasettype,PDO::PARAM_STR); 
                       
                      $rows=$command->queryAll();
                      $dataset_ids="";
                   
                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');
                    
                    
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    
                    ob_end_clean();

                    switch ($result) {
                        case "dataset":                         
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":                         
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":                           
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
                    
                    
                }
                
                if(isset($project))
                {
                    
                      $sql='select DISTINCT dataset.id from dataset,dataset_project,project where dataset_project.dataset_id=dataset.id and dataset_project.project_id=project.id and project.name=:project;';
                      $command=$connection->createCommand($sql);
                      $command->bindParam(":project",$project,PDO::PARAM_STR);               
                      $rows=$command->queryAll();
                      $dataset_ids="";
                   
                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');
                    
                    
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    
                    ob_end_clean();

                    switch ($result) {
                        case "dataset":                         
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":                         
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":                           
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
                    
                    
                }
                
          

	}
        
        public function actionView()
	{
		$this->render('view',array(
			'model'=>$this->loadModel(),
		));
	}
        
}

