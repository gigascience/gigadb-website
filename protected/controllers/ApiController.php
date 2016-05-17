<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



class ApiController extends Controller
{
    // Members
    /**
     * Key which has to be in HTTP USERNAME and PASSWORD headers 
     */
    Const APPLICATION_ID = 'ASCCPE';
 
    /**
     * Default response format
     * either 'json' or 'xml'
     */
    private $format = 'xml';
    /**
     * @return array action filters
     */
    public function filters()
    {
            return array();
    }
    
    public function actionIndex()
    {
        echo CJSON::encode(array(1, 2, 3));       
    }
    
    public function queryDatasets($key1,$value1)
    {
        if($key1=='dataset')
            $model = Dataset::model()->with('authors')->findByAttributes(array('identifier'=>$value1));
        elseif($key1=='keyword')
            $models=  $this->getFullDatasetResultByKeyword($value1);
        elseif($key1=='sample')
            $models = Dataset::model()->with('samples','samples.species')->findAll(array('condition'=>'scientific_name like :name','params'=>array(':name'=>'%'.$value1."%")));       
        elseif($key1=='author')
            $models = Dataset::model()->with('authors')->findAll(array('condition'=>'name like :name','params'=>array(':name'=>'%'.$value1."%"))); 
        
        if(empty($models) and empty($model)) {
            $this->renderPartial('view_empty');
        } elseif(empty($model)) {        
            $this->renderPartial('view_datasets',array('models'=>$models));   
        }else{
            $this->renderPartial('view',array('model'=>$model)); 
        } 
    }
    
    public function querySamples($key1,$value1)
    {
        if($key1=='sample')
            $models = Sample::model()->with('datasetSamples','species','datasets')->findAll(array('condition'=>'scientific_name like :name','params'=>array(':name'=>'%'.$value1."%")));                
 
        if(empty($models) and empty($model)) {
            $this->renderPartial('view_empty');
        } elseif(empty($model)) {        
            $this->renderPartial('view_samples',array('models'=>$models));   
        }else{
            $this->renderPartial('view',array('model'=>$model)); 
        } 
    }
    
    public function queryFiles($key1,$value1)
    {
        if($key1=='dataset')
            $models = File::model()->with('dataset')->findAll(array('condition'=>'identifier=:identifier','params'=>array(':identifier'=>$value1)));    
               
        if(empty($models) and empty($model)) {
            $this->renderPartial('view_empty');
        } elseif(empty($model)) {        
            $this->renderPartial('view_files',array('model'=>$models));   
        }else{
            $this->renderPartial('view',array('model'=>$model)); 
        } 
    }
    
    public function actionQuery()
    {
        $url=Yii::app()->request->requestUri;
        
        $equal_sign=strpos($url,'=');
        $question_mark=strpos($url,'?');
        if ($equal_sign==false or $question_mark==false){
            $models = Dataset::model()->findAll();
            if(empty($models)) {
                $this->renderPartial('view_empty');
            } else {      
                $this->renderPartial('view_datasets',array('models'=>$models));
            }
        }else{
            $return_sets=substr($url,11,$question_mark-11);
            $key1=substr($url,$question_mark+1,$equal_sign-$question_mark-1);
            $value1=substr($url,$equal_sign+1); 
            $value1=str_ireplace("%20", " ", $value1);
            if ($return_sets=='datasets')
                $this->queryDatasets($key1,$value1);
                //echo $key1," ", $value1;
            elseif ($return_sets=='samples')
                $this->querySamples ($key1, $value1);
                //echo $key1, " ", $value1;
            elseif($return_sets=='files')
                $this->queryFiles ($key1, $value1);
        }
    }
    
    
    // Actions
    public function actionList()
    {        
        // Get the respective model instance
        switch($_GET['model'])
        {
            case 'file':
                $models = File::model()->findAll();
                break;
            case 'dataset':
                $models = Dataset::model()->findAll();
                break;
            case 'sample':
                $models = Sample::model()->findAll();
                break;
            default:
                // Model not implemented error
               // $this->_sendResponse(501, sprintf(
                   // 'Error: Mode <b>list1</b> is not implemented for model <b>%s</b>',
                 //   $_GET['model']) );
                $this->renderPartial('view_empty');
                Yii::app()->end();
        }
        // Did we get some results?
        if(empty($models)) {
            // No
            //$this->_sendResponse(200, 
              //      sprintf('No items where found for model <b>%s</b>', $_GET['model']) );
            $this->renderPartial('view_empty');
        } else {
            // Prepare response
            //$rows = array();
            //foreach($models as $model)
            //    $rows[] = $model->attributes;
            // Send the response
            //$this->_sendResponse(200, CJSON::encode($rows));
            if($_GET['model']=='dataset'){         
                $this->renderPartial('view_datasets',array('models'=>$models));
            }
            elseif($_GET['model']=='file'){
                $this->renderPartial('view_files',array('models'=>$models));
            }
            elseif($_GET['model']=='sample'){
                $this->renderPartial('view_samples',array('models'=>$models));
            }
        }    
    }
    
    public function actionView()
    {
    	  $model = Dataset::model()->with('authors')->findByAttributes(array('identifier'=>$_GET['id']));
       if(!isset($_GET['id']))
            $this->_sendResponse(500, 'Error: Parameter <b>id</b> is missing' );
       $type='xml';
        if(isset($_GET['type']))
        {
        $type=$_GET['type'];  
        }
        $table='none';
        if(isset($_GET['table']))
        {
        $table=$_GET['table'];  
        }
       if($_GET['model']=='dataset'){
            //$this->_sendResponse(200, CJSON::encode($model));
            if($model->upload_status!='Published')
            {
                $this->_sendResponse(404,'Item Not Published now with id '.$_GET['id']);
            }
            else{
                ob_end_clean();
            	 if($table==='sample')
                {
                    $this->renderPartial('view_sample',array('model'=>$model,'type'=>$type));  
                }elseif ($table==='file') {
                    $this->renderPartial('view_file',array('model'=>$model,'type'=>$type));  
                    
                }elseif($table==='dataset'){
                    $this->renderPartial('view_datasets',array('model'=>$model,'type'=>$type));  
                }else
                $this->renderPartial('view',array('model'=>$model,'type'=>$type));
		}
        } 
        



    }

    // Actions
    public function actionSample()
    {
        // Get the respective model instance
        $models = Dataset::model()->with('samples','samples.species')->findAll(array('condition'=>'scientific_name like :name','params'=>array(':name'=>'%'.$_GET['name']."%")));                
        // Did we get some results?
        if(empty($models)) {
            // No
            //$this->_sendResponse(200, 
              //      sprintf('No items where found for for for model <b>%s</b>', $_GET['action']) );
            $this->renderPartial('view_empty');
        } else {       
            $this->renderPartial('view_datasets',array('models'=>$models));
            
        }                
        
        
        /*
        switch($_GET['action'])
        {
            case 'sample':
                $models = Dataset::model()->with('samples','samples.species')->findAll(array('condition'=>'scientific_name like :name','params'=>array(':name'=>'%'.$_GET['name']."%")));                
                break;
            default:
                // Model not implemented error
                $this->_sendResponse(501, sprintf(
                    'Error: Mode <b>list_sample</b> is not implemented for model <b>%s</b>',
                    $_GET['action']) );
                Yii::app()->end();
        }

        // Did we get some results?
        if(empty($models)) {
            // No
            $this->_sendResponse(200, 
                    sprintf('No items where found for for for model <b>%s</b>', $_GET['action']) );
        } else {
            if($_GET['action']=='sample'){         
                $this->renderPartial('view_datasets',array('models'=>$models));
            }
            }
         * 
         */
    }    
    
    public function actionKeyword()
    {
	if(isset($_GET['keyword']))
        {
          
            
        $keyword=$_GET['keyword'];
        

        $ds = new DatabaseSearch();        

        $data = $ds->searchByKey($keyword);
        $datasets = $data['datasets']['data'];
        $slicedatasets=NULL;
        if(isset($_GET['limit']))
        {
        $limit=$_GET['limit'];
        
        $slicedatasets = array_slice($datasets, 0, $limit);
        //print_r($slicedatasets);
        }
        else
        {
        $slicedatasets = $datasets;
        //print_r($slicedatasets);
        }
       
	 $table='none';
        if(isset($_GET['table']))
        {
            $table= $_GET['table'];
        }
 
        $type='xml';
        if(isset($_GET['type']))
        {
        $type=$_GET['type'];  
        }
        
        ob_end_clean();
        
	if($table === 'sample')
        {
        $this->renderPartial('keywordsample',array('data'=>$slicedatasets,'type'=>$type));
        }
        elseif($table === 'file')
        {
        $this->renderPartial('keywordfile',array('data'=>$slicedatasets,'type'=>$type));  
        }
	elseif($table === 'dataset')
        {
        $this->renderPartial('keyworddataset',array('data'=>$slicedatasets,'type'=>$type));
        }
        else
        {
        $this->renderPartial('keyword',array('data'=>$slicedatasets,'type'=>$type));
        }
                //$this->renderPartial('keywordtest',array('data'=>$slicedatasets,'type'=>$type));
        
        }
    }
  
     private function getFullDatasetResultByKeyword($keyword) {
        $wordCriteria=array();
        $wordCriteria['keyword']=$keyword;
        $list_result_dataset_criteria = Dataset::sphinxSearch($wordCriteria);
        //print_r($wordCriteria);
        //print_r($list_result_dataset_criteria);
        $temp_dataset_criteria = new CDbCriteria();
        $temp_dataset_criteria->addInCondition("id", $list_result_dataset_criteria);
        //print_r($temp_dataset_criteria);
        return Dataset::model()->findAll($temp_dataset_criteria);
    }
        // Actions
    public function actionAuthor()
    {
        // Get the respective model instance
        switch($_GET['action'])
        {
            case 'author':
                $models = Dataset::model()->with('authors')->findAll(array('condition'=>'name like :name','params'=>array(':name'=>'%'.$_GET['name']."%")));               
                break;
            default:
                // Model not implemented error
               // $this->_sendResponse(501, sprintf(
                 //   'Error: Mode <b>list</b> is not implemented for model <b>%s</b>',
                   // $_GET['action']) );
                $this->renderPartial('view_empty');
                Yii::app()->end();
        }
        // Did we get some results?
        if(empty($models)) {
            // No
            //$this->_sendResponse(200, 
              //      sprintf('No items where found for for for model <b>%s</b>', $_GET['action']) );
            $this->renderPartial('view_empty');
        } else {
            if($_GET['action']=='author'){         
                $this->renderPartial('view_datasets',array('models'=>$models));
            }
            }
    }    
  
    
    public function actionCreate()
    {
    }
    public function actionUpdate()
    {
    }
    public function actionDelete()
    {
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
    
}

?>
