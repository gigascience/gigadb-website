<?php

class DatasetController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/new_column2';

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
				'actions'=>array('view','checkDOIExist' , 'aSetSortCookies','ResetPageSize','Mint'),
				'users'=>array('*'),
			),
			array('allow',  // allow logged-in users to perform 'upload'
				'actions'=>array('upload','delete','create1','submit','updateSubmit', 'updateFile',
                    'datasetManagement','authorManagement','projectManagement','linkManagement','exLinkManagement',
                    'relatedDoiManagement','sampleManagement','PxInfoManagement','datasetAjaxDelete'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
			      'actions'=>array('admin','update','create','updateMetadata','private', 'mint', 'index'),
			      'roles'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}


	public function actionView($id)	{
        $form = new SearchForm;  // Use for Form
        $dataset = new Dataset; // Use for auto suggestion
        $this->layout='new_column2';
        $model = Dataset::model()->find("identifier=?", array($id));
        if (!$model) {

            $form = new SearchForm;
            $keyword = $id;
            $this->render('invalid', array('model' => $form, 'keyword' => $keyword, 'general_search' => 1));
            return;
        }
        $this->metaData['description'] = $model->description;
        $status_array = array('Request', 'Incomplete', 'Uploaded');

        if ($model->upload_status != "Published") {
            if (isset($_GET['token']) && $model->token == $_GET['token']) {

            } else {

                $form = new SearchForm;
                $keyword = $id;
                $this->render('invalid', array('model' => $form, 'keyword' => $keyword));
                return;
            }
        }

		$urlToRedirect = trim($model->getUrlToRedirectAttribute());
		$currentAbsoluteFullUrl = Yii::app()->request->getBaseUrl(true) . Yii::app()->request->url ;

		if($urlToRedirect && $currentAbsoluteFullUrl == $urlToRedirect ) {
			$this->metaData['redirect'] = 'http://dx.doi.org/10.5524/'.$model->identifier ;
			$this->render('interstitial',array(
				'model'=>$model
			));
			return;
		}
        $crit = new CDbCriteria;
        $crit->addCondition("t.dataset_id = ".$model->id);
        $crit->select = '*';
        $crit->join = "LEFT JOIN dataset ON dataset.id = t.dataset_id LEFT JOIN file_type ft ON t.type_id = ft.id
                LEFT JOIN file_format ff ON t.format_id = ff.id";

        $cookies = Yii::app()->request->cookies;
        // file
        $setting = array('name','size', 'type_id', 'format_id', 'location', 'date_stamp','sample_id'); // 'description','attribute' are hidden by default
        $pageSize = 10;
        $flag=null;
        
        if(isset($cookies['file_setting'])) {
            //$ss = json_decode($cookies['sample_setting']);
            $fcookie = $cookies['file_setting'];
            $fcookie = json_decode($fcookie->value, true);
            if($fcookie['setting'])
                $setting = $fcookie['setting'];
            $pageSize = $fcookie['page'];
        }

        if(isset($_POST['setting'])) {
            $setting = $_POST['setting'];
            $pageSize = $_POST['pageSize'];

            if(isset($cookies['file_setting']))
                unset(Yii::app()->request->cookies['file_setting']);

            $nc = new CHttpCookie('file_setting', json_encode(array('setting'=> $setting, 'page'=>$pageSize)));
            $nc->expire = time() + (60*60*24*30);
            Yii::app()->request->cookies['file_setting'] = $nc;
            $flag="file";
        }

        if($model->id == 629)
        {             
        $files=null;           
        }else{          
        $files = new CActiveDataProvider('File' , array(
            'criteria'=> $crit,
            'sort' => array('defaultOrder'=>'name ASC',
                            'attributes' => array(
                                'name',
                                'description',
                                'size',
                                'type_id' => array('asc'=>'ft.name asc', 'desc'=>'ft.name desc'),
                                'format_id' => array('asc'=>'ff.name asc', 'desc'=>'ff.name desc'),
                                'date_stamp',
                            )),
            'pagination' => array('pageSize'=>$pageSize)
        ));
        }

        //Sample
        $columns = array('name', 'taxonomic_id', 'genbank_name', 'scientific_name', 'common_name', 'attribute');
        $perPage = 10;
        if(isset($cookies['sample_setting'])) {
            //$ss = json_decode($cookies['sample_setting']);
            $scookie = $cookies['sample_setting'];
            $scookie = json_decode($scookie->value, true);
            if($scookie['columns'])
                $columns = $scookie['columns'];
            $perPage = $scookie['page'];
        }

        if(isset($_POST['columns'])) {
            $columns = $_POST['columns'];
            $perPage = $_POST['perPage'];
            $flag="sample";
            if(isset($cookies['sample_setting']))
                unset(Yii::app()->request->cookies['sample_setting']);

            $ncookie = new CHttpCookie('sample_setting', json_encode(array('columns'=> $columns, 'page'=>$perPage)));
            $ncookie->expire = time() + (60*60*24*30);
            Yii::app()->request->cookies['sample_setting'] = $ncookie;
        }

        $scrit = new CDbCriteria;
        $scrit->join = "LEFT JOIN dataset_sample ds ON ds.sample_id = t.id LEFT JOIN species ON t.species_id = species.id";
        $scrit->condition = "ds.dataset_id = :id";
        $scrit->params = array(':id' => $model->id);
        $samples = new CActiveDataProvider('Sample' , array(
            'criteria'=> $scrit,
            'pagination' => array('pageSize'=>$perPage),
            'sort' => array('defaultOrder'=>'t.name ASC',
                            'attributes' => array(
                                    'name',
                                    'common_name' => array(
                                            'asc' => 'species.common_name ASC',
                                            'desc' => 'species.common_name DESC',
                                        ),
                                    'genbank_name' => array(
                                            'asc' => 'species.genbank_name ASC',
                                            'desc' => 'species.genbank_name DESC',
                                        ),
                                    'scientific_name' => array(
                                            'asc' => 'species.scientific_name ASC',
                                            'desc' => 'species.scientific_name DESC',
                                        ),
                                    'taxonomic_id' => array(
                                            'asc' => 'species.tax_id ASC',
                                            'desc' => 'species.tax_id DESC',
                                        ),
                                )),
        ));

        $email = 'no_submitter@bgi.com';
        $result = Dataset::model()->findAllBySql("select email from gigadb_user g,dataset d where g.id=d.submitter_id and d.identifier='" . $id . "';");
        if (count($result) > 0) {
            $email = $result[0]['email'];
        }

        $result = Dataset::model()->findAllBySql("select identifier,title from dataset where identifier > '" . $id . "' and upload_status='Published' order by identifier asc limit 1;");
        if (count($result) == 0) {
            $result = Dataset::model()->findAllBySql("select identifier,title from dataset where upload_status='Published' order by identifier asc limit 1;");
            $next_doi = $result[0]->identifier;
            $next_title = $result[0]->title;
        } else {
            $next_doi = $result[0]->identifier;
            $next_title = $result[0]->title;
        }

        $result = Dataset::model()->findAllBySql("select identifier,title from dataset where identifier < '" . $id . "' and upload_status='Published' order by identifier desc limit 1;");
        if (count($result) == 0) {
            $result = Dataset::model()->findAllBySql("select identifier,title from dataset where upload_status='Published' order by identifier desc limit 1;");
            $previous_doi = $result[0]->identifier;
            $previous_title = $result[0]->title;
        } else {
            $previous_doi = $result[0]->identifier;
            $previous_title = $result[0]->title;
        }

        $attributes = $model->attributes;
        $l = array();
        foreach($attributes as $att) {
            $l[] = $att->id;
        }

        $authors = $model->authors;
        $at = array();
        foreach($authors as $au) {
            $at[] = $au->id;
        }

        $relateCriteria = new CDbCriteria;
        $relateCriteria->distinct = true;
        $relateCriteria->addNotInCondition("t.id", array($model->id));
        $relateCriteria->addCondition("t.upload_status = 'Published'");
        $relateCriteria->limit = 9;

        $rc = clone $relateCriteria;
        $rc->join = "JOIN dataset_attributes da ON t.id = da.dataset_id JOIN dataset_author au ON t.id = au.dataset_id";
        $rc->addInCondition("da.attribute_id", $l);
        $rc->addInCondition("au.author_id", $at, 'OR');
	$rc->addCondition("t.upload_status = 'Published'");
        $relates = Dataset::model()->findAll($rc);

        // if we don't find any dataset related by the first way, then by common type
        if (!$relates || count($relates) < 9) {

            $relatesIds = array($model->id);
            foreach ($relates as $relate) {
                $relatesIds[] = $relate->id;
            }

            $rc = clone $relateCriteria;
            $rc->join = "JOIN dataset_type dt ON t.id = dt.dataset_id";
            $rc->addInCondition("dt.type_id", $model->getTypeIds());
            $rc->addNotInCondition("t.id", $relatesIds);
            $rc->limit = 9 - count($relates);
            $relatesType = Dataset::model()->findAll($rc);

            foreach ($relatesType as $relate) {
                $relates[] = $relate;
            }
        }

        $scholar = $model->cited;

        $link_type = 'EBI';
        if(!Yii::app()->user->isGuest) {
            $user = User::model()->findByPk(Yii::app()->user->_id);
            if($user)
                $link_type = $user->preferred_link;
        }

        // Page private ? Disable robot to index
        $this->metaData['private'] = (Dataset::DATASET_PRIVATE == $model->upload_status);
               
        if($model->id == 629)
        {
            
            $this->render('viewfiles',array(
            'model'=>$model,
            'form'=>$form,
            'dataset'=>$dataset,
            'files'=>$files,
            'samples'=>$samples,
            'email' => $email,
            'previous_doi' => $previous_doi,
            'previous_title' => $previous_title,
            'next_title'=> $next_title,
            'next_doi' => $next_doi,
            'setting' => $setting,
            'columns' => $columns,
            'logs'=>$model->datasetLogs,
            'relates' => $relates,
            'scholar' => $scholar,
            'link_type' => $link_type,
            'flag' => $flag,
        ));
            
        }else{
        $this->render('view',array(
            'model'=>$model,
            'form'=>$form,
            'dataset'=>$dataset,
            'files'=>$files,
            'samples'=>$samples,
            'email' => $email,
            'previous_doi' => $previous_doi,
            'previous_title' => $previous_title,
            'next_title'=> $next_title,
            'next_doi' => $next_doi,
            'setting' => $setting,
            'columns' => $columns,
            'logs'=>$model->datasetLogs,
            'relates' => $relates,
            'scholar' => $scholar,
            'link_type' => $link_type,
            'flag' => $flag,
        ));
        }
    }


    public function actionPrivate() {
          $id = $_GET['identifier'];
          $model= Dataset::model()->find("identifier=?",array($id));
          if (!$model) {
            $this->redirect('/site/index');
          } else if ($model->upload_status == 'Published') {
            $this->redirect('/dataset/'.$model->identifier);
          }

          $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
          $model->token = substr(str_shuffle($chars),0,16);
          $model->save();

          $this->redirect('/dataset/view/id/'.$model->identifier.'/token/'.$model->token);
        }

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id) {
        $model = $this->loadModel($id);

        $dataProvider = new CActiveDataProvider('CurationLog', array(
            'criteria' => array(
                'condition' => "dataset_id=$id",
                'order' => 'id DESC',
            ),
        ));
        if (isset($_POST['Dataset'])) {
            
            if(isset($_POST['Dataset']['upload_status']) && $_POST['Dataset']['upload_status'] != $model->upload_status)            
            {
                CurationLog::createlog($_POST['Dataset']['upload_status'],$id,Yii::app()->user->id);              
            }
             if($_POST['Dataset']['curator_id'] != $model->curator_id)            
            {
                if($_POST['Dataset']['curator_id'] != "")
                {
                    $User1 = User::model()-> find('id=:id',array(':id'=>Yii::app()->user->id));
                    $username1 = $User1->first_name." ".$User1->last_name;
                    $User = User::model()-> find('id=:id',array(':id'=>$_POST['Dataset']['curator_id']));
                    $username = $User->first_name." ".$User->last_name;            
                    CurationLog::createlog_assign_curator($id,$username1,$username);                  
                    $model->curator_id = $_POST['Dataset']['curator_id'];
                }
                else{
                    
                    $model->curator_id = null;
                }

            }
            
            if($_POST['Dataset']['manuscript_id'])
            
            {
                $model->manuscript_id = $_POST['Dataset']['manuscript_id'];
                
            }else
            {
            
                $model->manuscript_id = null;
            }
            
            $datasetAttr = $_POST['Dataset'];

            $model->setAttributes($datasetAttr, true);

            if ($model->upload_status == 'Published') {
                $files = $model->files;
                if (strpos($model->ftp_site, "10.5524") == FALSE) {
                    $model->ftp_site="ftp://climb.genomics.cn/pub/10.5524/100001_101000/" . $model->identifier;

                    if (count($files) > 0) {
                        foreach ($files as $file) {
                            $origin_location = $file->location;
                            $new_location = "";
                            $location_array = explode("/", $origin_location);
                            $count = count($location_array);
                            if ($count == 1) {
                                $new_location = "ftp://climb.genomics.cn/pub/10.5524/100001_101000/" .
                                        $model->identifier . "/" . $location_array[0];
                            } else if ($count >= 2) {
                                $new_location = "ftp://climb.genomics.cn/pub/10.5524/100001_101000/" .
                                        $model->identifier . "/" . $location_array[$count - 2] . "/" . $location_array[$count - 1];
                            }
                            $file->location = $new_location;
                            $file->date_stamp = date("Y-m-d H:i:s");
                            if (!$file->save())
                                return false;
                        }
                    }
                }
            }

            // Image information
            $image = $model->image;
            $image->attributes = $_POST['Images'];
            $image->scenario = 'update';

            if ($model->publication_date == "")
                $model->publication_date = null;
            if ($model->modification_date == "")
                $model->modification_date = null;
            if ($model->fairnuse == "")
                $model->fairnuse = null;


            if ($model->save() && $image->save()) {
                if (isset($_POST['datasettypes'])) {
                    $datasettypes = $_POST['datasettypes'];
                }

                $datasetTypeMaps = DatasetType::model()->findAllByAttributes(array('dataset_id' => $id));

                for ($i = 0; $i < count($datasetTypeMaps); ++$i) {
                    $datasetTypeMap = $datasetTypeMaps[$i];
                    if ((isset($datasettypes) && !in_array($datasetTypeMap->type_id, array_keys($datasettypes), true)) || !isset($datasettypes)) {
                        $datasetTypeMap->delete();
                    }
                }

                if (isset($datasettypes)) {
                    foreach ($datasettypes as $datasetTypeId => $datasettype) {
                        $currDatasetTypeMap = DatasetType::model()->findByAttributes(array('dataset_id' => $model->id, 'type_id' => $datasetTypeId));
                        if (!$currDatasetTypeMap) {
                            $newDatasetTypeRelationship = new DatasetType;
                            $newDatasetTypeRelationship->dataset_id = $model->id;
                            $newDatasetTypeRelationship->type_id = $datasetTypeId;
                            $newDatasetTypeRelationship->save();
                        }
                    }
                }

                // semantic kewyords update, using remove all and re-create approach
				if( isset($_POST['keywords']) ){

					$sKeywordAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'keyword'));
					$keywordsArray = array_filter(explode(',', $_POST['keywords']));


					// remove existing dataset attributes
					$datasetAttributes = DatasetAttributes::model()->findAllByAttributes(array('dataset_id'=>$id,'attribute_id'=>$sKeywordAttr->id));

					foreach ($datasetAttributes as $key => $keyword) {
						$keyword->delete();
					}

					// create dataset attributes from form data
					if ( count($keywordsArray) > 0 ) {

						foreach ($keywordsArray as $keyword)
						{
							$dataset_attribute = new DatasetAttributes();
							$dataset_attribute->attribute_id = $sKeywordAttr->id;
							$dataset_attribute->dataset_id = $id;
							$dataset_attribute->value = $keyword;
							$dataset_attribute->save();
						}
					}
				}


				// retrieve existing redirect
				$criteria = new CDbCriteria(array('order'=>'id ASC'));
				$urlToRedirectAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'urltoredirect'));
				$urlToRedirectDatasetAttribute = datasetAttributes::model()->findByAttributes(array('dataset_id'=>$id,'attribute_id'=>$urlToRedirectAttr->id), $criteria);

				// saving url to redirect as a dataset attribute
				if( isset($urlToRedirectDatasetAttribute) || isset($_POST['urltoredirect'])  ){


					// update with value from form if value has changed.
					if (isset($urlToRedirectDatasetAttribute) && $_POST['urltoredirect'] != $urlToRedirectDatasetAttribute->value ) {
						$urlToRedirectDatasetAttribute->value = $_POST['urltoredirect'];
						$urlToRedirectDatasetAttribute->save();

					}

					// create a new dataset attribute if there isn't one
					else if ( isset($_POST['urltoredirect']) ){
						$urlToRedirectDatasetAttribute = new DatasetAttributes();
						$urlToRedirectDatasetAttribute->attribute_id = $urlToRedirectAttr->id;
						$urlToRedirectDatasetAttribute->dataset_id = $id;
						$urlToRedirectDatasetAttribute->value = $_POST['urltoredirect'];
						$urlToRedirectDatasetAttribute->save();
					}

				}



                if ($model->upload_status == 'Published') {
                    $this->redirect('/dataset/' . $model->identifier);
                } else {
                    $this->redirect(array('/dataset/view/id/' . $model->identifier.'/token/'.$model->token));
                }


            }
            else {
                Yii::log(print_r($model->getErrors(), true), 'debug');
            }

        }

        $this->render('update', array(
            'model' => $model,
            'curationlog'=>$dataProvider,
            'dataset_id'=>$id,
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
		$dataProvider=new CActiveDataProvider('Dataset');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Dataset('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Dataset'])) {
            $model->setAttributes($_GET['Dataset']);
        }

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function actionUpload() {
		if (isset($_POST['userId'])) {
			$user = User::model()->findByPk(Yii::app()->user->id);


			$excelFile = CUploadedFile::getInstanceByName('xls');
            // print_r($excelFile);die;
			$excelTempFileName = $excelFile->tempName;

			// email fields: to, from, subject, and so on
		    $from = Yii::app()->params['app_email_name']." <".Yii::app()->params['app_email'].">";
		    $to = Yii::app()->params['adminEmail'];
		    $subject = "New dataset uploaded by user ".$user->id." - ".$user->first_name.' '.$user->last_name;
		    $receiveNewsletter = $user->newsletter ? 'Yes' : 'No';
		    $message = <<<EO_MAIL

New dataset is uploaded by:
<br/>
<br/>
Id:  <b>{$user->id}</b>
<br/>
Email: <b>{$user->email}</b>
<br/>
First Name:  <b>{$user->first_name}</b>
<br/>
Last Name:  <b>{$user->last_name}</b>
<br/>
Affiliation:  <b>{$user->affiliation}</b>
<br/>
Receiving Newsletter:  <b>{$receiveNewsletter}</b>
<br/><br/>
EO_MAIL;

		    $headers = "From: $from";

		    /* prepare attachments */

			// boundary
		    $semi_rand = md5(time());
		    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

		    // headers for attachment
		    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
		     // multipart boundary
		    $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" ."Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
		    $message .= "--{$mime_boundary}\n";
            $fp =    @fopen($excelTempFileName,"rb");
	        $data =    @fread($fp,filesize($excelTempFileName));
            @fclose($fp);
            $data = chunk_split(base64_encode($data));
            // $newFileName = 'dataset_upload_'.$user->id.'.xls';
            $newFileName = $excelFile->name;
            $message .= "Content-Type: application/octet-stream; name=\"".$newFileName."\"\n" .
            "Content-Description: ".$newFileName."\n" ."Content-Disposition: attachment;\n" . " filename=\"".$newFileName."\"; size=".filesize($excelTempFileName).";\n" ."Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";

            $message .= "--{$mime_boundary}--";
		    $returnpath = "-f" . Yii::app()->params['adminEmail'];

	        $ok = @mail($to, $subject, $message, $headers, $returnpath);

	        if ($ok)  {
	        	$this->redirect('/dataset/upload/status/successful');
	        	return;
	        } else {
	        	$this->redirect('/dataset/upload/status/failed');
	        	return;
	        }
		}
		$this->render('upload');
	}


 public function actionSubmit() {
           if (isset($_POST['File'])) {
            $count = count($_POST['File']);
            //var_dump('count'.$count);
            for ($i = 0; $i < $count; $i++) {
                $id=$_POST['File'][$i]['id'];
                $model = File::model()->findByPk($id);
                if ($model === null)
                         continue;
                $model->attributes = $_POST['File'][$i];
                if ($model->date_stamp == "")
                    $model->date_stamp = NULL;
               // var_dump($model->description);
                if (!$model->save()) {
                    var_dump($_POST['File'][$i]);
                }
            }
        }

        if (!isset($_GET['id'])) {
                $this->redirect("/user/view_profile");
        } else {
            $dataset_id = $_GET['id'];
            $dataset = Dataset::model()->findByPk($dataset_id);
            if(!$dataset) {
                 Yii::app()->user->setFlash('keyword', "Cannot find dataset");
                $this->redirect("/user/view_profile");
            }

            if($dataset->submitter_id != Yii::app()->user->id) {
                Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                $this->redirect("/user/view_profile");
            }

            //change dataset status to Request
            $samples =  DatasetSample::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'sample_id asc'));

            $sampleLink = "";
            if ($samples != null) {
                $sampleLink .= "Samples:<br/>";
                foreach ($samples as $sample) {
                    $sampleLink = $sampleLink . Yii::app()->params['home_url'] . "/adminSample/view/id/" . $sample->sample_id . "<br/>";
                }
            }

            $isOld = 1;
            if($dataset->upload_status == 'Incomplete') {
                $isOld = 0;
            }

            //change the upload status
            $fileLink = "";
            if (isset($_POST['file'])) {
                $fileLink .= 'Files:<br/>';
                $fileLink = $link = Yii::app()->params['home_url'] . "/dataset/updateFile/?id=" . $dataset_id;
                  $dataset->upload_status = 'Pending';
                  CurationLog::createlog($dataset->upload_status,$dataset->id,Yii::app()->user->id);
            } else {
                  if($dataset->upload_status !== 'Request')
                    {
                        $dataset->upload_status = 'Request';
                        CurationLog::createlog($dataset->upload_status,$dataset->id,Yii::app()->user->id);
                    }
            }

            if (!$dataset->save()){
                Yii::app()->user->setFlash('keyword', "Submit failure" . $dataset_id);
                $this->redirect("/user/view_profile");
                return;
            }
        }

        $link = Yii::app()->params['home_url'] . "/dataset/update/id/" . $dataset_id;
        $linkFolder ="Link File Folder:<br/>";
        $linkFolder .= (Yii::app()->params['home_url'] . "/adminFile/linkFolder/?id=".$dataset_id);
        $user = User::model()->findByPk(Yii::app()->user->id);

        $from = Yii::app()->params['app_email_name'] . " <" . Yii::app()->params['app_email'] . ">";
        $ok1 = false;
        $ok2 = false;

        if (!$isOld) {
            $to = Yii::app()->params['adminEmail'];

            $subject = "New dataset " . $dataset_id . " submitted online by user " . $user->id . " - " . $user->first_name . ' ' . $user->last_name;
            $receiveNewsletter = $user->newsletter ? 'Yes' : 'No';
            $date = getdate();

            $message = <<<EO_MAIL

New dataset is submitted by:
<br/>
<br/>
User:  <b>{$user->id}</b>
<br/>
Email: <b>{$user->email}</b>
<br/>
First Name:  <b>{$user->first_name}</b>
<br/>
Last Name:  <b>{$user->last_name}</b>
<br/>
Affiliation:  <b>{$user->affiliation}</b>
<br/>
Receiving Newsletter:  <b>{$receiveNewsletter}</b>
<br/>
Submission ID: <b>$dataset_id</b><br/>
$link
<br/>
$sampleLink
    <br/>
$linkFolder
        <br/>

EO_MAIL;
            $headers = "Fcrrom: $from";

            /* prepare attachments */

// boundary
            $semi_rand = md5(time());
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

// headers for attachment
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
// multipart boundary
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
            $message .= "--{$mime_boundary}\n";

            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . Yii::app()->params['adminEmail'];

            $ok1 = @mail($to, $subject, $message, $headers, $returnpath);

            //send email to user to

            $to = $user->email;

            $subject = "GigaDB submission \"" . $dataset->title . '"'.' ['.$dataset_id.']';
            $receiveNewsletter = $user->newsletter ? 'Yes' : 'No';
            $timestamp = $date['mday'] . "-" . $date['mon'] . "-" . $date['year'];
            $message = <<<EO_MAIL
Dear $user->first_name $user->last_name,<br/>

Thank you for submitting your dataset information to GigaDB.
Our curation team will contact you shortly regarding your
submission "$dataset->title".<br/>
<br/>
In the meantime, please contact us at <a href="mailto:database@gigasciencejournal.com">database@gigasciencejournal.com</a> with any questions.<br/>
<br/>
Best regards,<br/>
<br/>
The GigaDB team<br/>
<br/>
Submission date: $timestamp
<br/>
EO_MAIL;

            $headers = "From: $from";

            /* prepare attachments */

// boundary
            $semi_rand = md5(time());
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

// headers for attachment
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
// multipart boundary
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
            $message .= "--{$mime_boundary}\n";

            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . Yii::app()->params['adminEmail'];

            $ok2 = @mail($to, $subject, $message, $headers, $returnpath);
        } else {
            $to = Yii::app()->params['adminEmail'];

            $subject = "Dataset " . $dataset_id . " updated online by user " . $user->id . " - " . $user->first_name . ' ' . $user->last_name;
            $receiveNewsletter = $user->newsletter ? 'Yes' : 'No';
            $date = getdate();
            $adminFileLink = Yii::app()->params['home_url'] . "/adminFile/update1/?id=" .$dataset_id;
            $message = <<<EO_MAIL
Dataset is updated by:
<br/>
<br/>
User:  <b>{$user->id}</b>
<br/>
Email: <b>{$user->email}</b>
<br/>
First Name:  <b>{$user->first_name}</b>
<br/>
Last Name:  <b>{$user->last_name}</b>
<br/>
Affiliation:  <b>{$user->affiliation}</b>
<br/>
Receiving Newsletter:  <b>{$receiveNewsletter}</b>
<br/>
Submission ID: <b>$dataset_id</b><br/>
$link
<br/>
$adminFileLink
    <br/>
EO_MAIL;

            $headers = "From: $from";

            /* prepare attachments */

// boundary
            $semi_rand = md5(time());
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

// headers for attachment
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
// multipart boundary
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
            $message .= "--{$mime_boundary}\n";

            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . Yii::app()->params['adminEmail'];

            $ok1 = @mail($to, $subject, $message, $headers, $returnpath);

            //send email to user to

            $to = $user->email;

          //  $subject = "GigaDB update \"" . $dataset->title . '"';
            $subject = "GigaDB submission \"" . $dataset->title . '"'.' ['.$dataset_id.']';
            $receiveNewsletter = $user->newsletter ? 'Yes' : 'No';
            $timestamp = $date['mday'] . "-" . $date['mon'] . "-" . $date['year'];
            $message = <<<EO_MAIL
Dear $user->first_name $user->last_name,<br/>

Thank you for updating your dataset information to GigaDB.
Our curation team will contact you shortly regarding your
updates "$dataset->title".<br/>
<br/>
In the meantime, please contact us at <a href="mailto:database@gigasciencejournal.com">database@gigasciencejournal.com</a> with any questions.<br/>
<br/>
Best regards,<br/>
<br/>
The GigaDB team<br/>
<br/>
Submission date: $timestamp
<br/>
EO_MAIL;

            $headers = "From: $from";

            /* prepare attachments */

// boundary
            $semi_rand = md5(time());
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

// headers for attachment
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
// multipart boundary
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
            $message .= "--{$mime_boundary}\n";

            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . Yii::app()->params['adminEmail'];

            $ok2 = @mail($to, $subject, $message, $headers, $returnpath);
        }

        if ($ok1 && $ok2) {
            $uploadedDatasets = Dataset::model()->findAllByAttributes(array('submitter_id' => Yii::app()->user->id));
            $this->render("upload", array('study' => $dataset_id, 'uploadedDatasets' => $uploadedDatasets));
        } else {
            //add something
            $uploadedDatasets = Dataset::model()->findAllByAttributes(array('submitter_id' => Yii::app()->user->id));
            $this->render("upload", array('study' => $dataset_id, 'uploadedDatasets' => $uploadedDatasets));
        }
    }


    public function actionCreate(){
        $dataset = new Dataset;
        $dataset->image = new Images;

        if(isset($_POST['Dataset']) && isset($_POST['Images'])){
            $dataset->attributes=$_POST['Dataset'];
            $dataset->image->attributes = $_POST['Images'];

            if ($dataset->publication_date == "")
                    $dataset->publication_date = null;
            if ($dataset->modification_date == "")
                    $dataset->modification_date = null;

            if ($dataset->image->validate('update') && $dataset->validate('update') && $dataset->image->save()) {
                // save image
                $dataset->image_id = $dataset->image->id;

                // save dataset
                if ($dataset->save()) {
                    // link datatypes
                    if (isset($_POST['datasettypes'])) {
                        $datasettypes = $_POST['datasettypes'];
                        foreach ($datasettypes as $id => $datasettype) {
                            $newDatasetTypeRelationship = new DatasetType;
                            $newDatasetTypeRelationship->dataset_id = $dataset->id;
                            $newDatasetTypeRelationship->type_id = $id;
                            $newDatasetTypeRelationship->save();
                        }
                    }


                    Yii::app()->user->setFlash('saveSuccess', 'saveSuccess');
                    if ($dataset->upload_status=='Pending') {
                      $this->redirect('/dataset/private/identifier/'.$dataset->identifier);
                    } else {
                      $this->redirect(array('/dataset/'.$dataset->identifier));
                    }
                }
            }
        }
        $this->render('create', array('model'=>$dataset)) ;
    }

        public function actionUpdateSubmit() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $identifier = Dataset::model()->findByAttributes(array('id' => $id))->identifier;
            $dataset_session = DatasetSession::model()->findByAttributes(array('identifier' => $identifier));
            if ($dataset_session == NULL)
                return $this->redirect("/user/view_profile");
            $vars = array('dataset', 'images', 'identifier', 'dataset_id',
                'datasettypes', 'authors', 'projects',
                'links', 'externalLinks', 'relations', 'samples');
            foreach ($vars as $var) {
                $_SESSION[$var] = CJSON::decode($dataset_session->$var);
            }
            //indicate that this is an old dataset
            $_SESSION['isOld'] = 1;

            $this->redirect("/dataset/create1");
        }
        Yii::app()->user->setFlash('keyword', 'no dataset is specified');
        return $this->redirect("/user/view_profile");
    }


    public function actionUpdateFile() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $user = User::model()->findByPk(Yii::app()->user->id);
            $dataset = Dataset::model()->findByattributes(array('id' => $id));
            if ($user->id != $dataset->submitter_id) {
                return false;
            }
            $identifier = $dataset->identifier;
            $dataset_session = DatasetSession::model()->findByAttributes(array('identifier' => $identifier));
            if ($dataset_session == NULL)
                return $this->redirect("/user/view_profile");
            $vars = array('dataset', 'images', 'identifier', 'dataset_id',
                'datasettypes', 'authors', 'projects',
                'links', 'externalLinks', 'relations', 'samples');
            foreach ($vars as $var) {
                $_SESSION[$var] = CJSON::decode($dataset_session->$var);
            }
            $_SESSION['isOld'] = 1;
            $this->redirect("/adminFile/create1");
        }
        Yii::app()->user->setFlash('keyword', 'no dataset is specified');
        return $this->redirect("/user/view_profile");
    }



/**
 *	actionMint
 *	post metadata, mint a new DOI
 */
	public function actionMint() {

        $result['status'] = false;
		$status_array = array('Request', 'Incomplete', 'Uploaded');

		$mds_metadata_url="https://mds.datacite.org/metadata";
		$mds_doi_url="https://mds.datacite.org/doi";

		$mds_username = Yii::app()->params['mds_username'];
		$mds_password = Yii::app()->params['mds_password'];
		$mds_prefix = Yii::app()->params['mds_prefix'];

		if(isset($_POST['doi'])){

			$doi = $_POST['doi'];
			if(stristr($doi, "/")){
				$temp = explode("/", $doi);
				$doi = $temp[1];
			}

			$doi = trim($doi);
			$dataset = Dataset::model()->find("identifier=?",array($doi));

			if ( $dataset && ! in_array($dataset->upload_status, $status_array) ) {

				$xml_data = $dataset->toXML();
				$ch= curl_init();
				curl_setopt($ch, CURLOPT_URL, $mds_metadata_url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/xml;charset=UTF-8'));
				curl_setopt($ch, CURLOPT_USERPWD, $mds_username . ":" . $mds_password);
				$curl_response = curl_exec($ch);
				$result['md_curl_response'] = $curl_response;
				$info1 = curl_getinfo($ch);
				$result['md_curl_status'] = $info1['http_code'];
				curl_close ($ch) ;

			}
                
			if ( $dataset && $result['md_curl_status'] == 201) {
				$doi_data = "doi=".$mds_prefix."/".$doi."\n"."url=http://gigadb.org/dataset/".$dataset->identifier ;
				$result['doi_data']  = $doi_data;
				$ch2= curl_init();
				curl_setopt($ch2, CURLOPT_URL, $mds_doi_url);
				curl_setopt($ch2, CURLOPT_POST, 1);
				curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch2, CURLOPT_POSTFIELDS, $doi_data);
				curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Content-Type:application/xml;charset=UTF-8'));
				curl_setopt($ch2, CURLOPT_USERPWD, $mds_username . ":" . $mds_password);
				$curl_response = curl_exec($ch2);
				$result['doi_curl_response'] = $curl_response;
				$info2 = curl_getinfo($ch2);
				$result['doi_curl_status'] = $info2['http_code'];
				curl_close ($ch2) ;
			}

			if (isset($result['doi_curl_status']) && $result['doi_curl_status'] == 201) {
				$result['status'] = true;
			}

		}

		echo json_encode($result);
		Yii::app()->end();
	}

     public function storeDataset(&$dataset) {
        $dataset_id = 0;
        $identifier = 0;
        if (isset($_SESSION['dataset']) && isset($_SESSION['images'])) {

            if (isset($_SESSION['identifier'])) {
                $identifier = $_SESSION['identifier'];
                $dataset = Dataset::model()->findByAttributes(array('identifier' => $identifier));
            }

            /*else {
                $file = fopen(Yii::app()->basePath."/scripts/data/lastdoi.txt", 'r');
                $test=fread($file, filesize(Yii::app()->basePath."/scripts/data/lastdoi.txt"));
                $file1 = fopen(Yii::app()->basePath."/scripts/data/lastdoi.txt", 'w');
                fwrite($file1, $test+1);
                fclose($file);
                fclose($file1);

                $identifier=$test;
            }*/

            $dataset->attributes = $_SESSION['dataset'];


            $dataset->identifier = $identifier;

            $dataset->dataset_size = $_SESSION['dataset']['dataset_size'];
            if ($dataset->dataset_size == '')
                $dataset->dataset_size = 0;
            $dataset->ftp_site = "''";

            $dataset->submitter_id = Yii::app()->user->_id;
            if ($dataset == null)
                return;
            try {
                if ($dataset->validate()
                ) {
                    if ($_SESSION['images'] != 'no-image') {
                        $dataset->image->attributes = $_SESSION['images'];
                        if (!( $dataset->image->validate() && $dataset->image->save() ))
                            return false;
                        $dataset->image_id = $dataset->image->id;
                    }
                    else {
                        //
                        $dataset->image->url="http://gigadb.org/images/data/cropped/no_image.png";
                        $dataset->image->location="no_image.jpg";
                        $dataset->image->tag="no image icon";
                        $dataset->image->license="Public domain";
                        $dataset->image->photographer="GigaDB";
                        $dataset->image->source="GigaDB";
                        //
                        if (isset($_SESSION['datasettypes'])) {
                            $datasettypes = $_SESSION['datasettypes'];
                            if (count($datasettypes) == 1) {
                                foreach ($datasettypes as $id => $datasettype)
                                    $type_id = $id;
                                //workflow
                                if ($type_id == 5) {
                                    $dataset->image->url="http://gigadb.org/images/data/cropped/workflow.jpg";
                                    $dataset->image->location="workflow.jpg";
                                    $dataset->image->tag="workflow icon";
                                    $dataset->image->license="Public domain";
                                    $dataset->image->photographer="GigaDB";
                                    $dataset->image->source="GigaDB";
                                } else if ($type_id == 2
                                ) {
                                    //genomics
                                   $dataset->image->url="http://gigadb.org/images/data/cropped/genomics.jpg";
                                    $dataset->image->location="genomics.jpg";
                                    $dataset->image->tag="genomic icon";
                                    $dataset->image->license="Public domain";
                                    $dataset->image->photographer="GigaDB";
                                    $dataset->image->source="GigaDB";
                                } else if ($type_id == 4) {
                                    //transcriptomics
                                    $dataset->image->url="http://gigadb.org/images/data/cropped/transcriptomics.jpg";
                                    $dataset->image->location="transcriptomics.jpg";
                                    $dataset->image->tag="transcriptomics icon";
                                    $dataset->image->license="Public domain";
                                    $dataset->image->photographer="GigaDB";
                                    $dataset->image->source="GigaDB";
                                }
                            }
                        }

                       if (!( $dataset->image->validate() && $dataset->image->save() ))
                            return false;

                        $dataset->image_id = $dataset->image->id;
                    }


                    if ($dataset->save()) {

                        $_SESSION['identifier'] = $identifier;
                        $_SESSION['dataset_id'] = $dataset->id;

                        $dataset_id = $dataset->id;
                        if (isset($_SESSION['datasettypes'])) {
                            $datasettypes = $_SESSION['datasettypes'];
                            foreach ($datasettypes as $id => $datasettype) {
                                $newDatasetTypeRelationship = new DatasetType;
                                $newDatasetTypeRelationship->dataset_id = $dataset->id;
                                $newDatasetTypeRelationship->type_id = $id;
                                $newDatasetTypeRelationship->save();
                            }
                        }
                    }
                    else
                        return false;

                    return true;
                }
                else {
                    return false;
                }
            } catch (Exception $e) {
                $dataset->addError('error', $e->getMessage());
                return false;
            }
        }
    }

    private function createManuScript($dataset_id , $doi , $pmid){

        if(empty($doi) && empty($pmid)){
            return ;
        }

        $manuscript = new Manuscript;
        if(!empty($doi)){
            $manuscript->identifier = $doi;
        }else{
            $manuscript->identifier = " ";
        }

        if(!empty($pmid)){
            $manuscript->pmid = $pmid;
        }

        $manuscript->dataset_id = $dataset_id;

        $manuscript->save(false);
    }

    private function setProject($dataset_id,$project){

        $new_project_url = $project['new_project_url'];
        $new_project_name = $project['new_project_name'];
        $new_project_image = $project['new_project_image'];

        $rows = max (count($new_project_url) , count($new_project_name) , count($new_project_image));
        for($i = 1; $i < $rows;$i++){
            $project_url = isset($new_project_url[$i])?$new_project_url[$i]:" ";
            $project_name = isset($new_project_name[$i])?$new_project_name[$i]:" ";
            $project_image = isset($new_project_image[$i])?$new_project_image[$i]:" ";

            $project = new Project;
            $project->url = $project_url;
            $project->name = $project_name;
            $project->image_location = $project_image;

            if($project->save(false)){
                $dataset_project = new DatasetProject;
                $dataset_project->dataset_id = $dataset_id;
                $dataset_project->project_id = $project->id;
                $dataset_project->save(false);
            }

        }

    }

    private function setAuthorList($dataset_id,$authors){


        $temp = explode(";", $authors);

        foreach ($temp as $key => $value) {
            $value=trim($value);

            if(strlen($value)>0){
                $author = Author::model()->find("name=?",array($value));
                if(!$author){ //  Author not found
                    $author = new Author;
                    $author->name =$value;
                    $author->orcid ="orcid";
                    $author->rank=0;
                    $author->save(true);
                }



                $dataset_author = new DatasetAuthor;
                $dataset_author->dataset_id = $dataset_id;
                $dataset_author->author_id = $author->id;

                $dataset_author->save(true);
            }
        }
    }

    private function setDatesetType($dataset_id,$dataset_types){
        $temp = explode(",", $dataset_types);

        foreach ($temp as $key => $value) {
            $value=trim($value);
            if(strlen($value)>0){
                $type = Type::model()->find("name=?",array($value));
                if(!$type){ // Type not found
                    $type = new Type;
                    $type->name = $value;
                    $type->description="description";
                    $type->save(true);

                }

                $dataset_type = new DatasetType;
                $dataset_type->dataset_id = $dataset_id;
                $dataset_type->type_id = $type->id;
                $dataset_type->save(false);

            }
        }

    }

    private function addExternalLink($dataset_id,$additional_information,$genome_browser){
        if(!empty($additional_information)){
            $external_link_type = ExternalLinkType::model()->find("name=?",array("additional_information"));

            $external_link = new ExternalLink;
            $external_link->dataset_id = $dataset_id;
            $external_link->external_link_type_id = $external_link_type->id;
            $external_link->url = $additional_information;

            $external_link->save(false);


        }

        if(!empty($genome_browser)){
            $external_link_type = ExternalLinkType::model()->find("name=?",array("genome_browser"));
            $external_link = new ExternalLink;
            $external_link->dataset_id = $dataset_id;
            $external_link->external_link_type_id = $external_link_type->id;
            $external_link->url = $genome_browser;

            $external_link->save(false);

        }

    }

    private function getFileType($type){

        $file_type = FileType::model()->find("name=?",array($type));
        if($file_type == null){
            $file_type = new FileType;

            $file_type->name= $type;
            $file_type->description = " ";

            $file_type->save(false);
        }

        return $file_type->id;
    }

    private function getFileFormat($format){

        $file_format = FileFormat::model()->find("name=?",array($format));
        if($file_format == null){
            $file_format = new FileFormat;

            $file_format->name= $format;
            $file_format->description = " ";

            $file_format->save(false);
        }

        return $file_format->id;
    }

    public function actioncheckDOIExist(){

        $result = array();
        $result['status'] = false;
        if(isset($_POST['doi'])){
            $doi = $_POST['doi'];
            if(stristr($doi, "/")){
                $temp = explode("/", $doi);
                $doi = $temp[1];
            }

            $doi = trim($doi);

            $dataset = Dataset::model()->find("identifier=?",array($doi));
            if($dataset){
                $result['status'] = true;
            }
        }
        echo json_encode($result);
    }

    private function userExist($email){
        $model = User::model()->find("email=?",array($email));
        if($model){
            return $model->id;
        }else {
            return false;
        }
    }

    private function sendHtmlEmailWithAttachment() {

    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=Dataset::model()->findByPk($id);
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
        if(isset($_POST['ajax']) && $_POST['ajax']==='dataset-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

        public function actionCreate1() {

            $dataset = new Dataset;
            $image = new Images;
            // set default types
            $dataset->types = array();

            if (isset($_POST['Dataset']) && isset($_POST['Images'])) {
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    #save dataset
                    $dataset->submitter_id = Yii::app()->user->_id;
                    $attrs = $_POST['Dataset'];
                    $dataset->title = $attrs['title'];
                    $dataset->description = $attrs['description'];
                    $dataset->upload_status = "Incomplete";
                    $dataset->ftp_site = "''";
                                     
                    // save dataset types
                    if (isset($_POST['datasettypes'])) {
                        $dataset->types = $_POST['datasettypes'];
                    }

                    $lastDataset = Dataset::model()->find(array('order'=>'identifier desc'));
                    $lastIdentifier = intval($lastDataset->identifier);

                    if(!is_int($lastIdentifier)) {
                        $transaction->rollback();
                        $this->redirect('/');
                    }

                    $dataset->identifier = $lastIdentifier + 1;

                    if($_POST['Dataset']['union']=='B') {
                        $dataset->dataset_size=$_POST['Dataset']['dataset_size'];
                    } else if($_POST['Dataset']['union']=='M') {
                        $dataset->dataset_size=$_POST['Dataset']['dataset_size']*1024*1024;
                    } else if($_POST['Dataset']['union']=='G') {
                        $dataset->dataset_size=$_POST['Dataset']['dataset_size']*1024*1024*1024;
                    } else if($_POST['Dataset']['union']=='T') {
                        $dataset->dataset_size=$_POST['Dataset']['dataset_size']*1024*1024*1024*1024;
                    }

                    #save image
                    if(!$_POST['Images']['is_no_image']) {
                        $uploadedFile = CUploadedFile::getInstance($image,'image_upload');
                        $fileName = "{$uploadedFile}";
                        $path = Yii::getPathOfAlias('webroot') ."/images/uploads/".$fileName;

                        $image->image_upload = $uploadedFile;
                        $image->url = $path;
                        $image->location = $fileName;
                        $image->tag = $_POST['Images']['tag'];
                        $image->license = $_POST['Images']['license'];
                        $image->photographer = $_POST['Images']['photographer'];
                        $image->source = $_POST['Images']['source'];
                    } else {
                        $image->url="http://gigadb.org/images/data/cropped/no_image.png";
                        $image->location="no_image.jpg";
                        $image->tag="no image icon";
                        $image->license="Public domain";
                        $image->photographer="GigaDB";
                        $image->source="GigaDB";
                    }

                    if($dataset->save() && $image->save()) {

                        $dataset->image_id = $image->id;
                        $dataset->save(false);
                        
                                            // semantic kewyords update, using remove all and re-create approach
                        if( isset($_POST['keywords']) ){

		        $sKeywordAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'keyword'));
			$keywordsArray = array_filter(explode(',', $_POST['keywords']));


			// remove existing dataset attributes
			$datasetAttributes = datasetAttributes::model()->findAllByAttributes(array('dataset_id'=>$dataset->id,'attribute_id'=>$sKeywordAttr->id));

			foreach ($datasetAttributes as $key => $keyword) {
                            $keyword->delete();
			}

			// create dataset attributes from form data
			if ( count($keywordsArray) > 0 ) {

                            foreach ($keywordsArray as $keyword)
				{
                                    $dataset_attribute = new DatasetAttributes();
                                    $dataset_attribute->attribute_id = $sKeywordAttr->id;
                                    $dataset_attribute->dataset_id = $dataset->id;
                                    $dataset_attribute->value = $keyword;
                                    $dataset_attribute->save();
				}
                            }
			}

                        if (isset($_POST['datasettypes'])) {
                            $types = DatasetType::storeDatasetTypes($dataset->id, $_POST['datasettypes']);
                            if(!$types) {
                                $transaction->rollback();
                                $this->redirect('/');
                            }
                        }
                        $transaction->commit();
                        $this->redirect(array('/dataset/authorManagement', 'id'=>$dataset->id));
                    }

                } catch(Exception $e) {
                    $message = $e->getMessage();
                    Yii::log(print_r($message, true), 'error');
                    $transaction->rollback();
                    $this->redirect('/');
                }

            }

            $this->render('create1', array('model' => $dataset, 'image'=>$image));
        }

        public function actionDatasetManagement() {
            if (!isset($_GET['id'])) {
                $this->redirect("/user/view_profile");
            } else {
                $dataset = Dataset::model()->findByPk($_GET['id']);

                if(!$dataset) {
                    $this->redirect("/user/view_profile");
                }

                // set dataset types
                 $dataset->types = $dataset->typeIds;

                if($dataset->submitter_id != Yii::app()->user->id) {
                    Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                    $this->redirect("/user/view_profile");
                 }

                if(!$dataset->image) {
                    $image = new Images;
                  } else {
                    $image = $dataset->image;
                  }

                  $is_new_image = $image->isNewRecord;

                if (isset($_POST['Dataset']) && isset($_POST['Images']))  {
                    $transaction = Yii::app()->db->beginTransaction();
                    try {
                        $attrs = $_POST['Dataset'];
                        $dataset->title = $attrs['title'];
                        $dataset->description = $attrs['description'];
                        // save dataset types
                        if (isset($_POST['datasettypes'])) {
                            $dataset->types = $_POST['datasettypes'];
                        }


                        if($_POST['Dataset']['union']=='B') {
                            $dataset->dataset_size=$_POST['Dataset']['dataset_size'];
                        } else if($_POST['Dataset']['union']=='M') {
                            $dataset->dataset_size=$_POST['Dataset']['dataset_size']*1024*1024;
                        } else if($_POST['Dataset']['union']=='G') {
                            $dataset->dataset_size=$_POST['Dataset']['dataset_size']*1024*1024*1024;
                        } else if($_POST['Dataset']['union']=='T') {
                            $dataset->dataset_size=$_POST['Dataset']['dataset_size']*1024*1024*1024*1024;
                        }

                        #save image
                        if(!$_POST['Images']['is_no_image']) {
                            $uploadedFile = CUploadedFile::getInstance($image,'image_upload');
                            $fileName = "{$uploadedFile}";
                            $path = Yii::getPathOfAlias('webroot') ."/images/uploads/".$fileName;

                            $image->image_upload = $uploadedFile;
                            $image->url = $path;
                            $image->location = $fileName;
                            $image->tag = $_POST['Images']['tag'];
                            $image->license = $_POST['Images']['license'];
                            $image->photographer = $_POST['Images']['photographer'];
                            $image->source = $_POST['Images']['source'];
                        } else {
                            $image->url="http://gigadb.org/images/data/cropped/no_image.png";
                            $image->location="no_image.jpg";
                            $image->tag="no image icon";
                            $image->license="Public domain";
                            $image->photographer="GigaDB";
                            $image->source="GigaDB";
                        }

                        if($dataset->save() && $image->save()) {
                            
                            if( isset($_POST['keywords']) ){

		        $sKeywordAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'keyword'));
			$keywordsArray = array_filter(explode(',', $_POST['keywords']));


			// remove existing dataset attributes
			$datasetAttributes = DatasetAttributes::model()->findAllByAttributes(array('dataset_id'=>$dataset->id,'attribute_id'=>$sKeywordAttr->id));

			foreach ($datasetAttributes as $key => $keyword) {
                            $keyword->delete();
			}

			// create dataset attributes from form data
			if ( count($keywordsArray) > 0 ) {

                            foreach ($keywordsArray as $keyword)
				{
                                    $dataset_attribute = new DatasetAttributes();
                                    $dataset_attribute->attribute_id = $sKeywordAttr->id;
                                    $dataset_attribute->dataset_id = $dataset->id;
                                    $dataset_attribute->value = $keyword;
                                    $dataset_attribute->save();
				}
                            }
			}

                            if($is_new_image) {
                                $dataset->image_id = $image->id;
                                $dataset->save(false);
                            }

                            if (isset($_POST['datasettypes'])) {
                                $types = DatasetType::storeDatasetTypes($dataset->id, $_POST['datasettypes']);
                                if(!$types) {
                                    $transaction->rollback();
                                    $this->redirect('/');
                                }
                            }
                            $transaction->commit();
                            $this->redirect(array('/dataset/authorManagement', 'id'=>$dataset->id));
                        }

                    } catch(Exception $e) {
                        $message = $e->getMessage();
                        Yii::log(print_r($message, true), 'error');
                        $transaction->rollback();
                        $this->redirect('/');
                    }
                }

                $this->render('datasetManagement', array('model' => $dataset,'image'=>$image));
            }
        }

        public function actionAuthorManagement() {
            if (!isset($_GET['id'])) {
                $this->redirect("/user/view_profile");
            } else {
                $dataset = Dataset::model()->findByPk($_GET['id']);
                if(!$dataset) {
                    $this->redirect("/user/view_profile");
                }

                if($dataset->submitter_id != Yii::app()->user->id) {
                    Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                    $this->redirect("/user/view_profile");
                 }

                $das = DatasetAuthor::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'rank asc'));

                $this->render('authorManagement', array('model' => $dataset,'das'=>$das));
            }

        }

        public function actionProjectManagement() {
            if (!isset($_GET['id'])) {
                $this->redirect("/user/view_profile");
            } else {
                $dataset = Dataset::model()->findByPk($_GET['id']);
                if(!$dataset) {
                    $this->redirect("/user/view_profile");
                }

                if($dataset->submitter_id != Yii::app()->user->id) {
                    Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                    $this->redirect("/user/view_profile");
                 }

                $dps = DatasetProject::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

                $this->render('projectManagement', array('model' => $dataset,'dps'=>$dps));
            }
        }

        public function actionLinkManagement() {
            if (!isset($_GET['id'])) {
                $this->redirect("/user/view_profile");
            } else {
                $dataset = Dataset::model()->findByPk($_GET['id']);
                if(!$dataset) {
                    $this->redirect("/user/view_profile");
                }

                if($dataset->submitter_id != Yii::app()->user->id) {
                    Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                    $this->redirect("/user/view_profile");
                 }

                $links = Link::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

                 $link_database = Yii::app()->db->createCommand()
                    ->select("prefix")
                    ->from("prefix")
                    ->order("prefix asc")
                    ->group("prefix")
                    ->queryAll();

                $this->render('linkManagement', array('model' => $dataset,'links'=>$links,'link_database' => $link_database));
            }
        }

        public function actionExLinkManagement() {
            if (!isset($_GET['id'])) {
                $this->redirect("/user/view_profile");
            } else {
                $dataset = Dataset::model()->findByPk($_GET['id']);
                if(!$dataset) {
                    $this->redirect("/user/view_profile");
                }

                if($dataset->submitter_id != Yii::app()->user->id) {
                    Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                    $this->redirect("/user/view_profile");
                 }

                $exLinks = ExternalLink::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

                $this->render('exLinkManagement', array('model' => $dataset,'exLinks'=>$exLinks));
            }
        }

        public function actionRelatedDoiManagement() {
            if (!isset($_GET['id'])) {
                $this->redirect("/user/view_profile");
            } else {
                $dataset = Dataset::model()->findByPk($_GET['id']);
                if(!$dataset) {
                    $this->redirect("/user/view_profile");
                }

                if($dataset->submitter_id != Yii::app()->user->id) {
                    Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                    $this->redirect("/user/view_profile");
                 }

                $relations = Relation::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'related_doi asc'));

                $this->render('relatedDoiManagement', array('model' => $dataset,'relations'=>$relations));
            }
        }

        public function actionSampleManagement() {
            if (!isset($_GET['id'])) {
                $this->redirect("/user/view_profile");
            } else {
                $dataset = Dataset::model()->findByPk($_GET['id']);
                if(!$dataset) {
                    $this->redirect("/user/view_profile");
                }

                if($dataset->submitter_id != Yii::app()->user->id) {
                    Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                    $this->redirect("/user/view_profile");
                 }

                $dss = DatasetSample::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'sample_id asc'));

                $this->render('sampleManagement', array('model' => $dataset,'dss'=>$dss));
            }
        }

        public function actionPxInfoManagement() {
            if (!isset($_GET['id'])) {
                $this->redirect("/user/view_profile");
            } else {
                $dataset = Dataset::model()->findByPk($_GET['id']);
                if(!$dataset) {
                    Yii::log('dataset id not found', 'debug');
                    $this->redirect("/user/view_profile");
                }

                if($dataset->submitter_id != Yii::app()->user->id) {
                    Yii::log('not the owner', 'debug');
                    Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
                    $this->redirect("/user/view_profile");
                 }

                # load attributes
                $keywordsAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'PX_keywords'));
                $sppAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'PX_sample_processing_protocol'));
                $dppAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'PX_data_processing_protocol'));
                $expTypeAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'PX_experiment_type'));
                $instrumentAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'PX_instrument'));
                $quantificationAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'PX_quantification'));
                $modificationAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'PX_modification'));

                if(!$keywordsAttr or !$sppAttr or !$dppAttr or !$expTypeAttr or !$instrumentAttr or !$quantificationAttr or !$modificationAttr) {
                    Yii::app()->user->setFlash('keyword', "Attr cannot be found.");
                    Yii::log("Attr cannot be found.", 'debug');
                    $this->redirect("/user/view_profile");
                }

                # create new pxForm for validation and store px info into pxForm
                $pxForm = new PxInfoForm;

                # load keywords
                $keywords = DatasetAttributes::model()->findByAttributes(array('dataset_id'=>$dataset->id, 'attribute_id'=>$keywordsAttr->id));
                if(!$keywords) {
                    $keywords = new DatasetAttributes;
                    $keywords->dataset_id = $dataset->id;
                    $keywords->attribute_id = $keywordsAttr->id;
                }
                $pxForm->keywords = $keywords->value;

                # load sample processing protocol
                $criteria = new CDbCriteria;
                $criteria->join = 'LEFT JOIN sample s on (t.sample_id = s.id) LEFT JOIN dataset_sample ds on (ds.sample_id = s.id)';
                $criteria->addCondition('ds.dataset_id = '.$dataset->id);
                $criteria->addCondition('t.attribute_id = '.$sppAttr->id);
                $spp = SampleAttribute::model()->find($criteria);

                if($spp) {
                    # get one spp
                    $pxForm->spp = $spp->value;
                }

                # load experiment first, if not create one
                $experiment = Experiment::model()->findByAttributes(array('dataset_id'=>$dataset->id));
                if(!$experiment) {
                    #create new experiment
                    $experiment = new Experiment;
                    $experiment->experiment_type = 'proteomic';
                    $experiment->experiment_name = 'PX "'.$dataset->title.'"';
                    $experiment->dataset_id = $dataset->id;
                    $experiment->save(false);
                }

                # load data processing protocol
                $dpp = ExpAttributes::model()->findByAttributes(array('exp_id'=>$experiment->id, 'attribute_id'=>$dppAttr->id));
                if(!$dpp) {
                    $dpp = new ExpAttributes;
                    $dpp->exp_id = $experiment->id;
                    $dpp->attribute_id = $dppAttr->id;
                }
                $pxForm->dpp = $dpp->value;

                # load experiment type
                $expType = ExpAttributes::model()->findByAttributes(array('exp_id'=>$experiment->id, 'attribute_id'=>$expTypeAttr->id));
                if(!$expType) {
                    # set default experiment type
                    $expType = new ExpAttributes;
                    $expType->exp_id = $experiment->id;
                    $expType->attribute_id = $expTypeAttr->id;
                    $expType->value = CJSON::encode(array());
                }
                $expTypeVal = CJSON::decode($expType->value);
                $pxForm->experimentType = $expTypeVal;
                if(isset($expTypeVal['Other'])) {
                    $pxForm->exTypeOther = $expTypeVal['Other'];
                }

                # load instrument
                $instrument = ExpAttributes::model()->findByAttributes(array('exp_id'=>$experiment->id, 'attribute_id'=>$instrumentAttr->id));
                if(!$instrument) {
                    # set default instrument
                    $instrument = new ExpAttributes;
                    $instrument->exp_id = $experiment->id;
                    $instrument->attribute_id = $instrumentAttr->id;
                    $instrument->value = CJSON::encode(array());
                }
                $insVal = CJSON::decode($instrument->value);
                $pxForm->instrument = $insVal;
                if(isset($insVal['Other'])) {
                    $pxForm->instrumentOther = $insVal['Other'];
                }

                # load quantification
                $quantification = ExpAttributes::model()->findByAttributes(array('exp_id'=>$experiment->id, 'attribute_id'=>$quantificationAttr->id));
                if(!$quantification) {
                    # set default quantification
                    $quantification = new ExpAttributes;
                    $quantification->exp_id = $experiment->id;
                    $quantification->attribute_id = $quantificationAttr->id;
                    $quantification->value = CJSON::encode(array());
                }
                $quanVal = CJSON::decode($quantification->value);
                $pxForm->quantification = $quanVal;
                if(isset($quanVal['Other'])) {
                    $pxForm->quantificationOther = $quanVal['Other'];
                }

                # load modification
                $modification = ExpAttributes::model()->findByAttributes(array('exp_id'=>$experiment->id, 'attribute_id'=>$modificationAttr->id));
                if(!$modification) {
                    # set default modificaiton
                    $modification = new ExpAttributes;
                    $modification->exp_id = $experiment->id;
                    $modification->attribute_id = $modificationAttr->id;
                    $modification->value = CJSON::encode(array());
                }
                $modiVal = CJSON::decode($modification->value);
                $pxForm->modification = $modiVal;
                if(isset($modiVal['Other'])) {
                    $pxForm->modificationOther = $modiVal['Other'];
                }


                if(isset($_POST['PxInfoForm'])) {
                    # default is save and quit, redirect to user view_profile page
                    $isSubmit = false;
                    if(isset($_POST['submit-btn'])) {
                        # if user click submit, then submit the dataset
                        $isSubmit = true;
                    }

                    # store px Info
                    $transaction = Yii::app()->db->beginTransaction();
                    try {
                        $attrs = $_POST['PxInfoForm'];
                        $pxForm->attributes = $attrs;

                        if($pxForm->validate()) {
                            #store keywords
                            $keywords->value = $attrs['keywords'];

                            #store dpp
                            $dpp->value = $attrs['dpp'];

                            if(isset($_POST['exType'])) {
                                #store exp type
                                $expTypeVal = $_POST['exType'];
                                if(isset($expTypeVal['Other'])) {
                                    $expTypeVal['Other'] = ($attrs['exTypeOther'])? $attrs['exTypeOther'] : "";
                                }
                                $expType->value = CJSON::encode($expTypeVal);
                            }

                            if(isset($_POST['quantification'])) {
                                #store quantification
                                $quanVal = $_POST['quantification'];
                                if(isset($quanVal['Other'])) {
                                    $quanVal['Other'] = ($attrs['quantificationOther'])? $attrs['quantificationOther'] : "";
                                }
                                $quantification->value = CJSON::encode($quanVal);
                            }

                            if(isset($_POST['instrument'])) {
                                #store instrument
                                $insVal = $_POST['instrument'];
                                if(isset($insVal['Other'])) {
                                    $insVal['Other'] = ($attrs['instrumentOther'])? $attrs['instrumentOther'] : "";
                                }
                                $instrument->value = CJSON::encode($insVal);
                            }

                            if(isset($_POST['modification'])) {
                                #store modification
                                $modiVal = $_POST['modification'];
                                if(isset($modiVal['Other'])) {
                                    $modiVal['Other'] = ($attrs['modificationOther'])? $attrs['modificationOther'] : "";
                                }
                                $modification->value = CJSON::encode($modiVal);
                            }

                            #store spp into each samples in the dataset
                            $samples = $dataset->allSamples;
                            $isSppsStored = $this->storeSpps($samples, $attrs['spp'], $sppAttr);

                            if($isSppsStored and $keywords->save() and $dpp->save() and $expType->save() and $quantification->save() and $instrument->save() and $modification->save()) {
                                $transaction->commit();
                                if($isSubmit) {
                                    $this->redirect(array('/dataset/submit', 'id'=>$dataset->id));
                                }
                                $this->redirect('/user/view_profile');
                            }
                        }
                    } catch(Exception $e) {
                        $message = $e->getMessage();
                        Yii::log(print_r($message, true), 'error');
                        $transaction->rollback();
                        $this->redirect('/');
                    }
                }
                $this->render('pxInfoManagement', array('model' => $dataset, 'pxForm'=>$pxForm));
            }
        }

        public function actionDatasetAjaxDelete() {
            if(isset($_POST['dataset_id'])) {
                $dataset = Dataset::model()->findByPk($_POST['dataset_id']);

                if(!$dataset) {
                    Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Dataset does not exist.")));
                }

                if($dataset->delete()) {
                    Util::returnJSON(array("success"=>true));
                }
            }
            Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Delete Error.")));
        }

        private function storeSpps($samples, $value, $sppAttr) {
            $lastSA = SampleAttribute::model()->findByAttributes(array(),array('order'=>'id desc'));
            $lastId = $lastSA->id;
            foreach($samples as $sample) {
                $spp = SampleAttribute::model()->findByAttributes(array('attribute_id' => $sppAttr->id, 'sample_id'=>$sample->id));
                if(!$spp) {
                    $lastId = $lastId+1;
                    $spp = new SampleAttribute;
                    $spp->id = $lastId;
                    $spp->attribute_id = $sppAttr->id;
                    $spp->sample_id = $sample->id;
                }
                #store spp value
                $spp->value = $value;
                if(!$spp->save()) {
                    return false;
                }
            }
            return true;
        }
}
