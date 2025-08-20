<?php

declare(strict_types=1);

class FunderController extends Controller
{
    private ?Funder $_model = null;

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

            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'users' => array('@'),
                'roles' => array('admin'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     */
    public function actionView(): void
    {
        $this->render('view', array('model' => $this->loadModel()));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate(): void
    {
        $model = new Funder();

        if ($funder = Yii::$app->request->post('Funder')) {
            $model->attributes = $funder;
            if ($model->save()) {
                $this->redirect(array('view','id' => $model->id));
            }
        }

        $this->render('create', array('model' => $model));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionUpdate(): void
    {
        $model = $this->loadModel();

        if ($funder = Yii::$app->request->post('Funder')) {
            $model->attributes = $funder;
            if ($model->save()) {
                $this->redirect(array('view','id' => $model->id));
            }
        }

        $this->render('update', array('model' => $model));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     */
    public function actionDelete(): void
    {
        if (!Yii::app()->request->isPostRequest) {
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
        }

        // we only allow deletion via POST request
        $this->loadModel()->delete();

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
        $dataProvider = new CActiveDataProvider('Funder');

        $this->render('index', array('dataProvider' => $dataProvider));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin(): void
    {
        $model = new Funder('search');
        $model->unsetAttributes();  // clear any default values

        if ($funder = Yii::$app->request->get('Funder')) {
            $model->setAttributes($funder);
        }

        $this->loadBaBbqPolyfills = true;

        $this->render('admin', array('model' => $model));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     */
    public function loadModel(): Funder
    {
        /** @var Funder $funderModel */
        $funderModel = Funder::model();

        if (!$this->_model) {
            if ($id = Yii::$app->request->get('id')) {
                $this->_model = $funderModel->findbyPk($id);
            }
            if (!$this->_model) {
                throw new CHttpException(404, 'The requested page does not exist.');
            }
        }
        return $this->_model;
    }

    /**
     * Performs the AJAX validation.
     *
     * @param CModel $model the model to be validated
     */
    protected function performAjaxValidation(CModel $model): void
    {
        if (Yii::$app->request->post('ajax') === 'funder-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
