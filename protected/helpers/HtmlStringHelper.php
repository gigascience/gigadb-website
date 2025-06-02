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
            $urlParts = parse_url($url);
            $urlDomain = isset($urlParts['host']) ? strtolower($urlParts['host']) : '';
            $urlDomain = preg_replace('/^www\./', '', $urlDomain);
            $isExternal = ($urlDomain !== 'gigadb.org');
            $attributes = '';

            if ($isExternal) {
                $attributes = ' rel="noopener noreferrer"';
            }

            return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"' . $attributes . '>' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '</a>';
        }, $sanitizedText);
    }
}
