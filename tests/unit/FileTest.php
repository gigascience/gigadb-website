<?php

class FileTest extends CDbTestCase
{
    protected $fixtures = array(
        'files' => 'File',
    );

    /**
     * @dataProvider sizeFormatsProvider
     */
    public function testItShouldReturnSizeWithFormat($unit, $precision, $expectation)
    {
        $system_under_test = $this->files(0);

        $this->assertEquals($expectation, $system_under_test->getSizeWithFormat($unit, $precision));
    }

    public function testItShouldReturnSizeWithFormatAndNoArguments()
    {
        $system_under_test = $this->files(0);

        $expectation = "1.23 GiB";
        $this->assertEquals($expectation, $system_under_test->getSizeWithFormat());
    }

    public function testItShouldReturnZeroByteWhenSizeNegative()
    {
        $system_under_test = $this->files(1);

        $expectation = "-1";
        $this->assertEquals($expectation, $system_under_test->getSizeWithFormat());
    }

    public function sizeFormatsProvider()
    {
        return [
            ["KiB",3, "1291135.786 KiB"],
            ["MiB",2, "1260.87 MiB"],
            ["GiB",2, "1.23 GiB"],
            ["TiB",5, "0.00120 TiB"],
            ["B",2, "1322123045 B"],
            ["KiB",9, "1291135.786132812 KiB"],
            ["MiB",9, "1260.874791145 MiB"],
            ["GiB",9, "1.231323038 GiB"],
            ["TiB",9, "0.001202464 TiB"],
            ["B",9, "1322123045 B"],
            [null,null, "1.23 GiB"],
            ["KiB",null, "1291135.79 KiB"],
            ["MiB",null, "1260.87 MiB"],
            ["GiB",null, "1.23 GiB"],
            ["TiB",null, "0.00 TiB"],
            ["B",null, "1322123045 B"],
            [null,9, "1.231323038 GiB"],
        ];
    }
}
