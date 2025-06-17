<?php

/**
 * Unit tests for DatasetUpload
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CurationLogTest extends CTestCase
{
    public function testBuildFullName()
    {
        $testCases = [
            ['John', 'Doe', 'John Doe'],
            [' John ', ' Doe ', 'John Doe'],
        ];

        foreach ($testCases as [$first, $last, $expected]) {
            $fullName = CurationLog::buildFullName($first, $last);
            $this->assertEquals($expected, $fullName);
        }
    }

    public function testGetCurrentUserFullName()
    {
        $originalUser = Yii::app()->getComponent('user');

        // Mock the WebUser component to simulate a logged-in user
        $mockUser = $this->getMockBuilder(WebUser::class)
            ->setMethods(['getFirstName', 'getLastName'])
            ->disableOriginalConstructor()
            ->getMock();

        Yii::app()->setComponent('user', $mockUser);

        $mockUser->method('getFirstName')->willReturn('John');
        $mockUser->method('getLastName')->willReturn('Doe');


        $fullName = CurationLog::getCurrentUserFullName();
        $this->assertEquals('John Doe', $fullName);

        // Restore the original user component
        Yii::app()->setComponent('user', $originalUser);
    }

    public function testMakeNewInstance()
    {
        $datasetId = 1;
        # assume logged in as admin
        $creator = 'Joe Bloggs';
        $curationLog = CurationLog::makeNewInstanceForDatasetBy($datasetId, $creator);
        $this->assertNotNull($curationLog);
        $this->assertTrue(is_a($curationLog, CurationLog::class));
        $this->assertEquals($datasetId, $curationLog->dataset_id);
        $this->assertEquals($creator, $curationLog->created_by);
        $this->assertTrue($curationLog->isNewRecord);
    }
}
