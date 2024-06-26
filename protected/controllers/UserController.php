<?php

class UserController extends Controller {
    const PAGE_SIZE = 10;

    public $layout='//layouts/main';

    /**
     * @var string specifies the default action
     */
    public $defaultAction='admin';

    /**
     * @var CActiveRecord the currently loaded data model instance.
     */
    private $_user;

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow',  # all users
                'actions'=>array(
                    'create', 'confirm', 'welcome',
                    'sendActivationEmail', 'emailWelcome'),
                'users'=>array('*'),
            ),
            array('allow', # logged in users
                    'actions'=>array('welcome', 'activationNeeded', 'changePassword','view_profile','edit_profile'),
                    'users'=>array('@'),
                ),
            array('allow', # admins
                'actions'=>array('list', 'show', 'delete','admin','update','view','newsletter'),
                'roles'=>array('admin'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
     * Shows a particular user.
     */
    public function actionShow() {
        #if (Yii::app()->user->checkAccess('showUser')) {
        #}
        $this->render('show',array('user'=>$this->loadUser()));
    }

    # Create new account
    public function actionCreate() {
        $user = new User;
        $user->newsletter=false;
        $this->performAjaxValidation($user);
        if (isset($_POST['User'])) {
            //$user->attributes = $_POST['User'];
            $user->setScenario('insert');
            $attrs = $_POST['User'];
            $user->attributes=$attrs;
            $user->email = strtolower(trim($attrs['email']));
            $user->username = $user->email;
            $user->first_name = trim($attrs['first_name']);
            $user->last_name = trim($attrs['last_name']);
            $user->password = $attrs['password'];
            $user->password_repeat = $attrs['password_repeat'];
            $user->first_name=$attrs['first_name'];
            $user->last_name=$attrs['last_name'];
            $user->affiliation = $attrs['affiliation'];
            $user->preferred_link = $attrs['preferred_link'];

            if (!Yii::app()->user->checkAccess('admin')) {
                $user->role = 'user'; // avoid send POST hacking
            }
            $user->newsletter=$attrs['newsletter'];
            $user->previous_newsletter_state = !$user->newsletter;

            if (in_array($_SERVER['GIGADB_ENV'], ["dev","CI"]) && "testCaptcha" !== $attrs['verifyCode']) {
                Yii::log("Because we are on {$_SERVER['GIGADB_ENV']}, captcha value is overridden for automated acceptance test", 'warning');
                Yii::log("To exercise captcha validation, use 'testCaptcha' in the form", 'warning');
                $_SESSION["captcha"] = $attrs['verifyCode'];
            }

            if ($user->validate('insert')) {
                $user->encryptPassword();

                if ($user->save(false)) {
                    $this->sendActivationEmail($user);
                    if($user->newsletter)
                        Yii::app()->newsletter->addToMailing($user->email);

                    $this->redirect(array('welcome', 'id'=>$user->id));
                }
                else {
                    Yii::log(__FUNCTION__."> create failed", 'warning');
                }
            }
            else {
                Yii::log(__FUNCTION__."> validation failed", 'warning');
            }
        }
        $this->render('create', array('model'=>$user)) ;
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax']==='user-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /**
     * Updates a particular user.
     * If update is successful, the browser will be redirected to the 'show' page.
     */
    public function actionUpdate() {
        $user = $this->loadUser();
        $this->performAjaxValidation($user);
        #if (!Yii::app()->user->checkAccess('updateOwnUser', array('user'=>$user))) {
        #    Yii::log(__FUNCTION__."> Unauthorized", 'debug');
        #    throw new CHttpException(403, 'You are not authorized to perform this action.');
        #}




        if (isset($_POST['User'])) {
            $user->attributes = $_POST['User'] ;
            $attrs = $_POST['User'];

            $password = $user->password_new = $attrs['password'];
            $user->password_repeat = $attrs['password_repeat'];

            if (!Yii::app()->user->checkAccess('admin')) {
                $user->role = 'user';
            }

            if ($user->validate('update')) {
                if ($password != '') {
                    $user->encryptPassword();
                }

                if ($user->save(false)) {

                    Yii::app()->user->setFlash('notice', 'Updated');
                    $this->redirect(array('user/show/id/'.$user->id));
                }
                else {
                    Yii::log(__FUNCTION__."> Update failed", 'warning');
                }

            }
            else {
                Yii::log(__FUNCTION__."> validation failed", 'warning');
            }
        }
        $user->password = $user->password_repeat = '';
        $this->render('update', array('model'=>$user));

    }

    /**
     * Deletes a particular user.
     * If deletion is successful, the browser will be redirected to the 'list' page.
     */
    public function actionDelete() {
        if (Yii::app()->request->isPostRequest) {
            # we only allow deletion via POST request
            $user =User::model()->findbyPk($_GET['id']) ;
            $auth = Yii::app()->authManager;
            $auth->revoke($user->getRole(), $user->email);
            //$user->delete();

            $user->is_activated = False;
            if ($user->save(False))
                $this->redirect(array('admin'));
            else{
                Yii::log("Saving user error" . print_r($user->getErrors(), true), 'error');
            }

        }
        else {
            throw new CHttpException(500,'Invalid request. Please do not repeat this request again.');
        }
    }

    /**
     * Lists all users.
     */
    public function actionList() {
        $criteria = new CDbCriteria;

        $pages = new CPagination(User::model()->count($criteria));
        $pages->pageSize = self::PAGE_SIZE;
        $pages->applyLimit($criteria);

        $sort = new CSort('User');
        $sort->applyOrder($criteria);

        $userList = User::model()->findAll($criteria);

        $this->render('list',array(
            'userList'=>$userList,
            'pages'=>$pages,
            'sort'=>$sort,
        ));
    }

    /**
     * Manages all users.
     */
    public function actionAdmin() {
        $model=new User('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['User']))
            $model->setAttributes($_GET['User']);

        $this->loadBaBbqPolyfills = true;
        $this->render('admin',array(
            'model'=>$model,
        ));
    }

    public function actionNewsletter(){

     $result = User::model()->findAllBySql("select email,first_name, last_name, affiliation from gigadb_user where newsletter=true order by id;");


     $this->renderPartial('newsletter',array(
            'models'=>$result,
        ));


    }

    # Confirm email works
	public function actionConfirm() {
		$key = $_GET['key'] ;
		User::model()->updateAll(array('is_activated'=>1),'id=:unique_id', array(':unique_id'=>$key));

        $user = User::model()->findByAttributes(array('id' => $key));
        if ($user->is_activated) {
            $this->sendNotificationEmail($user);
        }

		$this->render('confirm', array('user'=>$user));
	}

    public function actionWelcome() {
        $this->render('welcome', array('user'=>$this->loadUser())) ;
    }

    public function actionActivationNeeded() {
        $this->render('activationNeeded', array('user'=>$this->loadUser())) ;
    }

    public function actionView_Profile() {
        $model = new EditProfileForm();
        $model->user_id = Yii::app()->user->id;

        $user = $this->loadUser(Yii::app()->user->id);
        $model->first_name = $user->first_name;
        $model->last_name = $user->last_name;
        $model->email = $user->email;
        $model->affiliation = $user->affiliation;
        $model->newsletter = $user->newsletter;
        $model->preferred_link = $user->preferred_link;

        $current = $user->newsletter;

        if (isset($_POST['EditProfileForm'])) {
            $model->attributes = $_POST['EditProfileForm'];

            if ($model->validate()) {
                if($model->updateInfo()) {
                    $new = $model->newsletter;
                    if($new && !$current) {
                        Yii::log('add new mailing', 'debug');
                        $success = Yii::app()->newsletter->addToMailing($model->email, $model->first_name, $model->last_name);
                    }
                    if(!$new && $current) {
                        Yii::log('remove mailing', 'debug');
                        $success = Yii::app()->newsletter->removeFromMailing($model->email);
                    }
                    $this->redirect('/user/view_profile');
                }
            } else {
                Yii::log(print_r($model->getErrors(), true), 'debug');
            }
        }

        // # query to return datasets authored by the user
        // $adCriteria= new CDbCriteria;
        // $adCriteria->join = "JOIN dataset_author da on da.dataset_id = t.id join author a on da.author_id = a.id join gigadb_user u on a.gigadb_user_id = u.id";
        // $adCriteria->condition = "u.id=:user_id";
        // $adCriteria->params=array(':user_id'=>Yii::app()->user->id);
        // $authoredDatasets = Dataset::model()->findAll($adCriteria);

        # query to return the author ids linked ot the user
        $linkedAuthors = array();
        $authoredDatasets = array();

        $linked_author = $user->getLinkedAuthor();
        // Yii::log(print_r($linked_author, true), 'debug');
        if (!empty($linked_author)) {
            $linkedAuthors = $linked_author->getIdenticalAuthors();
            $linkedAuthors[] = $linked_author->id;
            // Yii::log(print_r($linkedAuthors, true), 'debug');

            # return datasets associated to linked authors
            // Yii::log(print_r($authoredDatasets, true), 'debug');
            foreach ($linkedAuthors as $author) {
                $authoredDatasets = array_merge($authoredDatasets, Author::model()->findByPk($author)->datasets);
            }
            // Yii::log(print_r($authoredDatasets, true), 'debug');
        }

        $searchRecord = SearchRecord::model()->findAllByAttributes(array('user_id' => Yii::app()->user->id));
        //Yii::log(print_r($searchRecord, true), 'debug');

        $uploadedDatasets = Dataset::model()->findAllByAttributes(array('submitter_id'=> Yii::app()->user->id), array('order'=>'upload_status'));
        $this->render('view_profile',array('model'=>$model,'searchRecord'=>$searchRecord,'uploadedDatasets'=>$uploadedDatasets, 'authoredDatasets' => $authoredDatasets, 'linkedAuthors' => $linkedAuthors));
    }

    # Change user password
    public function actionChangePassword() {
        $model = new ChangePasswordForm();
        $model->user_id = Yii::app()->user->id;
        $user = User::model()->findByattributes(array('id'=> Yii::app()->user->id));
        $model->newsletter = $user->newsletter;

        if(isset($_POST['ajax']) && $_POST['ajax']==='ChangePassword-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        if(isset($_POST['ChangePasswordForm']))
        {
            $model->attributes=$_POST['ChangePasswordForm'];
            $model->newsletter=$_POST['ChangePasswordForm']['newsletter'];
            if($model->validate() && $model->changePass())
                $this->redirect('/user/view_profile');
        }
        $model->password = $model->confirmPassword = '';
        $this->render('changePassword',array('model'=>$model));
    }

    public function actionPasswordChanged() {
        $this->render('passwordChanged') ;
    }

    # Send account activation email
    private function sendActivationEmail($user) {
        $recipient = $user->email;
        $subject = Yii::app()->params['email_prefix'] . "Welcome to " . Yii::app()->name;
        $url = $this->createAbsoluteUrl('user/confirm', array('key' => $user->id));
        $body = $this->renderPartial('emailWelcome',array('url'=>$url),true);
        try {
            Yii::app()->mailService->sendHTMLEmail(Yii::app()->params['adminEmail'], $recipient, $subject, $body);
        } catch (Swift_TransportException $ste) {
            Yii::log("Problem sending account activation email - " . $ste->getMessage(), "error");
        }
        Yii::log("Sent account activation email to $recipient, $subject");
    }

    public function actionEmailWelcome() {
        $this->renderPartial('emailWelcome');
    }


    # Send notification email to admins about new user
    private function sendNotificationEmail($user) {
        $recipient = Yii::app()->params['notify_email'];
        $subject = Yii::app()->params['email_prefix'] . "New user registration";
        $url = $this->createAbsoluteUrl('user/show', array('id'=>$user->id));
        $body = <<<EO_MAIL
New user registration
Email: {$user->email}
Name:  {$user->first_name} {$user->last_name}

$url
EO_MAIL;

        try {
            Yii::app()->mailService->sendHTMLEmail(Yii::app()->params['adminEmail'], $recipient, $subject, $body);
        } catch (Swift_TransportException $ste) {
            Yii::log("Problem sending password email - " . $ste->getMessage(), "error");
        }
        Yii::log(__FUNCTION__."> Sent email to $recipient, $subject");
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the primary key value. Defaults to null, meaning using the 'id' GET variable
     */
    private function loadUser($id=null) {
        if ($this->_user===null) {
            if ($id!==null || isset($_GET['id'])) {
                $this->_user=User::model()->findbyPk($id!==null ? $id : $_GET['id']) ;
            }
            if ($this->_user===null)
                throw new CHttpException(500,'The requested user does not exist.') ;
        }
        return $this->_user ;
    }




    /**
     * Executes any command triggered on the admin page.
     */
    protected function processAdminCommand() {
        if (isset($_POST['command'], $_POST['id']) && $_POST['command']==='delete') {
            $this->loadUser($_POST['id'])->delete();
            // reload the current page to avoid duplicated delete actions
            $this->refresh();
        }
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->layout = 'datasetpage';
        $this->render('view',array(
            'model'=>$this->loadModel($id)
        ));
    }

    public function loadModel($id)
    {
        $model=User::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

}


