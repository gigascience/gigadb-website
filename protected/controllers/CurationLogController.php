<?php

declare(strict_types=1);

class CurationLogController extends Controller
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
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionAdmin(): void
    {
        $model = new CurationLog('search');
        $model->unsetAttributes();  // clear any default values
        if ($curationLog = Yii::$app->request->get('CurationLog')) {
            $model->setAttributes($curationLog);
        }

        $this->loadBaBbqPolyfills = true;

        $this->render('admin', array('model' => $model));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate(int $id): void
    {
        $model = new CurationLog();
        /** @var User $userModel */
        $userModel = User::model();

        /** @var CWebApplication $app */
        $app = Yii::app();

        if ($curationLog = Yii::$app->request->post('CurationLog')) {
            $model->attributes = $curationLog;
            $model->creation_date = date("Y-m-d");
            $model->last_modified_date = null;
            $model->dataset_id = $id;
            $username = $userModel->find('id=:user_id', array(':user_id' => $app->user->id));

            $username = $username->first_name . ' ' . $username->last_name;
            $model->created_by = $username;

            if ($model->save()) {
                $this->redirect(array('view','id' => $model->id));
            }
        }

        $this->render('create', array('model' => $model, 'dataset_id' => $id));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionUpdate(): void
    {
        /** @var CWebApplication $app */
        $app = Yii::app();

        /** @var User $userModel */
        $userModel = User::model();

        if (!$id = Yii::$app->request->get('id')) {
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
        }

        $model = $this->loadModel((int) $id);

        if ($curationLog = Yii::$app->request->post('CurationLog')) {
            $model->attributes = $curationLog;
            $model->last_modified_date = date("Y-m-d");
            $username = $userModel->find('id=:user_id', array(':user_id' => $app->user->id));
            $username = $username->first_name . ' ' . $username->last_name;
            $model->last_modified_by = $username;
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

    /**
     * Manages all models.
     */
    public function actionView(int $id): void
    {
        $this->render('view', array('model' => $this->loadModel($id)));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     */
    public function loadModel(int $id): CurationLog
    {
        /** @var CurationLog $curationLogModel */
        $curationLogModel = CurationLog::model();

        $model = $curationLogModel->findByPk($id);
        if (!$model) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }
}
