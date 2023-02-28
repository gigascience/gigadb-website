<?php

/**
 * Class to handle the identity of user and how they authenticate
 *
 * @see https://www.yiiframework.com/doc/guide/1.1/en/topics.auth
 */
class PasswordResetTokenUserIdentity extends UserIdentity
{
    public $_id;
    private $random_string_length;
    const ERROR_SELECTOR_NOT_ASSOCIATED_WITH_A_USER = 4;
    const ERROR_RECALCULATED_HASH_OF_VERIFIER_DOES_NOT_MATCH_HASH_IN_DATABASE = 5;
    const ERROR_TOKEN_HAS_EXPIRED = 6;

    /**
     * @var string Token from URL sent by email
     */
    public $urlToken;

    /**
     * Constructor
     */
    public function __construct($urlToken)
    {
        $this->urlToken = $urlToken;
        $this->random_string_length = CryptoService::RANDOM_STRING_LENGTH;
    }

    /**
     * Provides the authentication process for an anonymous user with a password
     * reset token that consists of two parts: a selector and verifier.
     *
     * @return boolean whether authentication succeeds. True if successful, False otherwise
     */
    public function authenticate()
    {
        Yii::log("[INFO] [" . __CLASS__ . ".php] " . __FUNCTION__ . ": In PasswordResetTokenUserIdentity::authenticate()", 'info');

        // Find user associated with selector part in URL
        $selectorFromURL = substr($this->urlToken, 0, $this->random_string_length);
        $resetPasswordRequest = ResetPasswordRequest::model()->findByAttributes(array('selector' => $selectorFromURL));
        $user = User::model()->findByAttributes(array('id' => $resetPasswordRequest->gigadb_user_id));

        if ($user === null) {  // User not found
            $this->errorCode = self::ERROR_SELECTOR_NOT_ASSOCIATED_WITH_A_USER;
        } else // User found
        {
            // Check re-calculated hash from verifier matches hash in reset_password_request database table
            $signingKey = Yii::app()->params['signing_key'];
            $verifierFromURL = substr($this->urlToken, $this->random_string_length, $this->random_string_length);
            $hashedTokenFromURLVerifier = Yii::app()->cryptoService->getHashedToken($signingKey, $verifierFromURL);
            if ($hashedTokenFromURLVerifier === $resetPasswordRequest->hashed_token) {
                // Check if token has expired
                if ($resetPasswordRequest->isExpired()) {
                    Yii::log("[INFO] [" . __CLASS__ . ".php] " . __FUNCTION__ . ": Token has expired: ", 'info');
                    $this->errorCode = self::ERROR_TOKEN_HAS_EXPIRED;
                } else {
                    $this->_id = $user->id;
                    $this->errorCode = self::ERROR_NONE;
                }
            } else {
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
