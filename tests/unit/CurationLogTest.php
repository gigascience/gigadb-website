<?php

/**
 * Unit tests for DatasetUpload
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CurationLogTest extends CTestCase
{
    public function testMakeNewInstance()
    {
        $datasetId = 8;
        $user = User::model()-> find('id=:id', array(':id' => $datasetId));
        $creator = $user->first_name . " " . $user->last_name;
        $curationLog = CurationLog::makeNewInstanceForDatasetBy($datasetId, $creator);
        $this->assertNotNull($curationLog);
        $this->assertTrue(is_a($curationLog, CurationLog::class));
        $this->assertEquals($datasetId, $curationLog->dataset_id);
        $this->assertEquals($creator, $curationLog->created_by);
        $this->assertTrue($curationLog->isNewRecord);
    }
}
