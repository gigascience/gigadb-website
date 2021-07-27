<?php

class CurationLogTest extends CDbTestCase
{
    public function testMakeNewInstance()
    {
        $datasetId = 1;
        $creator = "System";
        $curationLog = CurationLog::makeNewInstanceForDatasetBy($datasetId, $creator);
        $this->assertNotNull($curationLog);
        $this->assertTrue(is_a($curationLog, CurationLog::class));
        $this->assertEquals($datasetId, $curationLog->dataset_id);
        $this->assertEquals($creator, $curationLog->created_by);
        $this->assertTrue($curationLog->isNewRecord);
    }
}