<?php

class AdminDatasetSampleController extends Controller
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
                'actions' => array('create1', 'delete1', 'autocomplete','addSample','deleteSample','addSampleAttr','deleteSampleAttr','attributesList','updateSampleAttribute'),
                'users' => array('@')),
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
            $model=$this->loadModel($id);

		    $this->layout = 'new_datasetpage';
            $this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new DatasetSample;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['DatasetSample']))
		{
			$model->attributes=$_POST['DatasetSample'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

        $this->layout = 'new_datasetpage';
		$this->render('create',array(
			'model'=>$model,
		));
	}

    public function actionAutocomplete() {

        if (isset($_GET['term'])) {
            $partial_sample_term = $_GET['term'];
            $autoCompleteService = Yii::app()->autocomplete;
            $result = $autoCompleteService->findSpeciesLike($partial_sample_term);
            echo CJSON::encode($result);
            Yii::app()->end();
        }
    }

    public function actionDelete1($id) {
        if (isset($_SESSION['samples'])) {
            $info = $_SESSION['samples'];
            foreach ($info as $key => $value) {
                if ($value['id'] == $id) {
                    unset($info[$key]);
                    $_SESSION['samples'] = $info;
                    // $vars = array('samples');
                    //Dataset::storeSession($vars);
                    $condition = 'id=' . $id;

                    $sample_id = DatasetSample::model()->findByAttributes(array('id' => $id))->sample_id;
                    DatasetSample::model()->deleteAll($condition);
                    Sample::model()->deleteAll('id=' . $sample_id);

                    $this->redirect("/adminDatasetSample/create1");
                }
            }
        }
    }

    public function storeSample(&$model, &$id) {


        if (isset($_SESSION['dataset_id'])) {
            $dataset_id = $_SESSION['dataset_id'];
            //1) find species id
            $species_id = 0;
            $tax_id = $model->tax_id;
            $name = $model->species;
            $model->sample_id=0;
            //validate
            if (!$model->validate()) {
                var_dump("here");
                return false;
            }
            //-1 means it doesn't exit in our database
            if ($model->tax_id != -1) {

                $species = Species::model()->findByAttributes(array('tax_id' => $tax_id));
                 $species_id = $species->id;
            } else {
                $species = Species::model()->findByAttributes(array('common_name' => $name));
                if ($species != NULL) {
                    $species_id = $species->id;
                } else {
                    $species = Species::model()->findByAttributes(array('scientific_name' => $name));
                    if ($species != NULL)
                        $species_id = $species->id;
                    else {
                        //insert a new species record
                        $model->addError('comment', 'The species you input is not in our database, please
                            input 0:new organism and contact
                        <a href=&quot;mailto:database@gigasciencejournal.com&quot;>database@gigasciencejournal.com</a>.');
                       //ac $model = new DatasetSample;
                        return false;
                    }
                }
            }
            //2) insert sample
            $sample = new Sample;
            $sample->species_id = $species_id;
            $sample->code = $model->code;
            //$sample->s_attrs = $model->attribute;
           // $sample_id = 0;
            if (!$sample->save()) {
                $model->addError('error', 'Sample save error');
                return false;
            }
            $sample_id = $sample->id;


            //3) insert dataset_sample

            $model->sample_id = $sample_id;
            $model->dataset_id = $dataset_id;

            if (!$model->save()) {
                $model->addError('keyword', 'Dataset_Sample is not stored!');
                return false;
            }

            $id = $model->id;
            return true;
        }

        return false;
        echo('xxxxxxx');
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate1() {

        $model = new DatasetSample;
        $model->dataset_id = 1;
        //$model->
        //update
        if (!isset($_SESSION['samples']))
            $_SESSION['samples'] = array();

        $samples = $_SESSION['samples'];

        if (isset($_POST['DatasetSample'])) {


            $model->attributes = $_POST['DatasetSample'];

            $name = $_POST['DatasetSample']['code'];
            $tax_id = -1;
            $species = 0;
            if (strpos($_POST['DatasetSample']['species'], ":") !== false) {
                $array = explode(":",$_POST['DatasetSample']['species']);
//                var_dump($array);
                $tax_id = $array[0];
                $species = $_POST['DatasetSample']['species'];
//                var_dump($tax_id);
            } else {
                $species = $_POST['DatasetSample']['species'];
            }
            $attrs = $_POST['DatasetSample']['attribute'];

            $model->code = $name;
            $model->species = $species;
            $model->tax_id = $tax_id;
            $model->attribute = $attrs;
          //  var_dump( $model->code, $model->attribute);

            $id = 0;

            if(strpos($name,'SAMPLE') == 0)
            {
                $attribute_temp=null;
                $species1=null;
                $tax_id1=-1;
                $temp=explode(':', $name);
                if($temp[0]=='SAMPLE')
                {
                    $xmlpath=  'http://www.ebi.ac.uk/ena/data/view/'."$temp[1]".'&display=xml';
                    $allfile= simplexml_load_file($xmlpath);

                   foreach ($allfile->SAMPLE->SAMPLE_ATTRIBUTES->SAMPLE_ATTRIBUTE as $child)
                {
                    if($child->TAG=='Sample type'||$child->TAG=='Time of sample collection'||$child->TAG=='Habitat'||$child->TAG=='Sample extracted from')
                        $attribute_temp.= $child->TAG." = \"".$child->VALUE."\",";
                }
                $attribute_temp.="Description = \"".$allfile->SAMPLE->DESCRIPTION."\",";
                    foreach($allfile->SAMPLE->SAMPLE_NAME as $child)
                    {
                        if($child->TAG=='TAXON_ID')
                        {
                            $species1.=$child->VALUE.":";
                            $tax_id1=$child->VALUE;
                        }
                        if($child->TAG=='SCIENTIFIC_NAME')
                            $species1.=$child->VALUE.",";
                        if($child->TAG=='COMMON_NAME')
                            $species1.=$child->VALUE;

                    }


                }
                     $attrs=$attribute_temp;
                    // $species=$species1;
                     $model->attribute = $attrs;
                    // $model->tax_id=$tax_id1;


            }


            if ($this->storeSample($model, $id)) {

                $newItem = array('id' => $id, 'name' => $name, 'species' => $species, 'attrs' => $attrs);

                array_push($samples, $newItem);
                $_SESSION['samples'] = $samples;
                // $vars = array('samples');
                //Dataset::storeSession($vars);
                $model = new DatasetSample;
            }
            else{
                $model->species="";
            }
        }

        $sample_model = new CArrayDataProvider($samples);

        $this->render('create1', array(
            'model' => $model,
            'sample_model' => $sample_model,
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

		if(isset($_POST['DatasetSample']))
		{
			$model->attributes=$_POST['DatasetSample'];
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
		$dataProvider=new CActiveDataProvider('DatasetSample');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new DatasetSample('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['DatasetSample']))
			$model->setAttributes($_GET['DatasetSample']);

        $this->loadBaBbqPolyfills = true;
        $this->layout = '//layouts/new_column2';
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
		$model=DatasetSample::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='dataset-sample-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

        public function actionAddSample() {
            if(isset($_POST['dataset_id']) && isset($_POST['sample_name']) && isset($_POST['species'])) {

                $transaction = Yii::app()->db->beginTransaction();
                try {
                    if($_POST['sample_name'] == "") {
                         Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", 'Cannot add sample, please input "Sample ID" value.')));
                    }

                    if($_POST['species'] == "") {
                        Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", 'Cannot add sample, please input "valid  species" value.')));
                    }

                    $array = explode(":",$_POST['species']);
                    $tax_id = $array[0];
                    $species = Species::model()->findByAttributes(array('tax_id' => $tax_id));

                    if(!$species) {
                        Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", 'Cannot add sample, please input "valid  species" value.')));
                    }

                    #create new sample
                    $sample = new Sample;
                    $sample->species_id = $species->id;
                    $sample->name = $_POST['sample_name'];
                    $sample->submitted_id = Yii::app()->user->id;
                    $sample->submission_date = date('Y-m-d H:i:s');

                    $user = User::model()->findByPk(Yii::app()->user->id);
                    if($user) {
                        $sample->contact_author_name  = $user->first_name." ".$user->last_name;
                        $sample->contact_author_email = $user->email;
                    }

                    if($sample->save()) {
                        #create dataset sample
                        $ds = new DatasetSample;
                        $ds->dataset_id = $_POST['dataset_id'];
                        $ds->sample_id = $sample->id;
                        if($ds->save()) {
                            $transaction->commit();
                            Util::returnJSON(array("success"=>true));
                        }
                    }

                    Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Save Error.")));

                } catch(Exception $e) {
                    $message = $e->getMessage();
                    Yii::log(print_r($message, true), 'error');
                    $transaction->rollback();
                    Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", 'Cannot add sample, please input "valid  species" value.')));
                }

            }
        }

        public function actionDeleteSample() {
            if(isset($_POST['ds_id'])) {
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    $ds = DatasetSample::model()->findByPk($_POST['ds_id']);
                    if($ds->delete()) {
                        $transaction->commit();
                        Util::returnJSON(array("success"=>true));
                    }
                } catch(Exception $e) {
                    $message = $e->getMessage();
                    Yii::log(print_r($message, true), 'error');
                    $transaction->rollback();
                    Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Delete Error.")));
                }

                 Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Delete Error.")));
            }
        }

        public function actionAddSampleAttr() {
            if(isset($_POST['sample_id']) && isset($_POST['attr_id'])
                && isset($_POST['attr_value']) && isset($_POST['attr_unit'])) {

                if(strlen($_POST['attr_id']) < 3) {
                    Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Please enter an Attribute name with more than 3 characters.")));
                }

                $lastSa = SampleAttribute::model()->find(array('order'=>'id desc'));

                $sa = new SampleAttribute;
                if($lastSa) {
                    $sa->id = $lastSa->id+1;
                }

                // try to find attribute, if not found, create a new one
               $attr = Attribute::model()->findByAttributes(array('attribute_name'=>$_POST['attr_id']));
               if(!$attr) {
                    #create new attribute
                    $attr = new Attribute;
                    $attr->attribute_name = $_POST['attr_id'];
                    $attr->save(false);
               }

                $sa->sample_id = $_POST['sample_id'];
                $sa->attribute_id = $attr->id;
                $sa->value = $_POST['attr_value'];

                if(isset($_POST['attr_unit']) && $_POST['attr_unit'] != "") {
                    $sa->unit_id = $_POST['attr_unit'];
                }

                if($sa->save()) {
                    Util::returnJSON(array("success"=>true));
                }

                Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Cannot add sample attr.")));
            }
        }

        public function actionDeleteSampleAttr() {
            if(isset($_POST['sa_id'])) {
                $sa = SampleAttribute::model()->findByPk($_POST['sa_id']);
                if($sa->delete()) {
                    Util::returnJSON(array("success"=>true));
                }
                 Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Delete Error.")));
            }
        }

        public function actionUpdateSampleAttribute() {
            if(isset($_POST['sa_id']) && isset($_POST['sa_value'])) {
                $sa = SampleAttribute::model()->findByPk($_POST['sa_id']);
                if(!$sa) {
                     Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Cannot find the sample attribute.")));
                }

                $sa->value = $_POST['sa_value'];
                if($sa->save()) {
                     Util::returnJSON(array("success"=>true));
                }

                Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Update Error.")));
            }
        }

        public function actionAttributesList() {
            $attrs = array();
            $result = array();

            if (isset($_GET['term'])) {
                $criteria = new CDbCriteria;
                $criteria->addSearchCondition('attribute_name', $_GET['term']);
                $attrs = Attribute::model()->findAll($criteria);

                foreach($attrs as $attr) {
                    $result[$attr->attribute_name] = $attr->attribute_name;
                }

                echo CJSON::encode($result);
                Yii::app()->end();
            }
        }
}
