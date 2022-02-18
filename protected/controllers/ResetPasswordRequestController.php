<?php

/**
 * Provides reset password functionality for users
 */
class ResetPasswordRequestController extends Controller
{
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('forgot'),
                'users' => array('?'),  // Can be executed by anonymous users
            ),
            array('deny',
                'actions' => array('reset'),
                'users' => array('?'),  // Cannot be executed by anonymous users
            ),
        );
    }
    
    /**
     * Displays request password page
     */
    public function actionForgot()
    {
        $this->layout = "new_main";
        $resetPasswordRequestForm = new ResetPasswordRequestForm;
        if (isset($_POST['LostUserPassword'])) {
            $resetPasswordRequestForm->email = $_POST['LostUserPassword']['email'];
            if ($resetPasswordRequestForm->validate()) {
                $user = User::model()->findByAttributes(array('email' => $resetPasswordRequestForm->email));
                if ($user !== null) {
                    Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": Found user account for ".$resetPasswordRequestForm->email, 'info');
                    $this->generateResetToken($user);
                }
                else {
                    Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": User account not found for ".$user, 'info');
                }
            }
            $this->redirect(array('user/resetThanks'));
        }
        $this->render('reset');
    }
    
    /**
     * Validates token for user to access password reset page
     * 
     * Token is validated with a database lookup of selector, and
     * re-calculating hash of verifier in URL and compare with
     * hash in database
     * 
     * Looks for /resetpasswordrequest/reset?token={token}
     */
    public function actionReset() 
    {
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": In ResetPasswordRequestController::actionReset()", 'info');

        $signingKey = Yii::app()->params['signing_key'];
        $this->layout = "new_main";
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            $userIdentity = new PasswordResetTokenUserIdentity($token);

            # Complete authentication of user wanting to reset password
            if($userIdentity->authenticate()){
                Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": User is authenticated!", 'info');
                $duration= 1800*1*1 ; // 30 mins
                // Yii::app()->user is the current user application component
                // We save PasswordResetTokenUserIdentity into it and then 
                // controllers can use saved user identity to determine
                // authorisation
                Yii::app()->user->login($userIdentity, $duration);
                $this->redirect('/user/changePassword');
            } else {
                // TODO: Delete token and show login page
                Yii::log("FAILED VALIDATION: " . $userIdentity->errorCode , "error");
            }
        }
    }
    
    /**
     * Some of the cryptographic strategies were taken from
     * https://paragonie.com/blog/2017/02/split-tokens-token-based-authentication-protocols-without-side-channels
     *
     * @return bool
     * @throws TooManyPasswordRequestsException
     */
//    public function generateResetToken($user)
//    {
//        // Remove existing password requests by $user
////        $this->resetPasswordCleaner->handleGarbageCollection();
//
//        // No need to implement at this moment
////        if ($availableAt = $this->hasUserHitThrottling($user)) {
////            throw new TooManyPasswordRequestsException($availableAt);
////        }
//
////        $expiresAt = new \DateTimeImmutable(sprintf('+%d seconds', $this->resetRequestLifetime));
//
//        $now = new Datetime();
//        $generatedAt = $now->format(DateTime::ISO8601) ;
//        $expiresAt = $now->modify('+ 1 hour');
//        $expiresAt = $expiresAt->format(DateTime::ISO8601) ;
//
//        $verifier = ResetPasswordHelper::getRandomAlphaNumStr();
//        $selector = ResetPasswordHelper::getRandomAlphaNumStr();
//
//        $resetPasswordRequest = new ResetPasswordRequest;
//        $resetPasswordRequest->requested_at = $generatedAt;
//        $resetPasswordRequest->expires_at = $expiresAt;
//        $resetPasswordRequest->selector = $selector;
//        $resetPasswordRequest->setVerifier($verifier);
//        $resetPasswordRequest->gigadb_user_id = $user->id;
//        $signingKey = Yii::app()->params['signing_key'];
//        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": verifier ".$verifier, 'info');
//        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": selector ".$selector, 'info');
//        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": signing_key ".$signingKey, 'info');
//        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": user_id ".$user->id, 'info');
//        $hashedTokenOfVerifier = ResetPasswordHelper::getHashedToken($signingKey, $verifier);
//        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": out ".$hashedTokenOfVerifier, 'info');
//        $resetPasswordRequest->hashed_token = $hashedTokenOfVerifier;
//        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": hashed_token ".$resetPasswordRequest->hashed_token, 'info');
//        
//        if($resetPasswordRequest->validate()) {
//            if($resetPasswordRequest->save(false)) {
//                // Send email containing URL for resetting password to user
//                $this->sendPasswordEmail($resetPasswordRequest);
//                return true;
//            }
//        }
//        else {
//            Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": resetPasswordRequest object not valid", 'info');
//            return false;
//        }
//    }

    /**
     * Sends an email to a user who has filled in the reset password form page
     * at /user/reset/username//style/float%3Aright. The email contains a link
     * to the page that allows the user to reset their password.
     * Used by actionReset() function.
     *
     * @param $user
     */
//    private function sendPasswordEmail($resetPasswordRequest) 
//    {
//        // Get public token consisting of selector and verifier
//        Yii::log("User id: " . $resetPasswordRequest->gigadb_user_id, "info");
//        Yii::log("Verifier: " . $resetPasswordRequest->getVerifier(), "info");
//        Yii::log("Public token: " . $resetPasswordRequest->getToken(), "info");
//        // Create URL for user to reset password /resetpasswordrequest/reset?token={token}
//        $url = $this->createAbsoluteUrl('resetpasswordrequest/reset');
//        $url = $url."?token=".$resetPasswordRequest->getToken();
//        Yii::log("URL for email: " . $url, "info");
//        
//        $recipient = $user->email;
//        $subject = Yii::app()->params['email_prefix'] . "Password reset";
//        $password_unhashed = $user->passwordUnHashed;
//        $url = $this->createAbsoluteUrl('site/login');
//        $url= $url."?username=".$user->email."&password=".$password_unhashed."&redirect=yes";
//        $body = $this->renderPartial('emailReset',array('url'=>$url,'password_unhashed'=>$password_unhashed,'user'=>$user->id),true);
//        try {
//            Yii::app()->mailService->sendHTMLEmail(Yii::app()->params['adminEmail'], $recipient, $subject, $body);
//        } catch (Swift_TransportException $ste) {
//            Yii::log("Problem sending password reset email to user - " . $ste->getMessage(), "error");
//        }
//    }
}

