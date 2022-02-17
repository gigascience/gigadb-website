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