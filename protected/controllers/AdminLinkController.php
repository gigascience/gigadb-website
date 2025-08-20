<?php

declare(strict_types=1);

class AdminLinkController extends Controller
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
                'actions' => array('create1', 'delete1','addLink', 'deleteLink'),
                'users' => array('@'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param int $id the ID of the model to be displayed
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
        $model = new Link();

        if ($link = Yii::$app->request->post('Link')) {
            $model->attributes = $link;

            if ($model->save()) {
                $this->redirect(array('view','id' => $model->id));
            }
        }

        $this->render('create', array('model' => $model));
    }

    //TODO: not used atm
    public function storeLink(&$model, &$id)
    {


        if (isset($_SESSION['dataset_id'])) {
            $dataset_id = $_SESSION['dataset_id'];


            $model->dataset_id = $dataset_id;
            if (!$model->save()) {
                $model->addError('keyword', 'Error: Link is not stored!');
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
        $model = new Link();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);


        $model->dataset_id = 1;


        $link_database = array();
        //retrieve the database
        if (!isset($_SESSION['link_database'])) {
            $models = Prefix::model()->findAll();
            $database = array();
            foreach ($models as $m) {
                $value = $m->prefix;
                array_push($database, $value);
            }
            sort($database);
            $_SESSION['link_database'] = $database;
        }
        $link_database = $_SESSION['link_database'];

        //update
        if (!isset($_SESSION['links']))
            $_SESSION['links'] = array();

        $links = $_SESSION['links'];

        if (isset($_POST['Link'])) {

            $link = $link_database[$_POST['Link']['database']] . ":" . $_POST['Link']['acc_num'];

            $is_primary = 1;

            $model->attributes = $_POST['Link'];
            $model->link = $link;
            $model->is_primary = $is_primary;
            $id = 0;
            if ($this->storeLink($model, $id)) {
                $link_type = "ext_acc_mirror";
                if ($is_primary == 0)
                    $link_type = "ext_acc_link";

                $newItem = array('id' => $id, 'link' => $link, 'link_type' => $link_type);


                array_push($links, $newItem);

                $_SESSION['links'] = $links;
                $model = new Link;
            }
        }

        $link_model = new CArrayDataProvider($links);

        $this->render('create1', array(
            'model' => $model,
            'link_model' => $link_model,
            'link_database' => $link_database
        ));
    }

    //TODO: not used atm
    public function actionDelete1($id)
    {
        if (isset($_SESSION['links'])) {
            $info = $_SESSION['links'];
            foreach ($info as $key => $value) {
                if ($value['id'] == $id) {
                    unset($info[$key]);
                    $_SESSION['links'] = $info;
                    $condition = 'id=' . $id;
                    Link::model()->deleteAll($condition);

                    $this->redirect("/adminLink/create1");
                }
            }
        }
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id the ID of the model to be updated
     */
    public function actionUpdate(int $id): void
    {
        $model = $this->loadModel($id);

        if ($link = Yii::$app->request->post('Link')) {
            $model->attributes = $link;
            if ($model->save()) {
                $this->redirect(array('view','id' => $model->id));
            }
        }

        $this->render('update', array('model' => $model));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param int $id the ID of the model to be deleted
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

    /**
     * Lists all models.
     */
    public function actionIndex(): void
    {
        $dataProvider = new CActiveDataProvider('Link');

        $this->render('index', array('dataProvider' => $dataProvider));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin(): void
    {
        $model = new Link('search');
        $model->unsetAttributes();  // clear any default values

        if ($link = Yii::$app->request->get('Link')) {
            $model->setAttributes($link);
        }

        $this->loadBaBbqPolyfills = true;

        $this->render('admin', array('model' => $model));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param int $id the ID of the model to be loaded
     */
    public function loadModel(int $id): Link
    {
        /** @var Link $linkModel */
        $linkModel = Link::model();

        $model = $linkModel->findByPk($id);
        if (!$model) {
            throw new CHttpException(404, "Can't find the link");
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
        if (Yii::$app->request->post('ajax') === 'link-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionAddLink(): void
    {
        $datasetId = Yii::$app->request->post('dataset_id');
        $database = Yii::$app->request->post('database');
        $accNum = Yii::$app->request->post('acc_num');

        if (!$datasetId || !$database || !$accNum) {
            Util::returnJSON(array('success' => false, 'message' => Yii::t('app', 'Invalid request')));
        }

        $linkVal = $database . ":" . $accNum;

        $link = Link::model()->findByAttributes(array('dataset_id' => $datasetId, 'link' => $linkVal));
        if ($link) {
            Util::returnJSON(array("success" => false, "message" => Yii::t("app", "This link has been added already.")));
        }

        $link = new Link();
        $link->dataset_id = $datasetId;
        $link->is_primary = true;
        $link->link = $linkVal;

        if ($link->save()) {
            Util::returnJSON(array("success" => true));
        }

        Util::returnJSON(array("success" => false, "message" => Yii::t("app", "Save Error.")));
    }

    public function actionDeleteLink(): void
    {
        if (!$linkId = Yii::$app->request->post('link_id')) {
            Util::returnJSON(array("success" => false, "message" => Yii::t('app', 'Invalid request')));
        }

        $link = Link::model()->findByPk($linkId);
        if ($link->delete()) {
            Util::returnJSON(array("success" => true));
        }

        Util::returnJSON(array("success" => false, "message" => Yii::t("app", "Delete Error.")));
    }
}
