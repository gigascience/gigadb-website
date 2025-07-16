<?php

declare(strict_types=1);

class AdminAuthorController extends Controller
{
    /**
     *
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
     *
     * @return array<int, array<int|string, list<string>|string>> access control rules
     */
    public function accessRules(): array
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
        $model = new Author();

        if ($author = Yii::$app->request->post('Author')) {
            $model->attributes = $author;
            if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('create', array('model' => $model));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id the ID of the model to be updated
     */
    public function actionUpdate(int $id): void
    {
        $model = $this->loadModel($id);
        $model->custom_name = $model->getDisplayName();

        if ($author = Yii::$app->request->post('Author')) {
            $model->attributes = $author;
            if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('update', array('model' => $model));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     *
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

        echo CJSON::encode(['success' => 'ok']);
        Yii::app()->end();
    }

    /**
     * Lists all models.
     */
    public function actionIndex(): void
    {
        $dataProvider = new CActiveDataProvider('Author');

        $this->render('index', array('dataProvider' => $dataProvider));
    }

    /**
     * Create a session to allow admin to search an author to link to the session-saved user
     */
    public function actionPrepareUserLink(int $user_id, bool $abort = false)
    {
        /** @var CWebApplication $app */
        $app = Yii::app();

        if ($user_id && !$abort) {
            if (preg_match("/^\d+$/", (string)$user_id)) {
                $app->session['attach_user'] = $user_id;
                Yii::log(__FUNCTION__ . "> new session var: attach_user = " . $user_id, 'info');
                if (isset($app->session['merge_author'])) {
                    unset($app->session['merge_author']);
                }
            }

            $this->redirect(array('adminAuthor/admin'));
        }
        if ($user_id && $abort) {
            unset(Yii::app()->session['attach_user']);
            Yii::log(__FUNCTION__ . "> unset session var: attach_user", 'info');

            $this->redirect(array('adminUser/view', 'id' => $user_id));
        }

        Yii::log(__FUNCTION__ . "> There is a problem with parameters received", 'error');
        $this->redirect(array('adminAuthor/admin'));
    }


    /**
     * Create a session to allow admin to search an author to link to the author
     */
    public function actionPrepareAuthorMerge(int $origin_author_id, bool $abort = false)
    {
        /** @var CWebApplication $app */
        $app = Yii::app();

        if ($origin_author_id && !$abort) {
            if (preg_match("/^\d+$/", (string)$origin_author_id)) {
                $app->session['merge_author'] = $origin_author_id;
                Yii::log(__FUNCTION__ . "> new session var: merge_author = " . $origin_author_id, 'info');
                if (isset($app->session['attach_user'])) {
                    unset($app->session['attach_user']);
                }
            }

            $this->redirect(array('adminAuthor/admin'));
        }
        if ($origin_author_id && $abort) {
            unset(Yii::app()->session['merge_author']);
            Yii::log(__FUNCTION__ . "> unset session var: merge_author", 'info');

            $this->redirect(array('adminAuthor/view', 'id' => $origin_author_id));
        }

        Yii::log(__FUNCTION__ . "> There is a problem with parameters received", 'error');
        $this->redirect(array('adminAuthor/admin'));
    }

    public function actionLinkUser(int $id): void
    {
        /** @var CWebApplication $app */
        $app = Yii::app();

        $author = $this->loadModel($id);
        if (!isset($app->session['attach_user'])) {
            Yii::log(__FUNCTION__ . '> attach_user is not set in session', 'error');
            $app->user->setFlash('danger', "Attached user is not set in session");

            $this->render('view', array('model' => $author));
            Yii::app()->end();
        }

        $user = User::model()->findByPk($app->session['attach_user']);
        if (!$user) {
            $app->user->setFlash('danger', "user to link doesn't exist");
            Yii::log(__FUNCTION__ . "> user to link doesn't exist", 'error');

            $this->render('view', array('model' => $author));
            Yii::app()->end();
        }

        $author->gigadb_user_id = $user->id;
        if ($author->save()) {
            Yii::log(
                __FUNCTION__ . "> author (" . $author->id . ")/user (." . $user->id . ".) linking has been performed",
                'info'
            );
            if ((int)$user->id === (int)$app->session['attach_user']) {
                unset($app->session['attach_user']);
            }

            $this->redirect(array('adminUser/view', 'id' => $user->id));
        }
        Yii::log(__FUNCTION__ . "> error while updating gigadb_user_id in author. " . implode(" ", $author->getErrors()['gigadb_user_id']), 'error');
        if ((int)$user->id === (int)$app->session['attach_user']) {
            unset($app->session['attach_user']);
        }
        $app->user->setFlash('danger', "An error occured while saving the author");

        $this->redirect(array('adminUser/view', 'id' => $user->id));
	}

    public function actionUnlinkUser(int $id, int $user_id): void
    {
        /** @var CWebApplication $app */
        $app = Yii::app();
        $model = $this->loadModel($id);
        $user = User::model()->findByPk($user_id);

        if (!$model || !$user) {
            Yii::log(__FUNCTION__ . '> no user model could be loaded', 'warning');

            $this->redirect(array('site/admin'));
        }

        if ($user_id !== $model->gigadb_user_id) {
            Yii::log(__FUNCTION__ . "> mismatch between loaded user and user id in author model", 'warning');
            $app->user->setFlash('danger', 'mismatch between loaded user and user id in author model');

            $this->redirect(array('adminUser/update', 'id' => $user->id));
         }

        $model->gigadb_user_id = null;
        if ($model->save()) {
            Yii::log(
                __FUNCTION__ . "> author (" . $model->id . ")/user (." . $user->id . ".) linking has been removed",
                'info'
            );

            $this->redirect(array('adminUser/update', 'id' => $user->id));
        }
        Yii::log(__FUNCTION__ . "> error while updating gigadb_user_id in author. " . implode(" ", $model->getErrors()['gigadb_user_id']), 'error');
        Yii::app()->user->setFlash('danger', 'Error while saving the related user in author');


        $this->redirect(array('site/admin'));
    }

    public function actionMergeAuthors(int $origin_author, int $target_author): void
    {
        /** @var CWebApplication $app */
        $app = Yii::app();
        $origin = $this->loadModel($origin_author);

        if (!isset($app->session['merge_author'])) {
            Yii::log(__FUNCTION__ . '> merge_author is not set in session', 'error');
            $app->user->setFlash('danger', "Merged author is not set in session");

            $this->redirect(array('adminAuthor/admin'));
        }

        $merge_author = (int)$app->session['merge_author'];
        if ($merge_author === $origin_author || $merge_author === $target_author) {
            if ($origin->mergeAsIdenticalWithAuthor($target_author)) {
                Yii::log(__FUNCTION__ . "> merging author {$origin_author} with {$target_author} was successful", 'info');
                $app->user->setFlash('success', "Merging authors completed successfully.");

                $this->redirect(array('adminAuthor/view', 'id' => $origin_author));
            } else {
                Yii::log(__FUNCTION__ . "> merging author {$origin_author} with {$target_author} failed", 'error');
                $app->user->setFlash('danger', "Merging authors failed.");

                $this->redirect(array('adminAuthor/admin'));
            }
        } else {
            Yii::log(__FUNCTION__ . "> merge_author {$merge_author} doesn't match GET parameters ({$origin_author},{$target_author}) to mergeAuthors", 'error');
            $app->user->setFlash('danger', "Mismatch in origin and target author");

            $this->redirect(array('adminAuthor/admin'));
        }
    }

    public function actionUnmerge(int $id): void
    {
        /** @var CWebApplication $app */
        $app = Yii::app();
        $model = $this->loadModel($id);

        if (!$model->unMerge()) {
            $app->user->setFlash('error', 'unmerging from graph has encountered an error');

            $this->redirect(array('adminAuthor/view', 'id' => $id));
        }

        $app->user->setFlash('success', "author unmerged from other authors");

        $this->redirect(array('adminAuthor/view', 'id' => $id));
    }

    public function actionIdenticalAuthorsGraph(int $id): void
    {
        $author = $this->loadModel($id);
        $authors = $author->getIdenticalAuthorsDisplayName();

        echo implode(", ", $authors);
        Yii::app()->end();
    }

    /**
     * Manages all models.
     */
    public function actionAdmin(): void
    {
        /** @var CWebApplication $app */
        $app = Yii::app();
        if ($attachUser = Yii::$app->request->get('attach_user')) {
            if (preg_match("/^\d+$/", $attachUser)) {
                $app->session['attach_user'] = $attachUser;
            } elseif ("abort" === $attachUser) {
                unset($app->session['attach_user']);

                $this->redirect(array('admin'));
            }
        }

        $model = new Author('search');
        $model->unsetAttributes();  // clear any default values
        if ($author = Yii::$app->request->get('Author')) {
            $model->setAttributes($author);
        }

        $this->loadBaBbqPolyfills = true;

        $this->render('admin', array('model' => $model));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @param int $id the ID of the model to be loaded
     */
    public function loadModel(int $id): Author
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
     * @param CModel $model the model to be validated
     */
    protected function performAjaxValidation(CModel $model): void
    {
        if (Yii::$app->request->post('ajax') === 'author-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
