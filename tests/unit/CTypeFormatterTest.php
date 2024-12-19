<?php

declare(strict_types=1);

class CTypeFormatterTest extends \Codeception\Test\Unit
{
    private LogCurationFormatter $formatter;
    private string $xmlWithPreTag;

    protected function setUp(): void
    {
        $this->formatter = new LogCurationFormatter();
        $myXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<test>
    <subtest>
        <title>test</title>
        <author>test</author>
        <year>2025</year>
    </subtest>
    <subtest>
        <title>test1</title>
        <author>test1</author>
        <year>2024</year>
    </subtest>
</test>
XML;
        $myShortXml = "&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;\n&lt;test&gt;\n    &lt;subtest&gt;\n...&lt;/resource&gt;";
        $this->xmlWithPreTag = "<span class=\"js-short-1\"><pre>$myShortXml</pre></span><span class=\"js-long-1\" style=\"display: none;\">$myXml</span>";
    }

    public function testItReturnsRightType()
    {
        $formatter = new CTypeFormatter();

        $result = $formatter->format($this->xmlWithPreTag, 'text');
        $this->assertEquals($this->xmlWithPreTag, $result);

        $result = $formatter->format('<p>test</p>', 'text');
        $this->assertEquals('&lt;p&gt;test&lt;/p&gt;', $result);
    }
}
