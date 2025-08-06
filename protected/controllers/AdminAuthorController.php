<?php

declare(strict_types=1);

class AdminAuthorController extends Controller
{
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
     *
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow', // admin only
                'actions' => array('admin', 'delete', 'index', 'view', 'create', 'update', 'prepareUserLink', 'prepareAuthorMerge', 'linkUser', 'unlinkUser', 'mergeAuthors', 'identicalAuthorsGraph', 'unmerge'),
                'roles'   => array('admin'),
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     *
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView(int $id)
    {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new Author;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if ($attrs = Yii::$app->request->post('Author')) {
            $model->attributes = $attrs;
            if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate(int $id)
    {
        $model = $this->loadModel($id);
        $model->custom_name = $model->getDisplayName();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if ($attrs = Yii::$app->request->post('Author')) {
            $model->attributes = $attrs;
            if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     *
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete(int $id)
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

        echo CJSON::encode(['success' => 'ok']);
        Yii::app()->end();
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider('Author');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Create a session to allow admin to search an author to link to the session-saved user
     */
    public function actionPrepareUserLink(int $user_id, bool $abort = false)
    {
        if ($user_id && !$abort) {
            if (preg_match("/^\d+$/", (string) $user_id)) {
                Yii::app()->session['attach_user'] = $user_id;
                Yii::log(__FUNCTION__ . "> new session var: attach_user = " . $user_id, 'info');
                if (!empty(Yii::app()->session['merge_author'])) {
                    unset(Yii::app()->session['merge_author']);
                }
            }
            $this->redirect(array('adminAuthor/admin'));
        } else if ($user_id && $abort) {
            unset(Yii::app()->session['attach_user']);
            Yii::log(__FUNCTION__ . "> unset session var: attach_user", 'info');
            $this->redirect(array('adminUser/view', 'id' => $user_id));
        } else {
            Yii::log(__FUNCTION__ . "> There is a problem with parameters received", 'error');
            $this->redirect(array('adminAuthor/admin'));
        }
    }


    /**
     * Create a session to allow admin to search an author to link to the author
     */
    public function actionPrepareAuthorMerge(int $origin_author_id, bool $abort = false)
    {
        if ($origin_author_id && !$abort) {
            if (preg_match("/^\d+$/", (string) $origin_author_id)) {
                Yii::app()->session['merge_author'] = $origin_author_id;
                Yii::log(__FUNCTION__ . "> new session var: merge_author = " . $origin_author_id, 'info');
                if (!empty(Yii::app()->session['attach_user'])) {
                    unset(Yii::app()->session['attach_user']);
                }
            }
            $this->redirect(array('adminAuthor/admin'));
        } else if ($origin_author_id && $abort) {
            unset(Yii::app()->session['merge_author']);
            Yii::log(__FUNCTION__ . "> unset session var: merge_author", 'info');
            $this->redirect(array('adminAuthor/view', 'id' => $origin_author_id));
        } else {
            Yii::log(__FUNCTION__ . "> There is a problem with parameters received", 'error');
            $this->redirect(array('adminAuthor/admin'));
        }
    }

    public function actionLinkUser(int $id)
    {
        $author = $this->loadModel($id);
        if (isset(Yii::app()->session['attach_user'])) {
            $user = User::model()->findByPk(Yii::app()->session['attach_user']);
            if ($user) {
                $author->gigadb_user_id = $user->id;
                if ($author->save()) {
                    Yii::log(
                        __FUNCTION__ . "> author (" . $author->id . ")/user (." . $user->id . ".) linking has been performed",
                        'info'
                    );
                    if ($user->id === Yii::app()->session['attach_user']) {
                        unset(Yii::app()->session['attach_user']);
                    }

                    $this->redirect(array('adminUser/view', 'id' => $user->id));
                }
                Yii::log(__FUNCTION__ . "> error while updating gigadb_user_id in author. " . implode(" ", $author->getErrors()['gigadb_user_id']), 'error');
                Yii::app()->user->setFlash('error', 'Could not link to this author. ' . CHtml::link('View author', ['adminUser/view', 'id' => $user->id]));

                $this->redirect(array('adminAuthor/admin'));

            }
            Yii::app()->user->setFlash('error', "user to link doesn't exist");
            Yii::log(__FUNCTION__ . "> user to link doesn't exist", 'error');

            $this->render('view', array('model' => $author));
            Yii::app()->end();
        }

        Yii::log(__FUNCTION__ . "> attach_user is not set in session", 'error');
        Yii::app()->user->setFlash('error', 'An error has occurred');

        $this->redirect(array('adminAuthor/admin'));
    }

    public function actionUnlinkUser(int $id, int $user_id)
    {
        $model = $this->loadModel($id);
        $user = User::model()->findByPk($user_id);

        if (!$model || !$user) {
            throw new CHttpException(400, 'Invalid request');
        }

         if ($user_id !== $model->gigadb_user_id) {
            Yii::log(__FUNCTION__ . "> mismatch between loaded user and user id in author model", 'warning');
            Yii::app()->user->setFlash('alert', 'Mismatch between users');
        } else {
            $model->gigadb_user_id = null;
            if ($model->save()) {
                Yii::log(
                    __FUNCTION__ . "> author (" . $model->id . ")/user (." . $user->id . ".) linking has been removed",
                    'info'
                );
            } else {
                Yii::log(__FUNCTION__ . "> error while updating gigadb_user_id in author. " . implode(" ", $model->getErrors()['gigadb_user_id']), 'error');
                Yii::app()->user->setFlash('alert', 'An error occured');
            }
        }

        $this->redirect(array('adminUser/update', 'id' => $user->id));
    }

    public function actionMergeAuthors(int $origin_author, int $target_author)
    {
        $origin = $this->loadModel($origin_author);

        if (isset(Yii::app()->session['merge_author'])) {
            $merge_author = Yii::app()->session['merge_author'];
            if ($merge_author === $origin_author || $merge_author === $target_author) {
                if ($origin->mergeAsIdenticalWithAuthor($target_author)) {
                    Yii::log(__FUNCTION__ . "> merging author {$origin_author} with {$target_author} was successful", 'info');
                    Yii::app()->user->setFlash('success', "Merging authors completed successfully.");
                    $this->redirect(array('adminAuthor/view', 'id' => $origin_author));
                } else {
                    Yii::log(__FUNCTION__ . "> merging author {$origin_author} with {$target_author} failed", 'error');
                }
            } else {
                Yii::log(__FUNCTION__ . "> merge_author {$merge_author} doesn't match GET parameters ({$origin_author},{$target_author}) to mergeAuthors", 'error');
            }
        } else {
            Yii::log(__FUNCTION__ . "> merge_author is not set in session", 'error');
        }

        Yii::app()->user->setFlash('error', 'An error occured');
        $this->redirect(array('adminAuthor/admin'));
    }

    public function actionUnmerge(int $id)
    {
        $model = $this->loadModel($id);
        if ($model->unMerge()) {
            Yii::app()->user->setFlash('success', "author unmerged from other authors");
            $this->redirect(array('adminAuthor/view', 'id' => $id));
        } else {
            Yii::app()->user->setFlash('error', "unmerging from graph has encountered an error");
            $this->redirect(array('adminAuthor/view', 'id' => $id));
        }
    }

    public function actionIdenticalAuthorsGraph(int $id)
    {
        $author = $this->loadModel($id);
        $authors = $author->getIdenticalAuthorsDisplayName();

        echo implode(", ", $authors);
        Yii::app()->end();
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        if ($attachUser = Yii::$app->request->get('attach_user')) {
            if (preg_match("/^\d+$/", $attachUser)) {
                Yii::app()->session['attach_user'] = $attachUser;
            } else if ("abort" === $attachUser) {
                unset(Yii::app()->session['attach_user']);
                $this->redirect(array('admin'));
            }
        }

        $model = new Author('search');
        $model->unsetAttributes();  // clear any default values
        if ($attrs = Yii::$app->request->get('Author')) {
            $model->setAttributes($attrs);
        }

        $this->loadBaBbqPolyfills = true;
        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @param integer the ID of the model to be loaded
     */
    public function loadModel(int $id)
    {
        $model = Author::model()->findByPk($id);
        if (!$model) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    /**
     * Performs the AJAX validation.
     *
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        $ajax = Yii::$app->request->post('ajax');
        if ($ajax === 'author-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
