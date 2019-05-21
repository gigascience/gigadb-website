<?php


class FundingTest extends CDbTestCase
{
    protected $fixtures=array(
        'funders'=>'Funder',
        'fundings'=>'Funding',
    );

    function testLoadByData() {
        $data = array(
            'dataset_id' => 3,
            'funder_id' => 1,
            'program_name' => 'Program Name',
            'grant' => 'Grant',
            'pi_name' => 'PI Name',
        );

        $funding = new Funding();
        $funding->loadByData($data);

        $this->assertEquals(3, $funding->dataset->id);
        $this->assertEquals(1, $funding->funder->id);
        $this->assertEquals('Program Name', $funding->program_name);
        $this->assertEquals('Grant', $funding->grant);
        $this->assertEquals('PI Name', $funding->pi_name);
    }

    function testValidate() {
        $data = array(
            'dataset_id' => 3,
            'funder_id' => 1,
            'program_name' => 'Program Name',
            'grant' => 'Grant',
            'pi_name' => 'PI Name',
        );

        $funding = new Funding();
        $funding->loadByData($data);

        $this->assertTrue($funding->validate());
    }

    function testAsArray() {
        $funding = $this->fundings(0);
        $array = $funding->asArray();

        $this->assertEquals(3, $array['dataset_id']);
        $this->assertEquals(1, $array['funder_id']);
        $this->assertEquals('The Good', $array['funder_name']);
        $this->assertEquals('Program Name1', $array['program_name']);
        $this->assertEquals('Grant1', $array['grant']);
        $this->assertEquals('PI Name1', $array['pi_name']);
    }
}
