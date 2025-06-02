<?php

/**
 * Unit tests for HtmlStringHelper
 */

class HtmlStringHelperTest extends CDbTestCase
{
    public function autoLinkUrlsProvider()
    {
        return [
            // [input, expected output]
            [
                'Visit https://google.com to browse internet.',
                'Visit <a href="https://google.com" rel="noopener noreferrer">https://google.com</a> to browse internet.'
            ],
            // omit partial URLs, handle only full URLS starting with http
            [
                'Check out www.github.com for code repositories.',
                'Check out www.github.com for code repositories.'
            ],
            [
                'Check out https://www.github.com for code repositories.',
                'Check out <a href="https://www.github.com" rel="noopener noreferrer">https://www.github.com</a> for code repositories.'
            ],
            [
                'Visit <a href="https://google.com">Google</a> and also check https://github.com',
                'Visit <a href="https://google.com">Google</a> and also check <a href="https://github.com" rel="noopener noreferrer">https://github.com</a>'
            ],
            [
                'Mixed content with <strong>bold</strong> and https://secure.site.com/path?param=value',
                'Mixed content with <strong>bold</strong> and <a href="https://secure.site.com/path?param=value" rel="noopener noreferrer">https://secure.site.com/path?param=value</a>'
            ],
            [
                'URL with fragment: https://docs.example.com/page#section',
                'URL with fragment: <a href="https://docs.example.com/page#section" rel="noopener noreferrer">https://docs.example.com/page#section</a>'
            ],
            [
                'No URLs in this text at all.',
                'No URLs in this text at all.'
            ],
            [
                '<script>alert("xss")</script> and https://safe.example.com',
                // The script tag should be removed by purifier, so only the link remains
                ' and <a href="https://safe.example.com" rel="noopener noreferrer">https://safe.example.com</a>'
            ],
            [
                'Visit https://gigadb.org to browse the site, and check the dataset https://gigadb.org/dataset/100907',
                'Visit <a href="https://gigadb.org">https://gigadb.org</a> to browse the site, and check the dataset <a href="https://gigadb.org/dataset/100907">https://gigadb.org/dataset/100907</a>'
            ],
            [
                'trailing punctuation (e.g. https://foo.com.).',
                'trailing punctuation (e.g. <a href="https://foo.com" rel="noopener noreferrer">https://foo.com</a>.).'
            ]
        ];
    }

    /**
     * @dataProvider autoLinkUrlsProvider
     */
    public function testAutoLinkUrls($input, $expected)
    {
        $output = HtmlStringHelper::autoLinkUrls($input);
        $this->assertEquals($expected, $output);
    }
}

