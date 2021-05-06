<?php
/**
 * Test for Business object that interacts with the FileFormat ActiveRecord model.
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FileFormatDAOTest extends CDbTestCase
{

	protected $fixtures=array(
        'file_format'=>'FileFormat',
    );
	/**
	 * function to export the list of file types as JSON
	 * @return string a JSON string representing the list of file types
	 */
	public function testToJSON()
	{
		$systemUnderTest = new FileFormatDAO();
		$fileformats = $systemUnderTest->toJSON();
		$this->assertNotNull($fileformats);
		$this->assertEquals(5, count(json_decode($fileformats,true)));
		$this->assertEquals(1,json_decode($fileformats,true)["TEXT"]);
		$this->assertEquals(2,json_decode($fileformats,true)["GFF"]);

	}
}


?>