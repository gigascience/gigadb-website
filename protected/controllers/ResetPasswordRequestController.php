<?php

/**
 * Verify that password supplied by a user attempting a login matches the hashes in the database
 *
 * Until every user has changed their password to use the stronger hashing algorithm,
 * The verification will happen in two stages, corresponding to each algorithm
 * The class's API is modeled after CPasswordHelper
 *
 * @uses sodium_crypto_pwhash_str_verify()
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class ResetPasswordRequestController extends Controller
{
    /**
     * Displays the login page
     */
    public function actionForgot() {

        $this->layout = "new_main";
        $resetPasswordRequestForm = new ResetPasswordRequestForm;
        if (isset($_POST['LostUserPassword'])) {
            $resetPasswordRequestForm->email = $_POST['LostUserPassword']['email'];
            if ($resetPasswordRequestForm->validate()) {
                $user = User::model()->findByAttributes(array('email' => $resetPasswordRequestForm->email));
            }
            if ($user !== null) {
                Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": Found user account for ".$resetPasswordRequestForm->email, 'info');
                if($this->generateResetToken($user)) {
                    Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": Generated token", 'info');
                }
                else {
                    Yii::log("[ERROR] [".__CLASS__.".php] ".__FUNCTION__.": Could not generate token ", 'error');
                }
            }
            else {
                Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": User account not found for ".$user, 'info');
            }
            $this->redirect(array('user/resetThanks'));
        }
        $this->render('reset');
    }
    
    /**
     * Some of the cryptographic strategies were taken from
     * https://paragonie.com/blog/2017/02/split-tokens-token-based-authentication-protocols-without-side-channels
     *
     * @return bool
     * @throws TooManyPasswordRequestsException
     */
    public function generateResetToken($user)
    {
        // Remove existing password requests by $user
//        $this->resetPasswordCleaner->handleGarbageCollection();

        // No need to implement at this moment
//        if ($availableAt = $this->hasUserHitThrottling($user)) {
//            throw new TooManyPasswordRequestsException($availableAt);
//        }

//        $expiresAt = new \DateTimeImmutable(sprintf('+%d seconds', $this->resetRequestLifetime));

        $now = new Datetime();
        $generatedAt = $now->format(DateTime::ISO8601) ;
//        $expiresAt = date($generatedAt, strtotime("+1 hour"));
        $expiresAt = $generatedAt;
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": generatedAt ".$generatedAt, 'info');
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": expiresAt: ".$expiresAt, 'info');

        $verifier = ResetPasswordHelper::getRandomAlphaNumStr();
        $selector = ResetPasswordHelper::getRandomAlphaNumStr();
        $encodedData = json_encode([$verifier, $user->id, $expiresAt]);

        $resetPasswordRequest = new ResetPasswordRequest;
        $resetPasswordRequest->requested_at = $generatedAt;
        $resetPasswordRequest->expires_at = $expiresAt;
        $resetPasswordRequest->selector = $selector;
        $resetPasswordRequest->gigadb_user_id = $user->id;
//        $signingKey = Yii::app()->params['signing_key'];
        $signingKey = "Fear_is_the_mind_killer";
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": verifier ".$verifier, 'info');
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": selector ".$selector, 'info');
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": encodedData ".$encodedData, 'info');
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": signing_key ".$signingKey, 'info');
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": user_id ".$user->id, 'info');
        $out = ResetPasswordHelper::getHashedToken($signingKey, $verifier);
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": out ".$out, 'info');
        $resetPasswordRequest->hashed_token = "foobar";
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": hashed_token ".$resetPasswordRequest->hashed_token, 'info');
        
        if($resetPasswordRequest->validate()) {
            if($resetPasswordRequest->save(false)) {
                // Send email containing URL for resetting password to user
                return true;
            }
        }
        else {
            Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": resetPasswordRequest object not valid", 'info');
            return false;
        }
    }
}

