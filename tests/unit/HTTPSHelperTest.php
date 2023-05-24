<?php

namespace GigaDB\Tests\UnitTests;

/**
 * Unit tests for PasswordHelper class
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 *
 */
class HTTPSHelperTest extends \CTestCase
{
    /**
     * test conversion from http to https
     *
     * @dataProvider exampleUrls
     */
    public function testHttpsize($raw_url, $expected)
    {
        $this->assertEquals($expected, \HTTPSHelper::httpsize($raw_url));
    }

    public function exampleUrls()
    {
        return [
            "valid" => ["http://dx.doi.org/10.17504/protocols.io.exwbfpe","https://dx.doi.org/10.17504/protocols.io.exwbfpe"],
            "still_valid" => [" http://dx.doi.org/10.17504/protocols.io.exwbfpe","https://dx.doi.org/10.17504/protocols.io.exwbfpe"],
            "blacklisted" => ["http://penguin.genomics.cn/jbrowse/index.html?data=100240&loc=PIN_chr1%3A10455684..94134086&tracks=DNA",false],
        ];
    }
}
