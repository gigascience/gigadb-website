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

		$expectation = "1.32GB";
		$this->assertEquals( $expectation, $system_under_test->getSizeWithFormat() );

	}

	public function sizeFormatsProvider() {
		return [
			["kB",3, "1322123.045kB"],
			["MB",2, "1322.12MB"],
			["GB",2, "1.32GB"],
			["TB",5, "0.00132TB"],
			["B",2, "1322123045B"],
			["kB",9, "1322123.045000000kB"],
			["MB",9, "1322.123045000MB"],
			["GB",9, "1.322123045GB"],
			["TB",9, "0.001322123TB"],
			["B",9, "1322123045B"],
			[null,null, "1.32GB"],
			["kB",null, "1322123.04kB"],
			["MB",null, "1322.12MB"],
			["GB",null, "1.32GB"],
			["TB",null, "0.00TB"],
			["B",null, "1322123045B"],
			[null,9, "1.322123045GB"],
		];
	}

}

?>