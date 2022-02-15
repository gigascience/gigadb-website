<?php

class ResetPasswordHelper
{
    /**
     * Returns the hash of a token. Used to generate a hash for the verifier.
     * @param signingKey Unique, random, cryptographically secure string
     * @param data Token to be hashed
     */
    public static function getHashedToken(string $signingKey, string $data)
    {
        $out = base64_encode(hash_hmac('sha256', $data, $signingKey, true));
        Yii::log("[INFO] [".__CLASS__.".php] ".__FUNCTION__.": base64_hash ".$out, 'info');
        return $out;
//        return base64_encode(hash_hmac('sha256', $data, $signingKey, true));
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