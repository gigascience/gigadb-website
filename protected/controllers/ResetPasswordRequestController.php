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
class ResetPasswordRequestController
{
    public function actionReset()
    {
        $this->layout='new_main';

        if (isset($_POST['LostUserPassword'])) {
            $email = $_POST['LostUserPassword']['email'];
            $user = User::model()->findByAttributes(array('email' => $email));
            if ($user !== null) {
                Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": Found user account for ".$email, 'info');
                $this->generateResetToken();
                
//                $user->password = $user->generatePassword(8);
//                $user->encryptPassword();
//                if ($user->save(false)) {
//                    Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": New temporary password saved for ".$email, 'info');
////                    $this->sendPasswordEmail($user);
//                }
//                else {
//                    Yii::log("[ERROR] [".__CLASS__.".php] ".__FUNCTION__.": Could not save new user password for ".$email, 'error');
//                }
            }
            else {
                Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": User account not found for ".$email, 'info');
            }
            $this->redirect(array('user/resetThanks'));
        }
        $this->render('reset');
    }
    
    /**
     * Some of the cryptographic strategies were taken from
     * https://paragonie.com/blog/2017/02/split-tokens-token-based-authentication-protocols-without-side-channels
     *
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

        $generatedAt = date("Y-m-d H:i:s");
        $expiresAt = date($generatedAt, strtotime("+1 hour"));
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": User account not found for ".$email, 'info');
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": User account not found for ".$email, 'info');

        $verifier = $this->getRandomAlphaNumStr();
        $selector = $this->getRandomAlphaNumStr();
        $encodedData = json_encode([$verifier, $user->id, $expiresAt]);
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": User account not found for ".$email, 'info');
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": User account not found for ".$email, 'info');
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": User account not found for ".$email, 'info');

//        $passwordResetRequest = $this->repository->createResetPasswordRequest(
//            $user,
//            $expiresAt,
//            $selector,
//            $tokenComponents->getHashedToken()
//        );
//
//        $this->repository->persistResetPasswordRequest($passwordResetRequest);
//
//        // final "public" token is the selector + non-hashed verifier token
//        return new ResetPasswordToken(
//            $tokenComponents->getPublicToken(),
//            $expiresAt,
//            $generatedAt
//        );
    }

    /**
     * Original credit to Laravel's Str::random() method.
     *
     * String length is 20 characters
     */
    public function getRandomAlphaNumStr()
    {
        $string = '';
        while (($len = \strlen($string)) < 20) {
            /** @var int<1, max> $size */
            $size = 20 - $len;
            $bytes = random_bytes($size);
            $string .= substr(
                str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        return $string;
    }
}

