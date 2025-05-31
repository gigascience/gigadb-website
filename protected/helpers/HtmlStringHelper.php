<?php
/**
 * Helper class for processing and formatting of strings containing html
 */
class HtmlStringHelper
{
    /**
     * @param string $text The input text that may contain URLs
     * @return string The text with URLs converted to clickable links
     */
    public static function autoLinkUrls($text)
    {
        $purifier = new CHtmlPurifier();
        $sanitizedText = $purifier->purify($text);

        $unwrappedUrlRe = '/(?<!href=["\'])\\bhttps?:\/\/[\w\-\.]+(\.[a-z]{2,})(:\d+)?(\/[\w\-\.~:\/?#\[\]@!$&\'\(\)*+,;=%]*)?/i';

        return preg_replace_callback($unwrappedUrlRe, function($matches) {
            $url = $matches[0];

            return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '</a>';
        }, $sanitizedText);
    }
}
