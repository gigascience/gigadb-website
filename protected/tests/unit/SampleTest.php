<?php


class SampleTest extends CDbTestCase
{
    protected $fixtures=array(
        'species'=>'Species',
        'samples'=>'Sample',
    );

    function testLoadByData() {
        $data = array(
            'species_name' => 'Adelie penguin',
            'sample_id' => 'Sample Id',
        );

        $sample = new Sample();
        $sample->loadByData($data);

        $this->assertEquals(1, $sample->species_id);
        $this->assertEquals('Sample Id', $sample->name);
    }

    function testValidate() {
        $data = array(
            'species_name' => 'Adelie penguin',
            'sample_id' => 'Sample Id',
        );

        $sample = new Sample();
        $sample->loadByData($data);

        $this->assertTrue($sample->validate());
    }
}
