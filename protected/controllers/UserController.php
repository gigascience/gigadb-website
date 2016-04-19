<?php

class UserController extends Controller {
    const PAGE_SIZE = 10;

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
            array('allow',  // all users
                'actions'=>array('create', 'confirm', 'welcome',
                				'reset', 'resetThanks',
                                'sendActivationEmail','emailWelcome','emailReset'),
                'users'=>array('*'),
            ),
            array('allow', # logged in users
                    'actions'=>array('welcome', 'activationNeeded', 'changePassword','view_profile','edit_profile'),
                    'users'=>array('@'),
                ),
            array('allow', # admins
                'actions'=>array('list', 'show', 'delete','admin','update','view'),
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
        $this->performAjaxValidation($user);
        if (isset($_POST['User'])) {
            //$user->attributes = $_POST['User'];

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

            if ($user->validate('insert')) {
                $user->encryptPassword();

                if ($user->save(false)) {
                    $this->sendActivationEmail($user);
                    if($user->newsletter)
                        Utils::addToMailing($user->email);

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

        $this->render('admin',array(
            'model'=>$model,
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

    # Look up user and reset password
    public function actionReset() {
    	$email='';
        //$this->render('reset',array('user'=>$this->loadUser())) ;
        if (isset($_REQUEST["reset_user"])) {
            $reset_user = $_REQUEST["reset_user"];
            Yii::log("reset $reset_user", 'debug');
            $user = User::model()->findByAttributes(array('email' => $reset_user));
            if ($user !== null) {
                Yii::log(__FUNCTION__."> reset found user $reset_user", 'debug');
                $user->password = $user->generatePassword(8);
                $user->encryptPassword();

                if ($user->save()) {
                    $this->sendPasswordEmail($user);
                }
                else {
                    Yii::log(__FUNCTION__."> Error: could not save new user password", 'error');
                }
            }
            else {
                Yii::log(__FUNCTION__."> User account not found for user $reset_user", 'error');
            }
            $this->redirect(array('user/resetThanks'));
        }
        $this->render('reset', array('email'=>$email));
    }

    public function actionResetThanks() {
        $this->render('resetThanks');
    }

    # Reset password and send it to user in email
    public function actionLostPass() {
        $user = $this->loadUser();
        if (isset($_POST['User'])) {
            $user->attributes = $_POST['User'];
            $user->password = $user->generatePassword(8);
            $user->encryptPassword();

            if ($user->save()) {
                $this->sendPasswordEmail($user);
            }
            else {
                Yii::log("Error saving new user password", 'error');
            }
        }
        $this->render('lostpass', array('user'=>$user)) ;
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
                        $success = Utils::addToMailing($model->email, array('FNAME'=>$model->first_name, 'LNAME'=>$model->last_name));
                    }
                    if(!$new && $current) {
                        Yii::log('remove mailing', 'debug');
                        $success = Utils::removeFromMailing($model->email);
                    }
                    $this->redirect('/user/view_profile');
                }
            } else {
                Yii::log(print_r($model->getErrors(), true), 'debug');
            }
        }

       

        $searchRecord = SearchRecord::model()->findAllByAttributes(array('user_id' => Yii::app()->user->id));
        //Yii::log(print_r($searchRecord, true), 'debug');

        $uploadedDatasets = Dataset::model()->findAllByAttributes(array('submitter_id'=> Yii::app()->user->id), array('order'=>'upload_status'));
        $this->render('view_profile',array('model'=>$model,'searchRecord'=>$searchRecord,'uploadedDatasets'=>$uploadedDatasets));
    }

    # Change user password
    public function actionChangePassword() {
        $model = new ChangePasswordForm();
        $model->user_id = Yii::app()->user->id;

        if(isset($_POST['ajax']) && $_POST['ajax']==='ChangePassword-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        if(isset($_POST['ChangePasswordForm']))
        {
            $model->attributes=$_POST['ChangePasswordForm'];
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
        $app_email_name = Yii::app()->params['app_email_name'];
        $app_email = Yii::app()->params['app_email'];
        $email_prefix = Yii::app()->params['email_prefix'];
        $headers = "From: $app_email_name <$app_email>\r\n"; //optional header fields
        $headers .= "Content-type: text/html\r\n";
        ini_set('sendmail_from', $app_email);

        $recipient = $user->email.',';
        $recipient.='database@gigasciencejournal.com';
        $subject = $email_prefix . "Welcome to " . Yii::app()->name;
        $url = $this->createAbsoluteUrl('user/confirm', array('key' => $user->id));
        $body = $this->renderPartial('emailWelcome',array('url'=>$url),true);
        mail($recipient, $subject, $body, $headers);
        Yii::log("Sent email to $recipient, $subject");
    }

    // Send password email
    private function sendPasswordEmail($user) {
        $app_email_name = Yii::app()->params['app_email_name'];
        $app_email = Yii::app()->params['app_email'];
        $email_prefix = Yii::app()->params['email_prefix'];
        $headers = "From: $app_email_name <$app_email>\r\n"; //optional header fields
        $headers .= "Content-type: text/html\r\n";
        ini_set('sendmail_from', $app_email);

        $recipient = $user->email;
        $subject = $email_prefix . "Password reset";
        $password_unhashed = $user->passwordUnHashed;
        $url = $this->createAbsoluteUrl('site/login');
        $body = $this->renderPartial('emailReset',array('url'=>'www.gigadb.org/site/login','password_unhashed'=>$password_unhashed),true);
        mail($recipient, $subject, $body, $headers);
        Yii::log(__FUNCTION__."> Sent email to $recipient, $subject");
    }

    public function actionEmailWelcome() {
        $this->renderPartial('emailWelcome');
    }

    public function actionEmailReset() {
        $this->renderPartial('emailReset');
    }

    public function actionSendActivationEmail() {
        $user = $this->loadUser();
        Yii::log(__FUNCTION__."> Sending activation email to user ". $user->email, 'debug');
        $this->sendActivationEmail($user);
        $this->render('activationNeeded', array('user'=>$user));
    }

    # Send notification email to admins about new user
    private function sendNotificationEmail($user) {
        $app_email_name = Yii::app()->params['app_email_name'];
        $app_email = Yii::app()->params['app_email'];
        $email_prefix = Yii::app()->params['email_prefix'];
        $headers = "From: $app_email_name <$app_email>\r\n"; //optional header fields
        ini_set('sendmail_from', $app_email);

        $recipient = Yii::app()->params['notify_email'];
        $subject = $email_prefix . "New user registration";
        $url = $this->createAbsoluteUrl('user/show', array('id'=>$user->id));
        $body = <<<EO_MAIL
New user registration
Email: {$user->email}
Name:  {$user->first_name} {$user->last_name}

$url

EO_MAIL;
        mail($recipient, $subject, $body, $headers);
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


    # Like loadUser, but expects unique_id in id field
    private function loadUserByUnique($id=null) {
        if ($this->_user===null) {
            if ($id!==null || isset($_GET['id'])) {
                #$this->_user=User::model()->findbyPk($id!==null ? $id : $_GET['id']) ;
                $value = $id!==null ? $id : $_GET['id'];
                $this->_user=User::model()->findByAttributes(array('unique_id' => $value));
            }
            if ($this->_user===null)
                throw new CHttpException(500,'The requested user does not exist.');
        }
        return $this->_user;
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


