<?php
/**
 * Class ResetPasswordHelper
 * 
 * Helper functions for verifying token from URL, creating tokens and random
 * strings
 */
class ResetPasswordHelper
{
    /**
     * Verify a password reset token
     *
     * @param string $token The token to verify which consists of selector and verifier
     * @return bool True if the re-calculated hash of the verifier matches the hash from the database.
     * @see https://paragonie.com/book/pecl-libsodium/read/07-password-hashing.md
     * 
     * @param $token
     * @return bool
     */
    public static function verifyToken($token)
    {
        $signingKey = Yii::app()->params['signing_key'];
        
        $selectorFromURL = substr($token, 0, 20);
        $resetPasswordRequest = ResetPasswordRequest::findResetPasswordRequestBySelector($selectorFromURL);
        if($resetPasswordRequest == null)
        {
            Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__." No password request record found using URL token", 'info');
            return false;
        }
        $selectorFromDatabase = $resetPasswordRequest->selector;

        // Re-calculate hash from verifier obtained from URL
        $verifierFromUrl = substr($token, 20, 20);
        $hashTokenFromUrlVerifier = ResetPasswordHelper::getHashedToken($signingKey, $verifierFromUrl);
        $hashTokenFromDatabase = $resetPasswordRequest->hashed_token;
        // Perform verification by comparing selectors and hashes
        if($selectorFromDatabase == $selectorFromURL && $hashTokenFromUrlVerifier == $hashTokenFromDatabase) {
            return true;
        }
        else {
            // Delete token and show login page
            Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__." Password reset token failed validation", 'info');
            return false;
        }
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
     * Original credit to Laravel's Str::random() method.
     *
     * String length is 20 characters
     */
    public static function getRandomAlphaNumStr()
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