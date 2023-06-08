<?php

class SampleTest extends CDbTestCase
{
    protected $fixtures = array(
        'samples' => 'Sample',
        'attributes' => 'Attribute',
        'sample_attribute' => 'SampleAttribute',
    );


    public function testItShouldReturnSampleAttributeArrayMap()
    {
        $system_under_test = $this->samples(0);
        $result = $system_under_test->getSampleAttributeArrayMap();
        $this->assertArrayHasKey("keyword", $result[0]);
        $this->assertArrayHasKey("number of lines", $result[1]);
        $this->assertEquals("some value", $result[0]["keyword"]);
        $this->assertEquals(155, $result[1]["number of lines"]);
    }
}
