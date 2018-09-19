<?php


class FileTest extends CDbTestCase
{

	protected $fixtures=array(
        'files'=>'File',
    );

	/**
     * @dataProvider sizeFormatsProvider
     */
	public function testItShouldReturnSizeWithFormat($unit, $precision, $expectation) {
		$system_under_test = $this->files(0);

		$this->assertEquals( $expectation, $system_under_test->getSizeWithFormat($unit,$precision) );

	}

	public function testItShouldReturnSizeWithFormatAndNoArguments() {
		$system_under_test = $this->files(0);

		$expectation = "1.32 GB";
		$this->assertEquals( $expectation, $system_under_test->getSizeWithFormat() );

	}

	public function sizeFormatsProvider() {
		return [
			["kB",3, "1322123.045 kB"],
			["MB",2, "1322.12 MB"],
			["GB",2, "1.32 GB"],
			["TB",5, "0.00132 TB"],
			["B",2, "1322123045 B"],
			["kB",9, "1322123.045000000 kB"],
			["MB",9, "1322.123045000 MB"],
			["GB",9, "1.322123045 GB"],
			["TB",9, "0.001322123 TB"],
			["B",9, "1322123045 B"],
			[null,null, "1.32 GB"],
			["kB",null, "1322123.04 kB"],
			["MB",null, "1322.12 MB"],
			["GB",null, "1.32 GB"],
			["TB",null, "0.00 TB"],
			["B",null, "1322123045 B"],
			[null,9, "1.322123045 GB"],
		];
	}

}

?>