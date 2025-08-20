<?php

declare(strict_types=1);

class AdminExternalLinkController extends Controller
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
                                 'actions' => array('create1', 'delete1','autocomplete','addExLink', 'deleteExLink'),
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
        $model = new ExternalLink();

        if ($externalLink = Yii::$app->request->post('ExternalLink')) {
            $model->attributes = $externalLink;

            if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('create', array('model' => $model));
    }

    //TODO: not used atm
    public function actionDelete1($id)
    {
        if (isset($_SESSION['externalLinks'])) {
            $info = $_SESSION['externalLinks'];
            foreach ($info as $key => $value) {
                if ($value['id'] == $id) {
                    unset($info[$key]);
                    $_SESSION['externalLinks'] = $info;
                    $condition = 'id=' . $id;
                    ExternalLink::model()->deleteAll($condition);
                    $this->redirect("/adminExternalLink/create1");
                }
            }
        }
    }

    //TODO: not used atm
    private function storeExternalLink(&$model, &$id)
    {


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

    //TODO: not used atm
    public function actionCreate1()
    {
        $model = new ExternalLink();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $model->dataset_id = 1;

        //update
        if (!isset($_SESSION['externalLinks']))
            $_SESSION['externalLinks'] = array();

        $externalLinks = $_SESSION['externalLinks'];


        if (isset($_POST['ExternalLink'])) {

            $url = $_POST['ExternalLink']['url'];
            if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url)) {
                $model->addError('error', 'Error: The Url is not valid!');
            } else {
                $type_id = 2;

                $model->url = $url;
                $model->external_link_type_id = $type_id;
                $id = 0;
                if ($this->storeExternalLink($model, $id)) {
                    $type_info = ExternalLinkType::model()->findByAttributes(array('id' => $type_id))->name;

                    $newItem = array('id' => $id, 'url' => $url, 'type_info' => $type_info, 'type_id' => $type_id);


                    array_push($externalLinks, $newItem);

                    $_SESSION['externalLinks'] = $externalLinks;
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

    public function actionAutocomplete(): void
    {
        /** @var CWebApplication $app */
        $app = Yii::app();
        if ($partial_external_link_term = Yii::$app->request->get('term')) {
            $autoCompleteServiceForExternalLink = $app->autocomplete;
            $result = $autoCompleteServiceForExternalLink->findSpeciesLike($partial_external_link_term);

            echo CJSON::encode($result);
            Yii::app()->end();
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

        if ($externalLink = Yii::$app->request->post('ExternalLink')) {
            $model->attributes = $externalLink;
            if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->id));
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
        $dataProvider = new CActiveDataProvider('ExternalLink');

        $this->render('index', array('dataProvider' => $dataProvider));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin(): void
    {
        $model = new ExternalLink('search');
        $model->unsetAttributes();  // clear any default values

        if ($externalLink = Yii::$app->request->get('ExternalLink')) {
            $model->setAttributes($externalLink);
        }

        $this->loadBaBbqPolyfills = true;

        $this->render('admin', array('model' => $model));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param int $id the ID of the model to be loaded
     */
    public function loadModel(int $id): ExternalLink
    {
        $model = ExternalLink::model()->findByPk($id);
        if (!$model) {
            throw new CHttpException(404, Yii::t('yii', 'External Link was not found.'));
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
        if (Yii::$app->request->post('ajax') === 'external-link-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionAddExLink(): void
    {
        $datasetId = Yii::$app->request->post('dataset_id');
        $url = Yii::$app->request->post('url');
        $externalLinkType = Yii::$app->request->post('externalLinkType');

        if (!$datasetId || !$url || !$externalLinkType) {
            Util::returnJSON(array('success' => false, 'message' => Yii::t('app', "Can't add the external link")));
        }


        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url)) {
            Util::returnJSON(array("success" => false,"message" => Yii::t("app", "The URL is invalid. Please enter a valid URL including http://")));
        }

        $exLink = ExternalLink::model()->findByAttributes(array('dataset_id' => $datasetId, 'url' => $url));
        if ($exLink) {
            Util::returnJSON(array("success" => false, "message" => Yii::t("app", "This external link has been added already.")));
        }

        $exLink = new ExternalLink();
        $exLink->dataset_id = $datasetId;
        $exLink->url = $url;
        $exLink->external_link_type_id = $externalLinkType;

        if ($exLink->save()) {
            Util::returnJSON(array("success" => true));
        }

        Util::returnJSON(array("success" => false, "message" => Yii::t("app", "Save Error.")));
    }

    public function actionDeleteExLink(): void
    {
        if (!$externalLinkId = Yii::$app->request->post('exLink_id')) {
            Util::returnJSON(array('success' => false, 'message' => Yii::t('app', 'Delete Error.')));
        }

        $exLink = ExternalLink::model()->findByPk($externalLinkId);
        if ($exLink->delete()) {
            Util::returnJSON(array("success" => true));
        }

        Util::returnJSON(array("success" => false, "message" => Yii::t("app", "Delete Error.")));
    }
}
