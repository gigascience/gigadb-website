<?php

class ApiController extends Controller
{
    // Members



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

			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('Dataset','File' , 'Sample','Search','Dump','List'),
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    public function actionDump()
    {
        $fileName = Yii::app()->basePath.'/../files/gigadb_dump.xml';
        if (file_exists($fileName))
                return Yii::app()->getRequest()->sendFile('gigadb_dump.xml', @file_get_contents($fileName));
            else
                throw new CHttpException(404, 'The requested page does not exist.');

    }

    public function actionDataset()
	{
                $status='Published';
                $id = Yii::app()->request->getParam('id');
                $doi= Yii::app()->request->getParam('doi');
                $result= Yii::app()->request->getParam('result');
                if(!isset($result))
                {
                  $result='all';
                }
                if(isset($id))
                {
                   try{
                   $model=  Dataset::model()->findByAttributes(array('id'=>$id,'upload_status'=>$status));}
                   catch(CDbException $e)
                   {

                            $this->_sendResponse(404,
                            sprintf('No items where found for dataset id <b>%s</b>',$id) );
                   }
                    if(!isset($model))
                   {

                        $this->_sendResponse(404,
                            sprintf('No items where found for dataset id <b>%s</b>',$id) );
                   }
                }
                else{
                    try{
                    $model=  Dataset::model()->findByAttributes(array('identifier'=>$doi,'upload_status'=>$status));}
                    catch(CDbException $e)
                   {

                            $this->_sendResponse(404,
                            sprintf('No items where found for dataset doi <b>%s</b>',$doi) );
                   }
                   if(!isset($model))
                   {

                       $this->_sendResponse(404,
                            sprintf('No items where found for dataset doi <b>%s</b>',$doi) );
                   }

                }



                 switch ($result) {
                        case "dataset":
                            $this->renderPartial('singledatasetonly',array('model'=>$model,));
                            break;
                        case "sample":
                            $this->renderPartial('singlesample',array('model'=>$model,));
                            break;
                        case "file":
                            $this->renderPartial('singlefile',array('model'=>$model,));
                            break;
                        case "all":
                            $this->renderPartial('singledataset',array('model'=>$model,));
                            break;
                        default:
                            break;
                    }
               /*
                $this->renderPartial('singledataset',array(
			'model'=>$model,
		));*/
	}

    public function actionList()
    {

      $status='Published';
      $datasets = Dataset::model()-> findAllByAttributes(array('upload_status'=>$status));

       $this->renderPartial('list',array(
                    'models'=>$datasets,
            ));



    }

    public function actionFile()
	{
                $status='Published';
		$id = Yii::app()->request->getParam('id');
                $doi= Yii::app()->request->getParam('doi');
                if(isset($id))
                {

                   try{
                   $model=  Dataset::model()->findByAttributes(array('id'=>$id,'upload_status'=>$status));}
                   catch(CDbException $e)
                   {

                            $this->_sendResponse(404,
                            sprintf('No items where found for file id <b>%s</b>',$id) );
                   }
                    if(!isset($model))
                   {

                        $this->_sendResponse(404,
                            sprintf('No items where found for file id <b>%s</b>',$id) );
                   }
                }
                else{
                    $this->redirect(array("api/dataset?doi=$doi&result=file"));
                  //$this->redirect(array('api/dataset','doi'=>$doi,'result'=>'file'));
                   try{
                   $model=  Dataset::model()->findByAttributes(array('identifier'=>$doi,'upload_status'=>$status));}
                   catch(CDbException $e)
                   {

                            $this->_sendResponse(404,
                            sprintf('No items where found for file doi <b>%s</b>',$doi) );
                   }
                    if(!isset($model))
                   {

                        $this->_sendResponse(404,
                            sprintf('No items where found for file doi <b>%s</b>',$doi) );
                   }

                }


                $this->renderPartial('singlefile',array(
			'model'=>$model,
		));

	}

    public function actionSample()
	{
		$status='Published';
                $id = Yii::app()->request->getParam('id');
                $doi= Yii::app()->request->getParam('doi');
                if(isset($id))
                {

                   try{
                   $model=  Dataset::model()->findByAttributes(array('id'=>$id,'upload_status'=>$status));}
                   catch(CDbException $e)
                        {

                            $this->_sendResponse(404,
                            sprintf('No items where found for sample id <b>%s</b>',$id) );
                        }
                     if(!isset($model))
                   {

                        $this->_sendResponse(404,
                            sprintf('No items where found for sample id <b>%s</b>',$id) );
                   }
                }
                else{
                   try{
                    $this->redirect(array("api/dataset?doi=$doi&result=sample"));
                   $model=  Dataset::model()->findByAttributes(array('identifier'=>$doi,'upload_status'=>$status));}
                   catch(CDbException $e)
                        {

                            $this->_sendResponse(404,
                            sprintf('No items where found for sample doi <b>%s</b>',$doi) );
                        }
                    if(!isset($model))
                   {

                        $this->_sendResponse(404,
                            sprintf('No items where found for sample doi <b>%s</b>',$doi) );
                   }

                }


                $this->renderPartial('singlesample',array(
			'model'=>$model,
		));


	}


    public function actionSearch()
	{
		$status='Published';
                ini_set('log_errors', true);
                ini_set('error_log', dirname(__FILE__).'/php_errors.log');
                $keyword = Yii::app()->request->getParam('keyword');
                $result= Yii::app()->request->getParam('result');
                $taxno= Yii::app()->request->getParam('taxno');
                $taxname= Yii::app()->request->getParam('taxname');
                $author= Yii::app()->request->getParam('author');
                $manuscript= Yii::app()->request->getParam('manuscript');
                $token= Yii::app()->request->getParam('token');
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
                        try{
                            $models= Dataset::model()->findAllBySql($sql);}
                        catch(CDbException $e)
                        {

                            $this->_sendResponse(404,
                            sprintf('No items where found for keyword <b>%s</b>',$keyword) );
                        }

                        if (ob_get_contents()){

                        }

                        $this->renderByResult($result,$models);

                    }
                    else {

                        $ds = new DatabaseSearch();
                        $data = $ds->searchByKey($keyword);
                        $datasets=[];
                        $samples=[];
                        $files=[];

                        /*
                        if(isset($_GET['type']))
                        {
                            $type=$_GET['type'];
                             for($int=0;$int<count($type);$int++)
                             {
                                 if($type[$int]=='sample'){

                                 foreach($data['samples']['data'] as $sampleid)
                                 {
                                     $id = DatasetSample::model()->findByAttributes(array('sample_id'=>$sampleid));
                                     $datasets[] = $id->dataset_id;
                                 }
                                // $datasets[] = $data['samples']['data'];
                                 continue;
                                 }
                                 if($type[$int]=='file'){
                                  foreach($data['files']['data'] as $fileid)
                                 {
                                     $id = File::model()->findByAttributes(array('id'=>$fileid));
                                     $datasets[] = $id->dataset_id;
                                 }
                                 continue;
                                 }
                                 if($type[$int]=='dataset'){
                                 foreach($data['datasets']['data'] as $datasetid)
                                 {

                                     $datasets[] = $datasetid;
                                 }
                                 continue;
                                 }
                                 if($type[$int] !=='sample' || $type[$int] !=='file' || $type[$int] !=='dataset'){

                                 $this->_sendResponse(404,
                                 sprintf('Parameter type[] is wrong <b>%s</b>',$type[$int]) );
                                 }
                             }
                        }*/
                        foreach($data['datasets']['data'] as $datasetid)
                        {
                            $datasets[] = $datasetid;
                        }

                        foreach($data['samples']['data'] as $sampleid)
                        {
                            $samples[] = $sampleid;
                        }
                        foreach($data['files']['data'] as $fileid)
                        {
                            $files[] = $fileid;
                        }


                        if(empty($datasets)&&empty($samples)&&empty($files)){

                            if (ob_get_contents()){
                             }
                          $this->_sendResponse(404,
                          sprintf('No items where found for keyword <b>%s</b>',$keyword) );
                        }
                       // print_r($datasets);
                       // print_r($samples);
                      //  print_r($files);




                        if(!isset($_GET['result']))
                        {

                             $this->renderPartial('keyword',array(
                                'datasetids'=>$datasets,
                                'sampleids'=>$samples,
                                'fileids'=>$files));

                        }
                        else{
                            switch ($result) {
                                case "dataset":
                                     if(empty($datasets)){


                                  $this->_sendResponse(404,
                                  sprintf('No items where found for keyword <b>%s</b> in dataset, Please search in sample or file',$keyword) );
                                     }
                                    $this->renderPartial('keywordalldataset',array(
                                    'datasetids'=>$datasets,));
                                    break;
                                case "sample":
                                  if(empty($samples)){


                                  $this->_sendResponse(404,
                                  sprintf('No items where found for keyword <b>%s</b> in sample, Please search in dataset or file',$keyword) );
                                     }

                                    $this->renderPartial('keywordallsample',array(
                                    'sampleids'=>$samples,));
                                    break;
                                case "file":

                                    if(empty($files)){


                                  $this->_sendResponse(404,
                                  sprintf('No items where found for keyword <b>%s</b> in file, Please search in dataset or sample',$keyword) );
                                     }
                                    $this->renderPartial('keywordallfile',array(
                                    'fileids'=>$files,));
                                    break;

                                default:
                                    break;
                            }
                        }
                   /*
                     $this->renderPartial('keyword',array(
                            'datasetids'=>$datasets,
                            'sampleids'=>$samples,
                            'fileids'=>$files));
                    */

                    }
                }

                if(isset($taxno))
                {
                    if(isset($datasettype)&& $datasettype !='')

                    {
                    $uppertype=strtoupper($datasettype);
                    $sql='select DISTINCT dataset.id from dataset,dataset_sample,sample,species,dataset_type,type where dataset.id=dataset_sample.dataset_id and dataset_sample.sample_id=sample.id and sample.species_id=species.id and dataset_type.dataset_id=dataset.id and dataset_type.type_id=type.id and species.tax_id=:taxno and dataset.upload_status=:status and upper(type.name)=:datasettype;';
                    $command=$connection->createCommand($sql);
                    $command->bindParam(":taxno",$taxno,PDO::PARAM_STR);
                    $command->bindParam(":status",$status,PDO::PARAM_STR);
                    $command->bindParam(":datasettype",$uppertype,PDO::PARAM_STR);
                    $rows=$command->queryAll();

                    }
                    else{

                    $sql='select DISTINCT dataset.id from dataset,dataset_sample,sample,species where dataset.id=dataset_sample.dataset_id and dataset_sample.sample_id=sample.id and sample.species_id=species.id and species.tax_id=:taxno and dataset.upload_status=:status;';
                    $command=$connection->createCommand($sql);
                    $command->bindParam(":taxno",$taxno,PDO::PARAM_STR);
                    $command->bindParam(":status",$status,PDO::PARAM_STR);
                    $rows=$command->queryAll();



                    }

                    if(count($rows)<1)
                      {
                            if (ob_get_contents()){
                            }
                            $this->_sendResponse(404,
                            sprintf('No items where found for taxno <b>%s</b>',$taxno) );
                      }


                    $dataset_ids="";

                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    if (ob_get_contents()){
                    }
                    if(!isset($_GET['result']))
                    {

                        $this->renderPartial('keyworddataset',array('models'=>$models,));

                    }else{
                        $this->renderByResult($result,$models);
                    exit;
                    }



                }

                if(isset($taxname))
                {

                 if(isset($datasettype)&& $datasettype !='')  {
                    $uppertype=strtoupper($datasettype);
                    $sql="select DISTINCT dataset.id from dataset,dataset_sample,sample,species,dataset_type,type where dataset.id=dataset_sample.dataset_id and dataset_sample.sample_id=sample.id and sample.species_id=species.id and dataset_type.dataset_id=dataset.id and dataset_type.type_id=type.id and upper(species.scientific_name)=:scientific_name and dataset.upload_status=:status and upper(type.name)=:datasettype;";
                    $uppertaxname=strtoupper($taxname);
                    $command=$connection->createCommand($sql);
                    $command->bindParam(":scientific_name",$uppertaxname,PDO::PARAM_STR);
                    $command->bindParam(":status",$status,PDO::PARAM_STR);
                    $command->bindParam(":datasettype",$uppertype,PDO::PARAM_STR);
                    $rows=$command->queryAll();

                 }


                    $sql="select DISTINCT dataset.id from dataset,dataset_sample,sample,species where dataset.id=dataset_sample.dataset_id and dataset_sample.sample_id=sample.id and sample.species_id=species.id and upper(species.scientific_name)=:scientific_name and dataset.upload_status=:status;";
                    $uppertaxname=strtoupper($taxname);
                    $command=$connection->createCommand($sql);
                    $command->bindParam(":scientific_name",$uppertaxname,PDO::PARAM_STR);
                    $command->bindParam(":status",$status,PDO::PARAM_STR);
                    $rows=$command->queryAll();
                    if(count($rows)<1)
                      {
                             if (ob_get_contents()){
                             }
                            $this->_sendResponse(404,
                            sprintf('No items where found for taxname <b>%s</b>',$taxname) );
                      }

                    $dataset_ids="";

                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');

                    try{
                        $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                        $models= Dataset::model()->findAllBySql($sql1);
                    }
                    catch(CDbException $e)
                    {

                            $this->_sendResponse(404,
                            sprintf('No items where found for taxname <b>%s</b>',$taxname) );
                    }
                     if (ob_get_contents()){

                     }
                    if(!isset($_GET['result']))
                    {

                          $this->renderPartial('keyworddataset',array('models'=>$models,));

                    }else{
                        $this->renderByResult($result,$models);
                        exit;
                    }
                }
                if(isset($author))
                {

                    $names=  explode(" ", strtoupper($author));
                    if(count($names)>2)
                    {
                        $surname=$names[0];
                        $middlename=$names[1];
                        $firstname=$names[2];
                        $sql='select DISTINCT dataset.id from dataset,dataset_author,author where dataset.id=dataset_author.dataset_id and dataset_author.author_id=author.id and upper(author.surname)=:surname and upper(author.first_name)=:firstname and upper(author.middle_name)=:middlename and dataset.upload_status=:status;';
                        $command=$connection->createCommand($sql);
                        $command->bindParam(":surname",$surname,PDO::PARAM_STR);
                        $command->bindParam(":firstname",$firstname,PDO::PARAM_STR);
                        $command->bindParam(":middlename",$middlename,PDO::PARAM_STR);
                        $command->bindParam(":status",$status,PDO::PARAM_STR);
                        $rows=$command->queryAll();
                        $dataset_ids="";
                        if(count($rows)<1)
                        {

                            $this->_sendResponse(404,
                                sprintf('No items where found for author <b>%s</b>',$surname." ".$middlename." ".$firstname) );
                        }

                        foreach($rows as $row)
                        {
                            $dataset_ids=$dataset_ids.$row['id'].",";
                        }
                        $dataset_ids=  trim($dataset_ids,',');

                        try{
                            $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                            $models= Dataset::model()->findAllBySql($sql1);
                        }
                         catch(CDbException $e)
                        {

                                $this->_sendResponse(404,
                                sprintf('No items where found for author <b>%s</b>',$author) );
                        }



                        $this->renderByResult($result,$models);

                    }
                    else{
                        $surname=$names[0];
                        $firstname=$names[1];
                        $sql='select DISTINCT dataset.id from dataset,dataset_author,author where dataset.id=dataset_author.dataset_id and dataset_author.author_id=author.id and upper(author.surname)=:surname and upper(author.first_name)=:firstname and dataset.upload_status=:status;';
                        $command=$connection->createCommand($sql);
                        $command->bindParam(":surname",$surname,PDO::PARAM_STR);
                        $command->bindParam(":firstname",$firstname,PDO::PARAM_STR);
                        $command->bindParam(":status",$status,PDO::PARAM_STR);
                        $rows=$command->queryAll();
                        $dataset_ids="";
                        if(count($rows)<1)
                        {

                            $this->_sendResponse(404,
                                sprintf('No items where found for author <b>%s</b>',$surname." ".$firstname) );
                        }

                        foreach($rows as $row)
                        {
                            $dataset_ids=$dataset_ids.$row['id'].",";
                        }
                        $dataset_ids=  trim($dataset_ids,',');

                        try{
                            $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                            $models= Dataset::model()->findAllBySql($sql1);
                        }
                        catch(CDbException $e)
                        {

                            $this->_sendResponse(404,
                            sprintf('No items where found for author <b>%s</b>',$author) );
                        }



                        $this->renderByResult($result,$models);
                    }
                }
                if(isset($manuscript))
                {

                    $sql='select DISTINCT dataset.id from dataset,manuscript where manuscript.dataset_id=dataset.id and manuscript.identifier=:manuscript and dataset.upload_status=:status;';
                    $command=$connection->createCommand($sql);
                    $command->bindParam(":manuscript",$manuscript,PDO::PARAM_STR);
                    $command->bindParam(":status",$status,PDO::PARAM_STR);

                    $rows=$command->queryAll();
                    $dataset_ids="";
                    if(count($rows)<1)
                    {
                        if(isset($token) && $token == 'doibanner')
                        {

                            $body = $this->getDOIBannerBody();
                            echo $body;

                            Yii::app()->end();

                        }else{


                            $this->_sendResponse(404,
                                sprintf('No items where found for manuscript <b>%s</b>',$manuscript) );
                        }
                    }

                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');

                    try{
                        $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                        $models= Dataset::model()->findAllBySql($sql1);
                    }
                    catch(CDbException $e)
                    {
                        if(isset($token) && $token == 'doibanner')
                        {

                            $body = $this->getDOIBannerBody();
                            echo $body;

                            Yii::app()->end();

                         }else{

                            $this->_sendResponse(404,
                            sprintf('No items where found for manuscript <b>%s</b>',$manuscript) );
                        }
                    }



                    if(isset($token) && $token == 'doibanner')
                    {
                        $body = '
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>
    <body>
      <img style="-webkit-user-select: none" src="http://media.springer.com/lw900/springer-cms/rest/v1/content/755916/data/v1">
    </body>
    </html>';
                        echo $body;

                        Yii::app()->end();

                    }else{

                        $this->renderByResult($result,$models);
                    }

                }

                if(isset($datasettype))
                {
                      $uppertype=strtoupper($datasettype);
                      $sql='select DISTINCT dataset.id from dataset,dataset_type,type where dataset_type.dataset_id=dataset.id and dataset_type.type_id=type.id and upper(type.name)=:datasettype and dataset.upload_status=:status;';
                      $command=$connection->createCommand($sql);
                      $command->bindParam(":datasettype",$uppertype,PDO::PARAM_STR);
                      $command->bindParam(":status",$status,PDO::PARAM_STR);

                      $rows=$command->queryAll();
                      $dataset_ids="";
                       if(count($rows)<1)
                      {

                            $this->_sendResponse(404,
                            sprintf('No items where found for dataset type <b>%s</b>',$datasettype) );
                      }

                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');

                    try{
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    }
                     catch(CDbException $e)
                    {
                            if (ob_get_contents()){
                            }
                            $this->_sendResponse(404,
                            sprintf('No items where found for dataset type <b>%s</b>',$datasettype) );
                    }
                    if (ob_get_contents()){

                    }

                    $this->renderByResult($result,$models);

                }

                if(isset($project))
                {
                      $uppertype=strtoupper($project);
                      $sql='select DISTINCT dataset.id from dataset,dataset_project,project where dataset_project.dataset_id=dataset.id and dataset_project.project_id=project.id and upper(project.name)=:project and dataset.upload_status=:status;';
                      $command=$connection->createCommand($sql);
                      $command->bindParam(":project",$uppertype,PDO::PARAM_STR);
                      $command->bindParam(":status",$status,PDO::PARAM_STR);
                      $rows=$command->queryAll();
                      $dataset_ids="";
                      if(count($rows)<1)
                      {

                            $this->_sendResponse(404,
                            sprintf('No items where found for project <b>%s</b>',$project) );
                      }
                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');

                    try{
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    }
                    catch(CDbException $e)
                    {

                            $this->_sendResponse(404,
                            sprintf('No items where found for project <b>%s</b>',$project) );
                    }



                    $this->renderByResult($result,$models);

                }

	}

    public function actionView()
	{
		$this->render('view',array(
			'model'=>$this->loadModel(),
		));
	}

    private function _sendResponse($status = 200, $body = '', $content_type = 'text/html')
    {
        // set the status
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        header($status_header);
        // and the content type
        header('Content-type: ' . $content_type);
        // pages with body are easy
        if($body != '')
        {
            // send the body
            echo $body;
        }
        // we need to create the body if none is passed
        else
        {
            // create some body messages
            $message = '';
            // this is purely optional, but makes the pages a little nicer to read
            // for your users.  Since you won't likely send a lot of different status codes,
            // this also shouldn't be too ponderous to maintain
            switch($status)
            {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }
            // servers don't always have a signature turned on
            // (this is an apache directive "ServerSignature On")
            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];
            // this should be templated in a real-world solution
            $body = '
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
    </head>
    <body>
        <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
        <p>' . $message . '</p>
        <hr />
        <address>' . $signature . '</address>
    </body>
    </html>';
            echo $body;
        }
        Yii::app()->end();
    }

    private function _getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

    /**
    * render a partial base on the value of result URL query string paramter
    *
    * @param string $result query string to select which section of dataset to display
    * @param string $models database resulset
    */
    private function renderByResult($result,$models) {
        switch ($result) {
            case "dataset":
                $this->renderPartial('keyworddataset',array('models'=>$models,));
                break;
            case "sample":
                $this->renderPartial('keywordsample',array('models'=>$models,));
                break;
            case "file":
                $this->renderPartial('keywordfile',array('models'=>$models,));
                break;
            default:
                break;
        }
    }

    /**
    * return a full body placeholder
    *
    * @return  string $body
    */
    private function getDOIBannerBody() {
        $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
    <body>
      <img style="-webkit-user-select: none" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==">
    </body>
</html>';
        return $body;
    }

}

