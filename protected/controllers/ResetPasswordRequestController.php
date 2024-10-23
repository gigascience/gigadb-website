<?php

/**
 * Provides reset password functionality for users
 */
class ResetPasswordRequestController extends Controller
{
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    /**
     * Specifies access control rules.
     *
     * The reset function can be used by anonymous users but it will
     * only work if a token is provided.
     *
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',  // anonymous users
                'actions' => array('forgot', 'reset', 'thanks'),
                'users' => array('?'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
     * Display thanks page
     *
     * This page lets the user know their request to reset their password has
     * been successfully submitted.
     */
    public function actionThanks()
    {
        $this->render('thanks');
    }

    /**
     * Displays request password page
     */
    public function actionForgot()
    {
        $forgotPasswordForm = new ForgotPasswordForm;
        if (isset($_POST['ForgotPasswordForm'])) {
            $forgotPasswordForm->email = $_POST['ForgotPasswordForm']['email'];
            if ($forgotPasswordForm->validate()) {
                $user = User::model()->findByAttributes(array('email' => $forgotPasswordForm->email));
                if ($user !== null) {
                    Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": Found user account for ".$forgotPasswordForm->email, 'info');
                    // Check if user has any valid reset password requests
                    $request = ResetPasswordRequest::model()->findByAttributes(array('gigadb_user_id' => $user->id));
                    if($request == null || $request->isExpired())  // User has no reset password requests or request has expired
                    {
                        // Remove any expired password requests belonging to user
                        if($request != null)
                            $request->delete();

                        $resetPasswordRequest = new ResetPasswordRequest();
                        $resetPasswordRequest->generateResetToken($user);
                        $resetPasswordRequest->save();
                        $this->sendPasswordEmail($resetPasswordRequest);
                    }
                    else  // User has unexpired reset token
                    {
                        Yii::app()->user->setFlash('fail-reset-password', "Too many password requests - please wait till current request expires");
                        // Display request reset password page with flash message
                        $this->redirect('forgot');
                    }
                }
                else {
                    Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": User account not found for ".$forgotPasswordForm->email, 'info');
                }
            }
            $this->redirect('thanks');
        }
        else {
            $this->render('forgot', array('model' => $forgotPasswordForm));
        }
    }

    /**
     * Displays password reset page if token is verified for user to access
     * password reset page
     *
     * Token is validated with a database lookup of selector, and
     * re-calculating hash of verifier in URL and compare with
     * hash in database
     *
     * Looks for /resetpasswordrequest/reset?token={token}
     */
    public function actionReset()
    {
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            $userIdentity = new PasswordResetTokenUserIdentity($token);
            if ($userIdentity->authenticate()) {
                Yii::log("[INFO] [" . __CLASS__ . ".php] " . __FUNCTION__ . ": User is authenticated!", 'info');
                $model = new ResetPasswordForm();
                // Find user id associated with selector part in URL
                $selectorFromURL = substr($token, 0, 20);
                $resetPasswordRequest = ResetPasswordRequest::model()->findByAttributes(array('selector' => $selectorFromURL));
                $model->user_id = $resetPasswordRequest->gigadb_user_id;
                // Update password with user's submitted change password form
                if (isset($_POST['ResetPasswordForm'])) {
                    $model->attributes=$_POST['ResetPasswordForm'];
                    if($model->validate() && $model->changePass()) {
                        // Delete token so it cannot be used again
                        $resetPasswordRequest->delete();
                        // Go to login page after updating password
                        Yii::app()->user->setFlash('success-reset-password','Your password has been successfully reset. Please login again.');
                        $this->redirect('/site/login');
                    }
                }
                else {
                    // Display reset password page
                    $model->password = $model->confirmPassword = '';
                    $this->render('reset', array('model' => $model));
                }
            } else {
                Yii::log("Token not valid" , "info");
                Yii::app()->user->setFlash('fail-reset-password','Your password reset token is invalid. Please request another.');
                // Display request reset password page
                $this->redirect('forgot');
            }
        }
        else {
            Yii::log("No token provided" , "info");
            // Display request reset password page
            $this->redirect('forgot');
        }
    }

    /**
     * Deletes all ResetPasswordRequests belonging to a user
     *
     * @param $userId
     * @return void
     * @throws CDbException
     */
    private function removeResetPasswordRequests($userId)
    {
        $resetPasswordRequests = ResetPasswordRequest::model()->findAll(array("condition" => "gigadb_user_id = $userId"));
        foreach ($resetPasswordRequests as $resetPasswordRequest)
            $resetPasswordRequest->delete();
    }

    /**
     * Sends an email to a user who has filled in the reset password form page.
     *
     * The email contains a link to the page that allows the user to reset their
     * password. Used by generateResetToken() function.
     *
     * @param $resetPasswordRequest
     */
    private function sendPasswordEmail($resetPasswordRequest)
    {
        // Create URL for user to verify password reset token
        $url = $this->createAbsoluteUrl('site/reset');
        $url = $url."?token=".$resetPasswordRequest->getToken();
        Yii::log("URL for email: " . $url, "info");

        $user = User::model()->findByattributes(array('id' => $resetPasswordRequest->gigadb_user_id));
        $recipient = $user->email;
        $subject = Yii::app()->params['email_prefix'] . "Password reset";
        $body = $this->renderPartial('emailReset', array('url' => $url), true);
        try {
            Yii::app()->mailService->sendHTMLEmail(Yii::app()->params['adminEmail'], $recipient, $subject, $body);
        } catch (Swift_TransportException $ste) {
            Yii::log("Problem sending password reset email to user - " . $ste->getMessage(), "error");
        }
    }
}
