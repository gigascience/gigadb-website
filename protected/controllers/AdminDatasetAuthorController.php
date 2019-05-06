<?php

class AdminDatasetAuthorController extends Controller
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
                                'actions' => array('create1', 'delete1', 'autocomplete', 'search','addAuthor', 'addAuthors', 'saveAuthors', 'deleteAuthor','updateRank'),
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
		$model=new DatasetAuthor;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['DatasetAuthor']))
		{
			$model->attributes=$_POST['DatasetAuthor'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

    public function actionCreate1() {
        $model = new DatasetAuthor;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        //this is fake information
        $model->dataset_id = 1;
        //update
        if (!isset($_SESSION['authors']))
            $_SESSION['authors'] = array();

        $authors = $_SESSION['authors'];

        if (isset($_POST['DatasetAuthor'])) {

            $ranks = trim($_POST['DatasetAuthor']['rank']);
            $names = trim($_POST['DatasetAuthor']['author_name']);
            if (substr($names, -1) == ";")
                $names = substr($names, 0, -1);
            $valid = true;
            $namearray = array();
            if ($names != "")
                $namearray = explode(";", $names);
//            var_dump($namearray);
            if ($ranks == "")
                $rankarray = array();
            else
                $rankarray = explode(";", $ranks);
//            var_dump($rankarray);
//            if (count($namearray) != count($rankarray)) {
//                $model->addError("error", "the number of name and rank are different!");
//                $valid = false;
//            }
            //test names
            if ($valid) {
                foreach ($namearray as $name) {
                    if ($name == "") {
                        $model->addError("author_id", "Name can't be blank!");
                        $valid = false;
                        break;
                    }
                }
            }
            //test ranks
            if ($valid) {
                foreach ($rankarray as $rank) {
//                    if ($rank == "") {
//                        $model->addError("error", "rank can't be blank!");
//                        $valid = false;
//                        break;
//                    }
                    if (!is_numeric($rank)) {
                        $model->addError("rank", "rank should be an integer!");
                        $valid = false;
                        break;
                    }
                }
            }
//            var_dump(count($rankarray)." test");
            if ($valid) {

                foreach ($namearray as $index => $name) {
                    if ($index < count($rankarray)) {
                        $rank = $rankarray[$index];
                    } else {
                        $rank = 1;

                        while (true) {
                             $found = true;
                            //find the maximum one in the authors array
                            foreach ($authors as $author) {
                                if ($author['rank'] == $rank){
                                   $found = false;
                                   break;
                                }
                            }
                            if($found)
                                break;

                            $rank++;
                        }


                    }

                    $valid = true;

                    //check if there is duplicate input
                    foreach (array_values($authors) as $author) {
                        if ($author['rank'] == $rank && $author['name'] == $name) {
                            $model->addError("author_id", "Duplicate input");
                            $valid = false;
                            break;
                        }
                    }

                    if ($valid) {

                        //store author dataset
                        $model->rank = $rank;
                        $model->author_name = $name;
                        $id = 0;
                        if ($this->storeAuthor($model, $id)) {

                            $newItem = array('rank' => $rank, 'id' => $id, 'name' => $name);

                            array_push($authors, $newItem);
                            $_SESSION['authors'] = $authors;
                            //$vars = array('authors');
                            ////Dataset::storeSession($vars);
                        } else {
                            $model->addError("error", "database operation failure, please log out first and log in again.");
                        }
                    }
                }
            }
        }
        //   $model = new DatasetAuthor;
        $author_model = new CArrayDataProvider($authors);
      //  $model = new DatasetAuthor;
        $this->render('create1', array(
            'model' => $model,
            'author_model' => $author_model,
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

		if(isset($_POST['DatasetAuthor']))
		{
			$model->attributes=$_POST['DatasetAuthor'];
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
        if (isset($_SESSION['authors'])) {
            $authors = $_SESSION['authors'];
            foreach ($authors as $key => $author) {
                if ($author['id'] == $id) {
                    unset($authors[$key]);
                    $_SESSION['authors'] = $authors;
                    // $vars = array('authors');
                    //Dataset::storeSession($vars);
                    //delete the record in table dataset_author
                    $condition = "id=" . $id;
                    DatasetAuthor::model()->deleteAll($condition);

                    $this->redirect("/adminDatasetAuthor/create1");
                }
            }
        }
    }

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('DatasetAuthor');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new DatasetAuthor('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['DatasetAuthor']))
			$model->setAttributes($_GET['DatasetAuthor']);

		$this->render('admin',array(
			'model'=>$model,
		));
	}

    public function actionSearch($term) {

        if (Yii::app()->request->isAjaxRequest && !empty($term)) {
            $variants = array();
            $criteria = new CDbCriteria;
            $criteria->select = 'first_name, surname';
            $criteria->distinct = 'true';
            $criteria->addSearchCondition("LOWER(first_name) || ' ' || LOWER(surname)", '%' . strtolower($term) . '%', false);

            $tags = Author::model()->findAll($criteria);
            if (!empty($tags)) {
                foreach ($tags as $tag) {
                    $variants[] = $tag->attributes['surname'] . ' ' . $tag->attributes['first_name'];
                }
            }
            echo CJSON::encode($variants);
            Yii::app()->end();
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    public function actionAutocomplete() {
        $res = array();
        $result = array();
        if (isset($_GET['term'])) {
            $sql = "Select distinct name from author where name like :name";
            $command = Yii::app()->db->createCommand($sql);
            $parts = explode(";", $_GET['term']);
            $part = $parts[count($parts) - 1];
            $command->bindValue(":name", '%' . $part . '%', PDO::PARAM_STR);
            $res = $command->queryAll();
            if (!empty($res))
                foreach ($res as $mres) {
                    $result[] = $mres['name'];
                }
            echo CJSON::encode($result);
            Yii::app()->end();
        }
    }

    public function storeAuthor(&$datasetAuthor, &$id) {

        if (isset($_SESSION['dataset_id'])) {
            $dataset_id = $_SESSION['dataset_id'];
            $model->dataset_id = $dataset_id;
            $model = $datasetAuthor;
            //store author into table author
            $name = $datasetAuthor->author_name;
            $rank = $datasetAuthor->rank;

            //determine if the model is valid
            if (!$datasetAuthor->validate())
                return false;

            //$author = Author::model()->findByAttributes(array('name' => $name, 'rank' => $rank));

            $author = Author::model()->findByCompleteName($name);
            if ($author != NULL) {
                $author_id = $author->id;
            } else {
                $author = new Author;
                $author->name = $name;
                //temporary
                $author->orcid = 1;
                $author->rank = $rank;
                if (!$author->save()) {
                    return false;
                }
                $author_id = $author->id;
            }

            $model->author_id = $author_id;
            $model->rank = $rank;

            if (!$model->save()) {
                Yii::app()->user->setFlash('keyword', 'Error: Author is not stored!');
                return false;
            }

            $id = $model->id;
            return true;
        }

        return false;
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=DatasetAuthor::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='dataset-author-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    /**
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionAddAuthor() {
        if(isset($_POST['dataset_id']) && isset($_POST['Author'])) {
            //$dataset = $this->getDataset($_POST['dataset_id']);

            $author = new Author();
            $author->loadByData($_POST['Author']);
            if($author->validate()) {
                //$author->save();
                //$dataset->addAuthor($author);

                Util::returnJSON(array(
                    "success"=>true,
                    'author' => $author->asArray(),
                ));
            }

            Util::returnJSON(array("success"=>false,"message"=>current($author->getErrors())));
        }
    }

    /**
     * @throws CException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSaveAuthors() {
        if(isset($_POST['dataset_id'])) {
            $dataset = $this->getDataset($_POST['dataset_id']);

            $transaction = Yii::app()->db->beginTransaction();
            if (isset($_POST['authors']) && is_array($_POST['authors'])) {
                foreach ($_POST['authors'] as $num => $row) {
                    if ($row['id']) {
                        $da = DatasetAuthor::model()->findByPk($row['id']);
                        if (!$da) {
                            $transaction->rollback();
                            Util::returnJSON(array("success" => false, "message" => "Row $num: Wrong id"));
                        }
                        $author = $da->author;
                    } else {
                        $author = new Author();
                        $author->loadByData($row);
                    }

                    if ($author->validate()) {
                        $author->save();
                        $dataset->addAuthor($author, $row['order']);
                    } else {
                        $transaction->rollback();
                        $error = current($author->getErrors());
                        Util::returnJSON(array("success" => false, "message" => "Row $num: " . $error[0]));
                    }
                }
            }

            if (isset($_POST['delete_ids']) && is_array($_POST['delete_ids'])) {
                foreach ($_POST['delete_ids'] as $deleteId) {
                    $da = DatasetAuthor::model()->findByPk($deleteId);
                    if ($da) {
                        if ($da->delete()) {
                            $da->author->delete();
                        }
                    }
                }
            }

            $transaction->commit();
            Util::returnJSON(array("success"=>true));
        }

        Util::returnJSON(array("success"=>false,"message"=>"Data is empty."));
    }

    /**
     * @throws CException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionAddAuthors() {
        $authors = CUploadedFile::getInstanceByName('authors');
        if($authors) {
            //$datasetId = isset($_POST['dataset_id']) ? $_POST['dataset_id'] : 0;
            //$dataset = $this->getDataset($datasetId);

            if ($authors->getType() != CsvHelper::TYPE_CSV && $authors->getType() != CsvHelper::TYPE_TSV) {
                Util::returnJSON(array("success"=>false,"message"=>"File has wrong extension."));
            }

            $delimiter = $authors->getType() == CsvHelper::TYPE_CSV ? ';' : "\t";
            $rows = CsvHelper::getArrayByFileName($authors->getTempName(), $delimiter);
            if (!$rows) {
                Util::returnJSON(array("success"=>false,"message"=>"File is empty."));
            }

            $authors = array();
            foreach ($rows as $num => $row) {
                $author = new Author();
                $author->loadByCsvRow($row);
                if($author->validate()) {
                    $authors[] = $author->asArray();
                } else {
                    $error = current($author->getErrors());
                    Util::returnJSON(array("success"=>false,"message"=> "Row $num: " . $error[0]));
                }
            }

            Util::returnJSON(array("success"=>true, 'authors' => $authors));
        }

        Util::returnJSON(array("success"=>false,"message"=>"You must input file."));
    }

    public function actionDeleteAuthor() {
        if(isset($_POST['da_id'])) {
            $da = DatasetAuthor::model()->findByPk($_POST['da_id']);
            $rank = $da->rank;
            if($da->delete()) {
                $da->author->delete();

                $criteria = new CDbCriteria;
                $criteria->addCondition('dataset_id='.$da->dataset_id);
                $criteria->addCondition('rank > '.$rank);
                $higherRankDas = DatasetAuthor::model()->findAll($criteria);

                foreach($higherRankDas as $hrda) {
                    $hrda->rank = $hrda->rank - 1;
                    $hrda->save(false);
                }

                 Util::returnJSON(array("success"=>true));
            }
             Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Delete Error.")));
        }
    }

    public function actionUpdateRank() {
        if(isset($_POST['da_id']) && isset($_POST['rank'])) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $da = DatasetAuthor::model()->findByPk($_POST['da_id']);
                $rank = $da->rank;
                $changeRank = intval($_POST['rank']);
                $lastDa = DatasetAuthor::model()->findByAttributes(array('dataset_id'=>$da->dataset_id), array('order'=>'rank desc'));

                if(!is_int($changeRank) or $changeRank == 0) {
                     Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Please enter a non-zero integer.")));
                }

                if(!$lastDa or ($changeRank > $lastDa->rank)) {
                     Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Please enter a value less or equal than ".$lastDa->rank)));
                }

                $das = array();
                if($changeRank > $rank) {
                    // update order down by 1
                    // find all dataset authors in between
                    $criteria = new CDbCriteria;
                    $criteria->addCondition('t.rank > '.min($rank,$changeRank));
                    $criteria->addCondition('t.rank <= '.max($rank,$changeRank));
                    $criteria->addCondition('t.dataset_id = '.$da->dataset_id);
                    $das = DatasetAuthor::model()->findAll($criteria);
                    foreach($das as $updateDa) {
                        $updateDa->rank = $updateDa->rank - 1;
                    }
                } else {
                    // update order up by 1
                    // find all dataset authors in between
                    $criteria = new CDbCriteria;
                    $criteria->addCondition('t.rank >= '.min($rank,$changeRank));
                    $criteria->addCondition('t.rank < '.max($rank,$changeRank));
                    $criteria->addCondition('t.dataset_id = '.$da->dataset_id);
                    $das = DatasetAuthor::model()->findAll($criteria);
                    foreach($das as $updateDa) {
                        $updateDa->rank = $updateDa->rank + 1;
                    }
                }

                $da->rank = $changeRank;
                if($da->save()) {
                    if(!$this->saveDas($das)) {
                        Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Cannot save das.")));
                    }

                    $transaction->commit();
                    Util::returnJSON(array("success"=>true));
                }
                Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Cannot update rank.")));
            } catch(Exception $e) {
                $message = $e->getMessage();
                Yii::log(print_r($message, true), 'error');
                $transaction->rollback();
                Util::returnJSON(array("success"=>false,"message"=>Yii::t("app", "Cannot update rank.")));
            }
        }
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

    private function saveDas($das) {
        foreach($das as $da) {
            if(!$da->save()) {
                return false;
            }
        }

        return true;
    }
}
