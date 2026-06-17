<?php

declare(strict_types=1);

require_once __DIR__ . '/LoadingFixtureTrait.php';

use Codeception\Test\Unit;
/**
 * Test for Business object that interacts with the FileType ActiveRecord model.
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FileTypeDAOTest extends Unit
{
    use LoadingFixtureTrait;

    public function _before()
    {
        $db = $this->getModule('Db')->_getDbh();
        $db->exec('TRUNCATE TABLE file_type CASCADE');

        $this->loadFixture('file_type', \FileType::class);
    }

    /**
     * function to export the list of file types as JSON
     * @return string a JSON string representing the list of file types
     */
    public function testToJSON()
    {
        $systemUnderTest = new FileTypeDAO();
        $filetypes = $systemUnderTest->toJSON();
        $this->assertNotNull($filetypes);
        $this->assertEquals(3, count(json_decode($filetypes, true)));
        $this->assertEquals(1, json_decode($filetypes, true)["Text"]);
        $this->assertEquals(2, json_decode($filetypes, true)["Sequence assembly"]);
        $this->assertEquals(3, json_decode($filetypes, true)["Annotation"]);
    }
}
