<?php

declare(strict_types=1);

class UserController extends Controller
{
    const PAGE_SIZE = 10;

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
     *
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array(
                'allow',  # all users
                'actions' => array(
                    'create', 'confirm', 'welcome',
                    'emailWelcome'
                ),
                'users'   => array('*'),
            ),
            array(
                'allow', # logged in users
                'actions' => array('changePassword', 'view_profile', 'edit_profile'),
                'users'   => array('@'),
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    # Create new account
    public function actionCreate() {
        $user = new User;
        $user->newsletter = false;
        $this->performAjaxValidation($user);
        if ($attrs = Yii::$app->request->post('User')) {
            $user->setScenario('insert');
            $user->attributes = $attrs;
            $user->email = strtolower(trim($attrs['email']));
            $user->username = $user->email;
            $user->first_name = trim($attrs['first_name']);
            $user->last_name = trim($attrs['last_name']);
            $user->password = $attrs['password'];
            $user->password_repeat = $attrs['password_repeat'];
            $user->first_name = $attrs['first_name'];
            $user->last_name = $attrs['last_name'];
            $user->affiliation = $attrs['affiliation'];
            $user->preferred_link = $attrs['preferred_link'];
            $user->role = 'user';

            $user->newsletter = $attrs['newsletter'];
            $user->previous_newsletter_state = !$user->newsletter;

            if (in_array($_SERVER['GIGADB_ENV'], ["dev", "CI"]) && "testCaptcha" !== $attrs['verifyCode']) {
                Yii::log("Because we are on {$_SERVER['GIGADB_ENV']}, captcha value is overridden for automated acceptance test", 'warning');
                Yii::log("To exercise captcha validation, use 'testCaptcha' in the form", 'warning');
                $_SESSION["captcha"] = $attrs['verifyCode'];
            }

            $securityManager = new \yii\base\Security();

            if ($user->validate()) {
                $user->encryptPassword();

                if ($user->save(false)) {
                    $data = json_encode(
                        [
                            'id' => $user->id,
                            'ts' => time(),
                            'rand' => $securityManager->generateRandomString(32),
                        ]);

                    $token = $securityManager->hashData($data, Yii::$app->params['secretEmailKey']);
                    $user->activation_token = $token;

                    if(!$user->save()) {
                        throw new CHttpException(500, 'An error occurred');
                    }

                    $this->sendActivationEmail($user, $token);
                    if ($user->newsletter)
                        Yii::app()->newsletter->addToMailing($user->email);

                    $this->redirect(array('welcome', 'id' => $user->id));
                } else {
                    Yii::log(__FUNCTION__ . "> create failed", 'warning');
                }
            } else {
                Yii::log(__FUNCTION__ . "> validation failed", 'warning');
            }
        }
        $this->render('create', array('model' => $user));
    }

    protected function performAjaxValidation($model) {
        $ajax = Yii::$app->request->post('ajax');
        if ($ajax && $ajax === 'user-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    # Confirm email works
    public function actionConfirm() {
        $token = Yii::$app->request->get('key');
        $expire = 3600;
        $security = new \yii\base\Security();
        $data = $security->validateData($token, Yii::$app->params['secretEmailKey']);

        if (!$data) {
            throw new CHttpException(400, 'Invalid link');
        }
        $decoded = json_decode($data, true);
        $userId = $decoded['id'];
        $timestamp = $decoded['ts'];
        $randomString = $decoded['rand'];

        if ($timestamp + $expire < time()) {
            throw new CHttpException(400, 'The link is expired');
        }

        $user = User::model()->findByPk($userId);
        if (!$user || $user->activation_token !== $token) {
            throw new CHttpException(400, 'Invalid token');
        }

        $user->activation_token = null;
        $user->is_activated = true;

        if ($user->save()) {
            $this->sendNotificationEmail($user);
        } else {
            Yii::app()->user->setFlash('danger', 'An error ocured and your account is not activated');
        }

        $this->render('confirm', array('user' => $user));
    }

    public function actionWelcome(int $id) {
        $this->render('welcome', array('user' => $this->loadModel($id)));
    }

    public function actionView_Profile() {
        $model = new EditProfileForm();
        $model->user_id = Yii::app()->user->id;

        $user = $this->loadModel(Yii::app()->user->id);
        $model->first_name = $user->first_name;
        $model->last_name = $user->last_name;
        $model->email = $user->email;
        $model->affiliation = $user->affiliation;
        $model->newsletter = $user->newsletter;
        $model->preferred_link = $user->preferred_link;

        $current = $user->newsletter;

        if ($attrs = Yii::$app->request->post('EditProfileForm')) {
            $model->attributes = $attrs;
            $model->scenario = 'update';

            if ($model->validate()) {
                if ($model->updateInfo()) {
                    $new = $model->newsletter;
                    if ($new && !$current) {
                        Yii::log('add new mailing', 'debug');
                        $success = Yii::app()->newsletter->addToMailing($model->email, $model->first_name, $model->last_name);
                    }
                    if (!$new && $current) {
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

        $uploadedDatasets = Dataset::model()->findAllByAttributes(array('submitter_id' => Yii::app()->user->id), array('order' => 'upload_status'));
        $this->render('view_profile', array('model' => $model, 'searchRecord' => $searchRecord, 'uploadedDatasets' => $uploadedDatasets, 'authoredDatasets' => $authoredDatasets, 'linkedAuthors' => $linkedAuthors));
    }

    # Change user password
    public function actionChangePassword() {
        $model = new ChangePasswordForm();
        $model->user_id = Yii::app()->user->id;
        $user = User::model()->findByattributes(array('id' => Yii::app()->user->id));

        if (Yii::$app->request->post('ajax') && 'ChangePassword-form' === Yii::$app->request->post('ajax')) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        if ($changePasswordFormAttr = Yii::$app->request->post('ChangePasswordForm')) {
            $model->attributes = $changePasswordFormAttr;
            $model->scenario = 'update';
            if ($model->validate() && $model->changePass()) {
                Yii::app()->user->setFlash('success', 'Password successfully updated');
                $this->redirect('/user/view_profile');
            }
        }
        $model->password = $model->confirmPassword = '';
        $this->render('changePassword', array('model' => $model));
    }

    public function actionPasswordChanged() {
        $this->render('passwordChanged');
    }

    public function actionSendActivationEmail(int $id) {
        $user = User::model()->findByPk($id);

        if (!$user) {
            throw new CHttpException(404,'User not found');
        }

        $isSuccessful = $this->sendActivationEmail($user);

        if($isSuccessful) {
            Yii::app()->user->setFlash('success', 'A confirmation email has been resent!');
        } else {
            Yii::app()->user->setFlash('danger', 'Unable to send confirmation email!');
        }

        return $this->redirect(array('welcome', 'id'=>$user->id));
    }

    # Send account activation email
    private function sendActivationEmail(User $user, string $token) {
        $url = $this->createAbsoluteUrl('user/confirm', array('key' => $token));

        $recipient = $user->email;
        $subject = Yii::app()->params['email_prefix'] . "Welcome to " . Yii::app()->name;
        $body = $this->renderPartial('emailWelcome', array('url' => $url), true);
        try {
            $isSent = Yii::app()->mailService->sendHTMLEmail(Yii::app()->params['adminEmail'], $recipient, $subject, $body);
            Yii::log("Sent account activation email to $recipient, $subject");

            return $isSent;

        } catch (Swift_TransportException $ste) {
            Yii::log("Problem sending account activation email - " . $ste->getMessage(), "error");
            return false;
        }
    }

    public function actionEmailWelcome() {
        $this->renderPartial('emailWelcome');
    }


    # Send notification email to admins about new user
    private function sendNotificationEmail($user) {
        $recipient = Yii::app()->params['notify_email'];
        $subject = Yii::app()->params['email_prefix'] . "New user registration";
        $url = $this->createAbsoluteUrl('user/show', array('id' => $user->id));
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
        Yii::log(__FUNCTION__ . "> Sent email to $recipient, $subject");
    }

    public function loadModel(int $id) {
        $model = User::model()->findByPk($id);
        if (!$model) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

}


