<?php

/**
 * Class CompatibilityHelper
 *
 * Polyfill PHP 8 functions. We can get rid of this once we use PHP 8
 */
class CompatibilityHelper
{

    /**
     *
     * @see https://www.php.net/manual/en/function.str-contains
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }

}