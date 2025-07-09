<?php

declare(strict_types=1);

class AdminDatasetProjectController extends Controller
{
    /**
     * @return string[] action filters
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
     * @return array<int, array<int|string, list<string>|string>> access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // admin only
                'actions' => array('admin','delete','index','view','create','update'),
                'roles' => array('admin'),
            ),
                        array('allow',
                                'actions' => array('create1', 'delete1','addProject','deleteProject'),
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
        $model = new DatasetProject();

        if ($datasetProject = Yii::$app->request->post('DatasetProject')) {
            $model->attributes = $datasetProject;
            if ($model->save()) {
                $this->redirect(array('view','id' => $model->id));
            }
        }

        $this->render('create', array('model' => $model));
    }

    //TODO: not used atm
    public function actionCreate1()
    {
        $model = new DatasetProject();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $model->dataset_id = 1;

        //update
        if (!isset($_SESSION['projects']))
            $_SESSION['projects'] = array();

        $projects = $_SESSION['projects'];

        if (isset($_POST['DatasetProject'])) {

            $project_id = $_POST['DatasetProject']['project_id'];

            $model->project_id = $project_id;
            $id = 0;
            if ($this->storeProject($model, $id)) {

                $name = Project::model()->findByAttributes(array('id' => $project_id))->name;

                $newItem = array('id' => $id, 'name' => $name);


                array_push($projects, $newItem);

                $_SESSION['projects'] = $projects;

                $model = new DatasetProject;

            }
        }


        $project_model = new CArrayDataProvider($projects);

        $this->render('create1', array(
            'model' => $model,
            'project_model' => $project_model,
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

        if ($datasetProject = Yii::$app->request->post('DatasetProject')) {
            $model->attributes = $datasetProject;
            if ($model->save()) {
                $this->redirect(array('view','id' => $model->id));
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

    // TODO: not used atm
    public function actionDelete1($id)
    {
        if (isset($_SESSION['projects'])) {
            $info = $_SESSION['projects'];
            foreach ($info as $key => $value) {
                if ($value['id'] == $id) {
                    unset($info[$key]);
                    $_SESSION['projects'] = $info;

                    $condition = "id=" . $id;
                    DatasetProject::model()->deleteAll($condition);
                    $this->redirect("/adminDatasetProject/create1");
                }
            }
        }
    }

    /**
     * Lists all models.
     */
    public function actionIndex(): void
    {
        $dataProvider = new CActiveDataProvider('DatasetProject');

        $this->render('index', array('dataProvider' => $dataProvider));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin(): void
    {
        $model = new DatasetProject('search');
        $model->unsetAttributes();  // clear any default values

        if ($datasetProject = Yii::$app->request->get('DatasetProject')) {
            $model->setAttributes($datasetProject);
        }

        $this->loadBaBbqPolyfills = true;

        $this->render('admin', array('model' => $model));
    }

    //TODO: not used atm
    private function storeProject(&$model, &$id)
    {


        if (isset($_SESSION['dataset_id'])) {
            $dataset_id = $_SESSION['dataset_id'];

            $model->dataset_id = $dataset_id;
            if (!$model->save()) {
                $model->addError("error", "save error");
                return false;
            }

            $id = $model->id;
        }

        return true;
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     */
    public function loadModel(int $id): DatasetProject
    {
        $model = DatasetProject::model()->findByPk($id);
        if (!$model) {
            throw new CHttpException(404, 'The requested Dataset Project does not exist.');
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
        if (Yii::$app->request->post('ajax') === 'dataset-project-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }


    public function actionAddProject(): void
    {
        $datasetId = Yii::$app->request->post('dataset_id');
        $projectId = Yii::$app->request->post('project_id');

        if (!$datasetId || !$projectId) {
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
        }

        $project = Project::model()->findByPk($projectId);

        if (!$project) {
            Util::returnJSON(array("success" => false, "message" => Yii::t("app", "Cannot find project.")));
        }

        $dp = DatasetProject::model()->findByAttributes(array('dataset_id' => $datasetId, 'project_id' => $projectId));

        if ($dp) {
            Util::returnJSON(array("success" => false, "message" => Yii::t("app", "This project has been added already.")));
        }

        $dp = new DatasetProject();
        $dp->dataset_id = $datasetId;
        $dp->project_id = $projectId;

        if ($dp->save()) {
            Util::returnJSON(array("success" => true));
        }

        Util::returnJSON(array("success" => false, "message" => Yii::t("app", "Save Error.")));
    }

    public function actionDeleteProject(): void
    {
        if (!$dpId = Yii::$app->request->post('dp_id')) {
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
        }

        $dp = DatasetProject::model()->findByPk($dpId);

        if ($dp->delete()) {
            Util::returnJSON(array("success" => true));
        }

        Util::returnJSON(array("success" => false, "message" => Yii::t("app", "Delete Error.")));
    }
}
