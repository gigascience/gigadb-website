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
            'sample_description' => 'Sample Description',
        );

        $sample = new Sample();
        $sample->loadByData($data);

        $this->assertEquals(1, $sample->species_id);
        $this->assertEquals('Sample Id', $sample->name);
        $this->assertEquals('Sample Description', $sample->description);
    }

    function testValidate() {
        $data = array(
            'species_name' => 'Adelie penguin',
            'sample_id' => 'Sample Id',
            'sample_description' => 'Sample Description',
        );

        $sample = new Sample();
        $sample->loadByData($data);

        $this->assertTrue($sample->validate());
    }
}
