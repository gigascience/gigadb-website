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
        $this->layout = "new_main";
        $this->render('thanks');
    }
    
    /**
     * Displays request password page
     */
    public function actionForgot()
    {
        $this->layout = "new_main";
        $forgotPasswordForm = new ForgotPasswordForm;
        if (isset($_POST['ForgotPasswordForm'])) {
            $forgotPasswordForm->email = $_POST['ForgotPasswordForm']['email'];
            if ($forgotPasswordForm->validate()) {
                $user = User::model()->findByAttributes(array('email' => $forgotPasswordForm->email));
                if ($user !== null) {
                    Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": Found user account for ".$forgotPasswordForm->email, 'info');
                    try {
                        $this->generateResetToken($user);
                    }
                    catch (Exception $e)
                    {
                        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": Too many password requests made by: ".$user, 'info');
                    }
                }
                else {
                    Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": User account not found for ".$user, 'info');
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
        $this->layout = "new_main";
        
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            $userIdentity = new PasswordResetTokenUserIdentity($token);
            if ($userIdentity->authenticate()) {
                Yii::log("[INFO] [" . __CLASS__ . ".php] " . __FUNCTION__ . ": User is authenticated!", 'info');
                $model = new ResetPasswordForm();
                // Find user id associated with selector part in URL
                $selectorFromURL = substr($token, 0, 20);
                $resetPasswordRequest = ResetPasswordRequest::findResetPasswordRequestBySelector($selectorFromURL);
                $model->user_id = $resetPasswordRequest->gigadb_user_id;
                // Update password with user's submitted change password form
                if (isset($_POST['ResetPasswordForm'])) {
                    $model->attributes=$_POST['ResetPasswordForm'];
                    if($model->validate() && $model->changePass()) {
                        // Delete token so it cannot be used again
                        #$resetPasswordRequest->delete();
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
     * Some of the cryptographic strategies were taken from
     * https://paragonie.com/blog/2017/02/split-tokens-token-based-authentication-protocols-without-side-channels
     *
     * @return bool
     * @throws TooManyPasswordRequestsException
     * @throws Exception
     */
    private function generateResetToken($user)
    {
        // Check if user has any valid reset password requests
//        if($this->unexpiredResetPasswordRequestExists($user->id))
//        {
//            throw new Exception("Too many password requests - need to wait till current request expires");
//        }
        
        // Remove all existing password requests belonging to user
//        $this->removeResetPasswordRequests($user->id);

        $verifier = Yii::app()->CryptoService->getRandomAlphaNumStr();
        $signingKey = Yii::app()->params['signing_key'];
        $hashedTokenOfVerifier = Yii::app()->CryptoService->getHashedToken($signingKey, $verifier);

        $resetPasswordRequest = new ResetPasswordRequest;
        $resetPasswordRequest->gigadb_user_id = $user->id;
        $resetPasswordRequest->selector = Yii::app()->CryptoService->getRandomAlphaNumStr();
        $resetPasswordRequest->hashed_token = $hashedTokenOfVerifier;
        $resetPasswordRequest->setVerifier($verifier);

        if($resetPasswordRequest->validate()) {
            if($resetPasswordRequest->save(false)) {
                // Send email containing URL for resetting password to user
                $this->sendPasswordEmail($resetPasswordRequest);
                return true;
            }
        }
        else {
            Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": resetPasswordRequest object not valid", 'info');
            return false;
        }
    }

    /**
     * Checks for any valid or invalid reset password requests belonging to a 
     * user
     * 
     * @param $userId
     * @return bool
     */
    private function resetPasswordRequestExists($userId)
    {
        $resetPasswordRequests = ResetPasswordRequest::model()->findAll(array("condition" => "gigadb_user_id = $userId"));
        if($resetPasswordRequests)
            return true;
        else
            return false;
    }

    /**
     * Checks for any valid reset password requests belonging to a user
     * 
     * @param $userId
     * @return bool|void
     */
    private function unexpiredResetPasswordRequestExists($userId)
    {
        if($this->resetPasswordRequestExists($userId))
        {
            $resetPasswordRequests = ResetPasswordRequest::model()->findAll(array("condition" => "gigadb_user_id = $userId"));
            $resetPasswordRequest = $resetPasswordRequests[0];
            if(!$resetPasswordRequest->isExpired())
            {
                return true;
            }
        }
        else
            return false;
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
        $url = $this->createAbsoluteUrl('resetPasswordRequest/reset');
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
