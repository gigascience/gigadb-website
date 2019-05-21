<?php

class AdminRelationController extends Controller
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
                                  'actions' => array('create1', 'delete1','addRelation','deleteRelation', 'getRelation'),
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
		$model = new Relation();
        $relationDAO = new RelationDAO();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Relation']))
		{
			$model->attributes=$_POST['Relation'];
            if($model->save()) {
                $related_id=$model->related_doi;
                $dataset_id=$model->dataset_id;
                $relationship= $model->relationship;

                $relationDAO->createReciprocalTo( $model, new Relation() );

                $this->redirect(array('view','id'=>$model->id));
            }
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

    public function storeRelation(&$model, &$id) {


        if (isset($_SESSION['dataset_id'])) {
            $dataset_id = $_SESSION['dataset_id'];

            $model->dataset_id = $dataset_id;
            if (!$model->save()) {
                $model->addError('error', 'Relation is not stored!');
                return false;
            }

            $id = $model->id;
            return true;
        }
        return false;
    }

    public function actionCreate1() {
        $model = new Relation;
        $relationDAO = new RelationDAO();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);


        $model->dataset_id = 1;
        //$model->re
        //update
        if (!isset($_SESSION['relations']))
            $_SESSION['relations'] = array();

        $relations = $_SESSION['relations'];

        $relation_type = array("IsNewVersionOf",
            "IsSupplentedBy", "IsSupplementedTo",
            "Compiles", "IsCompiledBy"
        );

        if (isset($_POST['Relation'])) {
            //store the information in session
//            if (!isset($_SESSION['relation_id']))
//                $_SESSION['relation_id'] = 0;
//            $id = $_SESSION['relation_id'];
//            $_SESSION['relation_id'] += 1;



            $related_doi = $_POST['Relation']['related_doi'];
            $relationship = $relation_type[$_POST['Relation']['relationship']];

            $model->related_doi = $related_doi;
            $model->relationship = $relationship;

            $id = 0;
            if ($this->storeRelation($model, $id)) {
                $newItem = array('id' => $id, 'related_doi' => $related_doi, 'relationship' => $relationship);

                $relationDAO->createReciprocalTo( $model, new Relation() );

                array_push($relations, $newItem);

                $_SESSION['relations'] = $relations;

                // $vars = array('relations');
                //Dataset::storeSession($vars);
                $model = new Relation;
            }
        }


        $relation_model = new CArrayDataProvider($relations);


        $this->render('create1', array(
            'model' => $model,
            'relation_model' => $relation_model,
            'relation_type' => $relation_type
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

		if(isset($_POST['Relation']))
		{
			$model->attributes=$_POST['Relation'];

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
        if (isset($_SESSION['relations'])) {
            $info = $_SESSION['relations'];
            foreach ($info as $key => $value) {
                if ($value['id'] == $id) {
                    unset($info[$key]);
                    $_SESSION['relations'] = $info;
                    // $vars = array('relations');
                    //Dataset::storeSession($vars);
                    $condition = 'id=' . $id;
                    Relation::model()->deleteAll($condition);
                    $this->redirect("/adminRelation/create1");
                }
            }
        }
    }


	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Relation');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Relation('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Relation']))
			$model->setAttributes($_GET['Relation']);

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
		$model=Relation::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='relation-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    public function actionAddRelation() {
        if(isset($_POST['dataset_id']) && isset($_POST['doi']) && isset($_POST['relationship'])) {

            $relation = Relation::model()->findByAttributes(array(
              'dataset_id'=>$_POST['dataset_id'], 
              'related_doi'=>$_POST['doi'],
              'relationship_id'=>$_POST['relationship'],
              ));
            if($relation) {
              Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "This relation has been added already.")));
            }

            $transaction = Yii::app()->db->beginTransaction();
            try {
                $relation = new Relation;
                $relation->dataset_id = $_POST['dataset_id'];
                $relation->related_doi = $_POST['doi'];
                $relation->relationship_id = $_POST['relationship'];

                $relation2 = new Relation;
                $relation2->dataset_id = Dataset::model()->findByAttributes(array('identifier' => $_POST['doi']))->id;
                $relation2->related_doi = Dataset::model()->findByPk($_POST['dataset_id'])->identifier;
                $relation2->relationship_id = $_POST['relationship'];

                if($relation->save()&&$relation2->save()) {
                  $transaction->commit();
                  Util::returnJSON(array("success"=>true));
                }
                else {
                    $transaction->rollback();
                    Yii::log(print_r($relation->getErrors(), true), 'debug');
                }

            }catch(Exception $e) {
                $message = $e->getMessage();
                Yii::log(print_r($message, true), 'error');
                $transaction->rollback();
                Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Save Error.")));
            }
        }
    }

    public function actionDeleteRelation() {
        if(isset($_POST['relation_id'])) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $relation = Relation::model()->findByPk($_POST['relation_id']);

                $rdid = $relation->dataset_id;
                $rrdoi = $relation->related_doi;
                $rrid = $relation->relationship_id;

                $relation2 = Relation::model()->findByAttributes(array(
                  'dataset_id'=> Dataset::model()->findByAttributes(array('identifier' => $rrdoi))->id,
                  'related_doi' => Dataset::model()->findByPk($rdid)->identifier,
                  'relationship_id' => $rrid,
                  ));

                if($relation->delete()&&$relation2->delete()) {
                      $transaction->commit();
                      Util::returnJSON(array("success"=>true));
                 }
                else {
                    $transaction->rollback();
                    Util::returnJSON(array("success"=>false));
                }
              }catch(Exception $e) {
                $message = $e->getMessage();
                Yii::log(print_r($message, true), 'error');
                $transaction->rollback();
                Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Delete Error.")));
              }
        }
    }

    public function actionGetRelation() {
        if(isset($_POST['dataset_id']) && isset($_POST['doi']) && isset($_POST['relationship'])) {
            $relationship = Relationship::model()->findByPk($_POST['relationship']);
            if (!$relationship) {
                Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Relationship ID is invalid.")));
            }

            Util::returnJSON(array(
                "success"=>true,
                'relation' => array(
                    'relationship_id' => $relationship->id,
                    'relationship_name' => $relationship->name,
                    'related_doi' => $_POST['doi'],
                ),
            ));
        }
    }

}
