<?php

class CurationLogTest extends CDbTestCase
{
    public function testCreateLogEntry()
    {
        $datasetId = 8;
        $creator = "System";
        $curationLog = CurationLog::createLogEntry($datasetId, $creator);
        $this->assertNotNull($curationLog);
        $this->assertTrue(is_a($curationLog, CurationLog::class));
        $this->assertEquals($datasetId, $curationLog->dataset_id);
        $this->assertEquals($creator, $curationLog->created_by);
        $this->assertTrue($curationLog->isNewRecord);
    }
}