<?php

class CurationLogTest extends CDbTestCase
{
    public function testNoLastModifiedDate()
    {
        $expectation = null;
        $this->assertEquals($expectation, CurationLog::createCurationLogEntry(8)->last_modified_date, "Last modified date created");
    }
}