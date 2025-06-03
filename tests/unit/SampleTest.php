<?php

declare(strict_types=1);

require_once __DIR__ . '/LoadingFixtureTrait.php';

use Codeception\Test\Unit;

class SampleTest extends Unit
{
    use LoadingFixtureTrait;

    public function _before()
    {
        $db = $this->getModule('Db')->_getDbh();
        $db->exec('TRUNCATE TABLE sample CASCADE');
        $db->exec('TRUNCATE TABLE attribute CASCADE');
        $db->exec('TRUNCATE TABLE sample_attribute CASCADE');

        $this->loadFixture('sample', \Sample::class);
        $this->loadFixture('attribute', \Attributes::class);
        $this->loadFixture('sample_attribute', \SampleAttribute::class);
    }

    public function testItShouldReturnSampleAttributeArrayMap()
    {
        $system_under_test = Sample::model()->findByPk(1);
        $result = $system_under_test->getSampleAttributeArrayMap();
        $this->assertArrayHasKey("keyword", $result[0]);
        $this->assertArrayHasKey("number of lines", $result[1]);
        $this->assertEquals("some value", $result[0]["keyword"]);
        $this->assertEquals(155, $result[1]["number of lines"]);
    }
}
