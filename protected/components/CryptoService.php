<?php

/**
 * Service to provide tokens for password reset functionality
 */
class CryptoService extends yii\base\Component
{
    const RANDOM_STRING_LENGTH = 20;

    /**
     * Initializes application component.
     * This method overrides the parent implementation by setting default cache
     * key prefix.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Returns the hash of a token. Used to generate a hash for the verifier.
     *
     * @param string $signingKey Unique, random, cryptographically secure string
     * @param string $data Token to be hashed
     * @return string
     */
    public static function getHashedToken(string $signingKey, string $data)
    {
        $hash = base64_encode(hash_hmac('sha256', $data, $signingKey, true));
        return $hash;
    }

    /**
     * Uses method in Yii2 to generate random string
     *
     * String length is 20 characters
     */
    public static function getRandomString()
    {
        return Yii::$app->security->generateRandomString(self::RANDOM_STRING_LENGTH);
    }
}
