<?php

/**
 * Class CompatibilityHelper
 *
 * Polyfill PHP 8 functions. We can get rid of this once we use PHP 8 for the main app
 */
class CompatibilityHelper
{

    /**
     *
     * @see https://www.php.net/manual/en/function.str-contains
     * @param ?string $haystack
     * @param ?string $needle
     * @return bool
     */
    public static function str_contains(?string $haystack, ?string $needle): bool {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }

    /**
     *
     * @see https://www.php.net/manual/en/function.str-starts-with
     * @param ?string $haystack
     * @param ?string $needle
     * @return bool
     */
    public static function str_starts_with ( ?string $haystack, ?string $needle ): bool {
        return strpos( $haystack , $needle ) === 0;
    }

}