<?php 

declare(strict_types=1);

class LogCurationFormatterTest extends \Codeception\Test\Unit
{
    private LogCurationFormatter $formatter;
    private string $myXml;
    private string $xmlWithPreTag;

    protected function setUp(): void
    {
        $this->formatter = new LogCurationFormatter();
        $this->myXml = <<<XML
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
        $this->xmlWithPreTag = "<span class=\"js-short-1\"><pre>$myShortXml</pre></span><span class=\"js-long-1\" style=\"display: none;\">$this->myXml</span>";
    }

    public function testItDisplaysCorrectlyTheXmlAttr()
    {
        $expectedXml = $this->xmlWithPreTag;
        $expectedXml .= "<button type='button' class='js-desc btn btn-subtle' data='1' aria-label='show more' aria-expanded='false' aria-controls='js-long-1'>+</button>";

        $result = $this->formatter->getDisplayXmlAttr(1, $this->myXml);

        $this->assertEquals($expectedXml, $result, 'The date should be formatted correctly');
    }
}
