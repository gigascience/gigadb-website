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
        $userId = 344;
        $user = User::model()-> find('id=:id', array(':id' => $userId));
        $creator = $user->getFullName();
        $curationLog = CurationLog::makeNewInstanceForDatasetBy($userId, $creator);
        $this->assertNotNull($curationLog);
        $this->assertTrue(is_a($curationLog, CurationLog::class));
        $this->assertEquals($userId, $curationLog->dataset_id);
        $this->assertEquals($creator, $curationLog->created_by);
        $this->assertTrue($curationLog->isNewRecord);
    }
}
