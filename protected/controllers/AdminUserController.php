<?php

declare(strict_types=1);

class AdminUserController extends Controller
{
    const PAGE_SIZE = 10;
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
                'allow', # admins
                'actions' => array('list', 'show', 'delete', 'admin', 'update', 'view', 'newsletter'),
                'roles'   => array('admin'),
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    private function performAjaxValidation($model)
    {
        $ajax = Yii::$app->request->post('ajax');

        if ($ajax === 'admin-user-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /**
     * Shows a particular user.
     */
    public function actionShow() {
        if (!$id = Yii::$app->request->get('id')) {
            throw new CHttpException(400, 'Invalid request');
        }

        $this->render('show',array('user'=> $this->loadModel((int)$id)));
    }
    /**
     * Updates a particular user.
     * If update is successful, the browser will be redirected to the 'show' page.
     */
    public function actionUpdate(int $id)
    {
        $user = $this->loadModel($id);
        $this->performAjaxValidation($user);

        if ($attrs = Yii::$app->request->post('User')) {
            $user->email = $user->username = strtolower(trim($attrs['email']));
            $user->first_name = trim($attrs['first_name']);
            $user->last_name = trim($attrs['last_name']);
            $user->role = $attrs['role'];
            $user->affiliation = $attrs['affiliation'];
            $user->preferred_link = $attrs['preferred_link'];
            $user->newsletter = $attrs['newsletter'];
            $user->is_activated = $attrs['is_activated'];

            $user->scenario = 'update';
            if ($user->save()) {
                Yii::app()->user->setFlash('notice', 'Updated');
                $this->redirect(array('adminUser/view/id/' . $user->id));
            } else {
                Yii::log(__FUNCTION__ . '> Update failed', 'warning');
            }
        }

        $this->render('update', array('model' => $user));
    }

    /**
     * Deletes a particular user.
     * If deletion is successful, the browser will be redirected to the 'list' page.
     */
    public function actionDelete()
    {
        throw new CHttpException(404, 'Unable to delete the author');
        /*if (!Yii::app()->request->isAjaxRequest || !Yii::app()->request->isPostRequest) {
            throw new CHttpException(403, 'Forbidden');
        }

        $user = User::model()->findbyPk(Yii::$app->request->get('id'));

        $auth = Yii::app()->authManager;

        $auth->revoke($user->getRole(), $user->email);
        echo CJSON::encode(array(
            'success' => true,
            'message' => 'Enregistrement effectué',
            'data' => $user->getRole(),
        ));
        Yii::app()->end();
        //$user->delete();

        $user->is_activated = false;
        if ($user->save())
            $this->redirect(array('admin'));
        else {
            Yii::log('Saving user error' . print_r($user->getErrors(), true), 'error');
        }*/
    }

    /**
     * Lists all users.
     */
    public function actionList()
    {
        $criteria = new CDbCriteria;

        $pages = new CPagination(User::model()->count($criteria));
        $pages->pageSize = self::PAGE_SIZE;
        $pages->applyLimit($criteria);

        $sort = new CSort('User');
        $sort->applyOrder($criteria);

        $userList = User::model()->findAll($criteria);

        $this->render('list', array(
            'userList' => $userList,
            'pages'    => $pages,
            'sort'     => $sort,
        ));
    }

    /**
     * Manages all users.
     */
    public function actionAdmin()
    {
        $model = new User('search');
        $model->unsetAttributes();  // clear any default values
        if ($attrs = Yii::$app->request->get('User'))
            $model->setAttributes($attrs);

        $this->loadBaBbqPolyfills = true;
        $this->render('admin', array(
            'model' => $model,
        ));
    }

    public function actionNewsletter()
    {
        $result = User::model()->findAllBySql('select email,first_name, last_name, affiliation from gigadb_user where newsletter=true order by id;');

        $this->renderPartial('newsletter', array(
            'models' => $result,
        ));
    }

    /**
     * Displays a particular model.
     *
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView(int $id)
    {
        $this->render('view', array(
            'model' => $this->loadModel($id)
        ));
    }

    private function loadModel(int $id)
    {
        $model = User::model()->findByPk($id);

        if (!$model) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }
}


