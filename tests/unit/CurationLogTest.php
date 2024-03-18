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
        # assume logged in as admin
        $userId = 400;
        $user = User::model()-> find('id=:id', array(':id' => $userId));
        $curationLog = CurationLog::makeNewInstanceForDatasetBy($userId, $user->getFullName());
        $this->assertNotNull($curationLog);
        $this->assertTrue(is_a($curationLog, CurationLog::class));
        $this->assertEquals($userId, $curationLog->dataset_id);
        $this->assertEquals("Joe Bloggs", $curationLog->created_by);
        $this->assertTrue($curationLog->isNewRecord);
    }
}
