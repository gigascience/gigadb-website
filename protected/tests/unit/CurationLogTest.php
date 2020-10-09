<?php

class CurationLogTest extends CDbTestCase
{
    protected $fixtures=array(
        'log'=>'CurationLog',
        'dataset'=>'Dataset',
    );

    public function testCreateLogEntry()
    {
        $this->assertEquals(date("Y-m-d"), $this->log(0)->creation_date, "Different date format with CurationLog");
    }
}