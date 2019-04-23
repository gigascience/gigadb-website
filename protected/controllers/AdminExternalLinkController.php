<?php

class AdminExternalLinkController extends Controller
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
                                 'actions' => array('create1', 'delete1','autocomplete','addExLink', 'deleteExLink', 'deleteExLinks'),
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
		$model=new ExternalLink;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ExternalLink']))
		{
			$model->attributes=$_POST['ExternalLink'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}
        
         public function actionDelete1($id) {
        if (isset($_SESSION['externalLinks'])) {
            $info = $_SESSION['externalLinks'];
            foreach ($info as $key => $value) {
                if ($value['id'] == $id) {
                    unset($info[$key]);
                    $_SESSION['externalLinks'] = $info;
                    // $vars = array('externelLinks');
                    //Dataset::storeSession($vars);
                    $condition = 'id=' . $id;
                    ExternalLink::model()->deleteAll($condition);
                    $this->redirect("/adminExternalLink/create1");
                }
            }
        }
    }

    public function storeExternalLink(&$model, &$id) {


        if (isset($_SESSION['dataset_id'])) {
            $dataset_id = $_SESSION['dataset_id'];

            $model->dataset_id = $dataset_id;
            if (!$model->save()) {
                $model->addError('error', 'Error: ExternalLink is not stored!');
                return false;
            }

            $id = $model->id;
            return true;
        }
        return false;
    }

    public function actionCreate1() {
        $model = new ExternalLink;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $model->dataset_id = 1;

        //update 
        if (!isset($_SESSION['externalLinks']))
            $_SESSION['externalLinks'] = array();

        $externalLinks = $_SESSION['externalLinks'];


        if (isset($_POST['ExternalLink'])) {
            //store the information in session 
//            if (!isset($_SESSION['externalLink_id']))
//                $_SESSION['externalLink_id'] = 0;
//            $id = $_SESSION['externalLink_id'];
//            $_SESSION['externalLink_id'] += 1;



            $url = $_POST['ExternalLink']['url'];
            if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url)) {
                $model->addError('error', 'Error: The Url is not valid!');
            } else {

                //$model->
                $type_id = 2;

                $model->url = $url;
                $model->external_link_type_id = $type_id;
                $id = 0;
                if ($this->storeExternalLink($model, $id)) {
                    $type_info = ExternalLinkType::model()->findByAttributes(array('id' => $type_id))->name;

                    $newItem = array('id' => $id, 'url' => $url, 'type_info' => $type_info, 'type_id' => $type_id);


                    array_push($externalLinks, $newItem);

                    $_SESSION['externalLinks'] = $externalLinks;
                    // $vars = array('externalLinks');
                    //Dataset::storeSession($vars);
                    $model = new ExternalLink;
                }
            }
        }


        $externalLink_model = new CArrayDataProvider($externalLinks);


        $this->render('create1', array(
            'model' => $model,
            'externalLink_model' => $externalLink_model
        ));
    }

    public function actionAutocomplete() {

        if (isset($_GET['term'])) {
            $partial_external_link_term = $_GET['term'];
            $autoCompleteServiceForExternalLink = Yii::app()->autocomplete;
            $result = $autoCompleteServiceForExternalLink->findSpeciesLike($partial_external_link_term);
            echo CJSON::encode($result);
            Yii::app()->end();
        }
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

		if(isset($_POST['ExternalLink']))
		{
			$model->attributes=$_POST['ExternalLink'];
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
		$dataProvider=new CActiveDataProvider('ExternalLink');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new ExternalLink('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ExternalLink']))
			$model->setAttributes($_GET['ExternalLink']);

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
		$model=ExternalLink::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='external-link-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    /**
     * @throws CException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionAddExLink() {
        if(isset($_POST['dataset_id']) && isset($_POST['url']) && isset($_POST['externalLinkType'])) {
            $dataset = $this->getDataset($_POST['dataset_id']);

            $exLink = new ExternalLink;
            $exLink->loadByData($_POST);

            $transaction = Yii::app()->db->beginTransaction();
            if($exLink->validate() && $exLink->save()) {
                $dataset->setAdditionalInformationKey($_POST['externalLinkType'], true);
                if (!$dataset->save()) {
                    $transaction->rollback();
                    Util::returnJSON(array(
                        "success" => false,
                        "message" => Yii::t("app", "Cant update Dataset."),
                    ));
                }

                $transaction->commit();
                Util::returnJSON( array(
                    "success" => true,
                ));
            }

            $transaction->rollback();
            Util::returnJSON(array(
                "success"=>false,
                "message"=>current($exLink->getErrors())
            ));
        }

        Util::returnJSON(array(
            "success" => false,
            "message" => Yii::t("app", "Data is empty."),
        ));
    }

    /**
     * @throws CDbException
     * @throws CException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionDeleteExLink() {
        if(isset($_POST['exLink_id'])) {
            $dataset = $this->getDataset($_POST['dataset_id']);
            $exLink = $this->getExLink($_POST['exLink_id']);

            $transaction = Yii::app()->db->beginTransaction();
            if($exLink->delete()) {
                $count = ExternalLink::model()->CountByAttributes(array('dataset_id' => $_POST['dataset_id'], 'type' => $exLink->type));

                if (!$count) {
                    $dataset->setAdditionalInformationKey($exLink->type, false);
                    if (!$dataset->save()) {
                        $transaction->rollback();
                        Util::returnJSON(array(
                            "success" => false,
                            "message" => Yii::t("app", "Cant update Dataset."),
                        ));
                    }
                }

                $transaction->commit();
                Util::returnJSON( array(
                    "success" => true,
                ));
            }

            $transaction->rollback();
            Util::returnJSON(array(
                "success" => false,
                "message" => Yii::t("app", "Cant delete External Link."),
            ));
        }

        Util::returnJSON(array(
            "success" => false,
            "message" => Yii::t("app", "Data is empty."),
        ));
    }

    /**
     * @throws CException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionDeleteExLinks() {

        if(isset($_POST['dataset_id']) && isset($_POST['type'])) {
            $dataset = $this->getDataset($_POST['dataset_id']);
            $exLinks = ExternalLink::model()->findAllByAttributes(array(
                'dataset_id' => $_POST['dataset_id'],
                'type' => $_POST['type'],
            ));

            $transaction = Yii::app()->db->beginTransaction();
            foreach ($exLinks as $exLink) {
                if(!$exLink->delete()) {
                    $transaction->rollback();
                    Util::returnJSON(array(
                        "success"=>false,
                        "message"=>Yii::t("app", "Cant delete External Link.")
                    ));
                }
            }

            $dataset->setAdditionalInformationKey($_POST['type'], false);
            if (!$dataset->save()) {
                $transaction->rollback();
                Util::returnJSON(array(
                    "success" => false,
                    "message" => Yii::t("app", "Cant update Dataset."),
                ));
            }

            $transaction->commit();
            Util::returnJSON(array("success"=>true));
        }

        Util::returnJSON(array(
            "success"=>false,
            "message"=>Yii::t("app", "Data is empty.")
        ));
    }

    /**
     * @param $id
     * @return array|Dataset|mixed|null
     * @throws \yii\web\BadRequestHttpException
     */
    protected function getDataset($id)
    {
        $dataset = Dataset::model()->findByPk($id);

        if (!$dataset) {
            throw new \yii\web\BadRequestHttpException('Dataset ID is invalid.');
        }

        return $dataset;
    }

    /**
     * @param $id
     * @return array|Dataset|mixed|null
     * @throws \yii\web\BadRequestHttpException
     */
    protected function getExLink($id)
    {
        $exLink = ExternalLink::model()->findByPk($id);

        if (!$exLink) {
            throw new \yii\web\BadRequestHttpException('External Link ID is invalid.');
        }

        return $exLink;
    }
}
