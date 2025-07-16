<?php

declare(strict_types=1);

class AdminUserCommandController extends Controller
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
                'actions' => array('admin','delete','index','view','validate','reject','update'),
                'roles' => array('admin'),
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
     * Validate a claim by linking author with gigadb_user and updating user_command
     */
    public function actionValidate(int $id): void
    {
        /** @var CWebApplication $app */
        $app = Yii::app();

        $claim = $this->loadModel($id);

        if ('claim_author' !== $claim->action_label) {
            throw new CHttpException(400, 'Invalid request');
        }

        /** @var Author $authorModel */
        $authorModel = Author::model();
        /** @var User $userModel */
        $userModel = User::model();

        $author = $authorModel->findbyPk($claim->actionable_id);
        $requester = $userModel->findbyPk($claim->requester_id);

        if (!$author || !$requester) {
            throw new CHttpException(404, 'No author or requester found');
        }

        $author->gigadb_user_id = $requester->id;
        if ($author->save()) {
            Yii::log(__FUNCTION__ . "> author (" . $author->id . ")/user (" . $requester->id . ") linking has been performed", 'warning');
            $claim->status = "linked";
            $claim->actioner_id = $app->user->id;
            $now = new Datetime();
            $claim->action_date = $now->format(DateTime::ISO8601);

            //TODO no rollback?
            if ($claim->save()) {
                Yii::log(__FUNCTION__ . "> claim " . $claim->id . " updated as 'linked'", 'warning');
            }

            $claim->delete(); //claim record when validated is not needed (job done) and there is log/email for audit
        } else {
            Yii::log(__FUNCTION__ . "> author (" . $author->id . ")/user (" . $requester->id . ") linking failed", 'warning');
            $claim->status = "validation error";
            $claim->actioner_id = $app->user->id;
            if ($claim->save()) {
                Yii::log(__FUNCTION__ . "> claim " . $claim->id . " updated as 'validation error'", 'warning');
            }
        }

        $this->redirect(array('adminUser/view/', 'id' => $requester->id));

	}

    /**
     * Reject a claim
     */
    public function actionReject(int $id): void
    {
        /** @var CWebApplication $app */
        $app = Yii::app();

        $claim = $this->loadModel($id);

        //claim record when rejected needs to be kept to prevent someone repeatedly claiming despite rejection
        if ("claim_author" === $claim->action_label) {
            $claim->status = "rejected";
            $claim->actioner_id = $app->user->id;
            $now = new Datetime();
            $claim->action_date = $now->format(DateTime::ISO8601);
            if ($claim->save()) {
                $app->user->setFlash('success', "Claimed rejected. No linking performed");
                Yii::log(__FUNCTION__ . "> claim " . $claim->id . " updated as 'rejected'", 'warning');
            }
        }

		$this->redirect(array('adminUser/update','id' => $claim->requester_id));

	}

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id the ID of the model to be updated
     */
    public function actionUpdate(int $id): void
    {
        $model = $this->loadModel($id);

        if ($userCommand = Yii::$app->request->post('UserCommand')) {
            $model->attributes = $userCommand;
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
        $dataProvider = new CActiveDataProvider('UserCommand');

        $this->render('index', array('dataProvider' => $dataProvider));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin(): void
    {
        $model = new UserCommand('search');
        $model->unsetAttributes();  // clear any default values
        if ($userCommand = Yii::$app->request->get('UserCommand')) {
            $model->setAttributes($userCommand);
        }

        $this->render('admin', array('model' => $model));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param int $id the ID of the model to be loaded
     */
    public function loadModel(int $id): UserCommand
    {
        /** @var UserCommand $userCommandModel */
        $userCommandModel = UserCommand::model();

        $model = $userCommandModel->findByPk($id);
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
        if (Yii::$app->request->post('ajax') === 'user-command-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
