<?php

declare(strict_types=1);

class AdminRelationController extends Controller
{
    /**
     * @return string[] action filters
     */
    public function filters(): array
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array<int, array<int|string, list<string>|string>> access control rules
     */
    public function accessRules(): array
    {
        return array(
            array('allow', // admin only
                'actions' => array('admin','delete','index','view','create','update'),
                'roles' => array('admin'),
            ),
            array('allow',
                'actions' => array('create1', 'delete1','addRelation','deleteRelation'),
                'users' => array('@'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView(int $id): void
    {
        $this->render('view', array('model' => $this->loadModel($id)));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate(): void
    {
        /** @var CWebApplication $app */
        $app = Yii::app();

        $model = new Relation();
        $relationDAO = new RelationDAO();

        if ($attributes = Yii::$app->request->post('Relation')) {
            $transaction = $app->db->beginTransaction();
            try {
                $model->attributes = $attributes;

                if ($model->dataset && $model->dataset->identifier === $attributes['related_doi']) {
                    throw new CException("Can't refer the same DOI");
                }

                if (!$model->save()) {
                    $this->render('create', array('model' => $model));
                }

                if ($attributes['add_reciprocal']) {
                    $relationDAO->createReciprocalTo($model, new Relation());
                }

                $transaction->commit();
                $this->redirect(array('view', 'id' => $model->id));
            } catch (Exception $e) {
                $transaction->rollback();

                $app->user->setFlash('error', $e->getMessage());
            }
        }

        $this->render('create', array('model' => $model));
    }

    //TODO: not used atm
    public function storeRelation(&$model, &$id)
    {


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

    //TODO: not used atm
    public function actionCreate1()
    {
        $model = new Relation();
        $relationDAO = new RelationDAO();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);


        $model->dataset_id = 1;
        if (!isset($_SESSION['relations']))
            $_SESSION['relations'] = array();

        $relations = $_SESSION['relations'];

        $relation_type = array("IsNewVersionOf",
            "IsSupplentedBy", "IsSupplementedTo",
            "Compiles", "IsCompiledBy"
        );

        if (isset($_POST['Relation'])) {

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
    public function actionUpdate(int $id): void
    {
        $model = $this->loadModel($id);

        if ($relation = Yii::$app->request->post('Relation')) {
            $model->attributes = $relation;

            if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('update', array('model' => $model));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete(int $id): void
    {
        if (!Yii::app()->request->isPostRequest) {
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
        }

        // we only allow deletion via POST request
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!Yii::$app->request->get('ajax')) {
            $returnUrl = Yii::$app->request->post('returnUrl');

            $this->redirect($returnUrl ?: array('admin'));
        }
    }


    //TODO: not used atm
    public function actionDelete1($id)
    {
        if (isset($_SESSION['relations'])) {
            $info = $_SESSION['relations'];
            foreach ($info as $key => $value) {
                if ($value['id'] == $id) {
                    unset($info[$key]);
                    $_SESSION['relations'] = $info;
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
    public function actionIndex(): void
    {
        $dataProvider = new CActiveDataProvider('Relation');

        $this->render('index', array('dataProvider' => $dataProvider));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin(): void
    {
        $model = new Relation('search');
        $model->unsetAttributes();  // clear any default values
        if ($relation = Yii::$app->request->get('Relation')) {
            $model->setAttributes($relation);
        }

        $this->loadBaBbqPolyfills = true;

        $this->render('admin', array('model' => $model));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     */
    public function loadModel(int $id): Relation
    {
        $model = Relation::model()->findByPk($id);
        if (!$model) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    /**
     * Performs the AJAX validation.
     *
     * @param CModel $model the model to be validated
     */
    protected function performAjaxValidation(CModel $model): void
    {
        if (Yii::$app->request->post('ajax') === 'relation-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionAddRelation(): void
    {
        $datasetId = Yii::$app->request->post('dataset_id');
        $doi = Yii::$app->request->post('doi');
        $relationship = Yii::$app->request->post('relationship');

        if (!$datasetId || !$doi || !$relationship) {
            Util::returnJSON(array('success' => false, 'message' => Yii::t('app', 'Please fill all the required fields.')));
        }

        $relation = Relation::model()->findByAttributes(array(
          'dataset_id' => $datasetId,
          'related_doi' => $doi,
          'relationship_id' => $relationship,
          ));

        if ($relation) {
            Util::returnJSON(array("success" => false, "message" => Yii::t("app", "This relation has been added already.")));
        }

        $transaction = Yii::app()->db->beginTransaction();
        try {
            $relation = new Relation();
            $relation->dataset_id = $datasetId;
            $relation->related_doi = $doi;
            $relation->relationship_id = $relationship;

            $relation2 = new Relation();
            $relation2->dataset_id = Dataset::model()->findByAttributes(array('identifier' => $doi))->id;
            $relation2->related_doi = Dataset::model()->findByPk($datasetId)->identifier;
            $relation2->relationship_id = $relationship;

            if ($relation->save() && $relation2->save()) {
                $transaction->commit();

                Util::returnJSON(array("success" => true));
            }

            $transaction->rollback();
            Yii::log(print_r($relation->getErrors(), true), 'debug');
        } catch (Exception $e) {
            $message = $e->getMessage();
            Yii::log(print_r($message, true), 'error');
            $transaction->rollback();

            Util::returnJSON(array("success" => false, "message" => Yii::t("app", "Save Error.")));
        }
    }

    public function actionDeleteRelation(): void
    {
        if (!$relationId = Yii::$app->request->post('relation_id')) {
            Util::returnJSON(array('success' => false, 'message' => Yii::t('app', 'Invalid request')));
        }


        $transaction = Yii::app()->db->beginTransaction();
        try {
            $relation = Relation::model()->findByPk($relationId);

            $rdid = $relation->dataset_id;
            $rrdoi = $relation->related_doi;
            $rrid = $relation->relationship_id;

            $relation2 = Relation::model()->findByAttributes(array(
              'dataset_id' => Dataset::model()->findByAttributes(array('identifier' => $rrdoi))->id,
              'related_doi' => Dataset::model()->findByPk($rdid)->identifier,
              'relationship_id' => $rrid,
              ));

            if ($relation->delete() && $relation2->delete()) {
                  $transaction->commit();

                  Util::returnJSON(array("success" => true));
            }

            $transaction->rollback();

            Util::returnJSON(array("success" => false));
        } catch (Exception $e) {
            $message = $e->getMessage();
            Yii::log(print_r($message, true), 'error');
            $transaction->rollback();

            Util::returnJSON(array("success" => false, "message" => Yii::t("app", "Delete Error.")));
        }
    }
}
