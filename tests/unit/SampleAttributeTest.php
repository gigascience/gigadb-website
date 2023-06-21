<?php

/**
 * Class SampleAttribute
 *
 * generated with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept generate:test unit SampleAttribute
 *
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run unit SampleAttributeTest
 */
class SampleAttributeTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testAttributeIdSampleIdExist()
    {
        $sampleAttribute = new SampleAttribute();
        $sampleAttribute->attribute_id = 200; // An attribute_id that exists in the Attribute table
        $sampleAttribute->sample_id = 151; // A sample_id that exists in the Sample table
        $this->assertTrue($sampleAttribute->validate());
        $this->assertArrayNotHasKey('attribute_id', $sampleAttribute->getErrors());
        $this->assertArrayNotHasKey('sample_id', $sampleAttribute->getErrors());

    }

    public function testAttributeIdNotExists()
    {
        $sampleAttribute = new SampleAttribute();
        $sampleAttribute->attribute_id = 9999; // An attribute_id that doesn't exist in the Attribute table
        $sampleAttribute->sample_id = 151;
        $this->assertFalse($sampleAttribute->validate());
        $this->assertArrayHasKey('attribute_id', $sampleAttribute->getErrors());
    }

    public function testSampleIdNotExist()
    {
        $sampleAttribute = new SampleAttribute();
        $sampleAttribute->attribute_id = 200;
        $sampleAttribute->sample_id = 9999; // A sample_id that exists in the Sample table
        $this->assertFalse($sampleAttribute->validate());
        $this->assertArrayHasKey('sample_id', $sampleAttribute->getErrors());
    }

    public function testAttributeIdSampleIdNotExist()
    {
        $sampleAttribute = new SampleAttribute();
        $sampleAttribute->attribute_id = 9999;
        $sampleAttribute->sample_id = 9999;
        $this->assertFalse($sampleAttribute->validate());
        $this->assertArrayHasKey('attribute_id', $sampleAttribute->getErrors());
        $this->assertArrayHasKey('sample_id', $sampleAttribute->getErrors());
    }
}