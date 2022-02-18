<?php
/**
 * Class to handle the identity of user and how they authenticate
 *
 * @see https://www.yiiframework.com/doc/guide/1.1/en/topics.auth
 */
class PasswordResetTokenUserIdentity extends UserIdentity {

    public $_id;
    public $type;
    const ERROR_SELECTOR_NOT_ASSOCIATED_WITH_A_USER = 4;
    const ERROR_RECALCULATED_HASH_OF_VERIFIER_DOES_NOT_MATCH_HASH_IN_DATABASE = 5;

    /**
     * @var string Token from URL sent by email
     */
    public $urlToken;

    /**
     * Constructor is needed as we don't need ($username, $password), because
     * Oauth is the authentication instead we need to feed in the provider and
     * uid returned form Oauth process
     */
    public function __construct($urlToken)
    {
        $this->urlToken = $urlToken;
        $this->type = "passwordResetUser";
    }

    /**
     * Provides the authentication process for a user with a password reset
     * token that consists of two parts: a selector and verifier.
     *
     * @return boolean whether authentication succeeds. True if successful, False otherwise
     */
    public function authenticate() 
    {
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": In PasswordResetTokenUserIdentity::authenticate()", 'info');

        // Find user associated with selector part in URL
        $selectorFromURL = substr($this->urlToken, 0, 20);
        $resetPasswordRequest = ResetPasswordRequest::findResetPasswordRequestBySelector($selectorFromURL);
        $user = User::model()->findByAttributes(array('id' => $resetPasswordRequest->gigadb_user_id));

        if ($user === null)  // User not found
        {
            $this->errorCode = self::ERROR_SELECTOR_NOT_ASSOCIATED_WITH_A_USER;
        } 
        else  // User found
        {
            // Re-calculate hash from verifier and check if it matches with
            // hash stored in reset_password_request database table
            $signingKey = Yii::app()->params['signing_key'];
            $verifierFromURL = substr($this->urlToken, 20, 20);
            $hashedTokenFromURLVerifier = ResetPasswordHelper::getHashedToken($signingKey, $verifierFromURL);
            if($hashedTokenFromURLVerifier == $resetPasswordRequest->hashed_token)
            {
                $this->_id = $user->id;
                $this->setState('userType', $this->type);
                $this->errorCode = self::ERROR_NONE;
            }
            else
            {
                $this->errorCode = self::ERROR_RECALCULATED_HASH_OF_VERIFIER_DOES_NOT_MATCH_HASH_IN_DATABASE;
            }
        }
        return !$this->errorCode;
    }

    public function getId()
    {
        return $this->_id;
    }
}

?>