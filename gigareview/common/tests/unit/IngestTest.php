<?php
namespace common\tests;

use common\models\Ingest;

class IngestTest extends \Codeception\Test\Unit
{
    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testTimestampBehaviourCreation()
    {
        $ingest = new Ingest();
        $this->assertNotNull($ingest);
        $this->assertNull($ingest->created_at);
        $this->assertNull($ingest->updated_at);
        $ingest->save();
        $this->assertNotNull($ingest->created_at);
        $ingest->file_name = "dummy.csv";
        sleep(1);
        $ingest->save();
        $this->assertGreaterThan($ingest->created_at, $ingest->updated_at);
    }

    public function testCreateIngestInstance()
    {
        $reportFileName = "Report-GIGA-em-manuscripts-latest-214-20220607004243.csv";
        $scope = "manuscripts";
        $reportType = 1;

        $ingest = Ingest::createIngestInstance($reportFileName, $scope);
        $this->assertNotNull($ingest);
        $this->assertTrue(is_a($ingest, Ingest::class));
        $this->assertEquals($reportFileName, $ingest->file_name);
        $this->assertEquals($reportType, $ingest->report_type);
        $this->assertTrue($ingest->isNewRecord);
    }

    public function testLogStatusAfterSave()
    {
        $reportFileName = "Report-GIGA-em-manuscripts-latest-214-20220607004243.csv";
        $scope = "manuscripts";
        $reportType = 1;
        $fetchStatus = 3;
        $remoteFileStatus = 1;
        $parseStatus = 1;
        $storeStatus = 1;

        $updateStatusAfterSave = Ingest::logStatusAfterSave($reportFileName, $scope);
        $this->assertTrue($updateStatusAfterSave);

        $ingest = Ingest::findOne(['file_name'=>$reportFileName, 'report_type'=>$reportType, 'fetch_status'=>$fetchStatus, 'parse_status'=>$parseStatus, 'remote_file_status'=>$remoteFileStatus, 'store_status'=>$storeStatus]);
        $this->assertNotNull($ingest);
        $this->assertInstanceOf('common\models\Ingest', $ingest);
        $this->assertEquals($reportFileName, $ingest->file_name);
        $this->assertEquals($reportType, $ingest->report_type);
        $this->assertEquals($fetchStatus, $ingest->fetch_status);
        $this->assertEquals($remoteFileStatus, $ingest->remote_file_status);
        $this->assertEquals($parseStatus, $ingest->parse_status);
        $this->assertEquals($storeStatus, $ingest->store_status);
    }

    public function testLogStatusFailSave()
    {
        $reportFileName = "Report-GIGA-em-manuscripts-latest-214-20220607004243.csv";
        $scope = "manuscripts";
        $reportType = 1;
        $fetchStatus = 3;
        $remoteFileStatus = 1;
        $parseStatus = 1;
        $storeStatus = 0;

        $updateStatusFailSave = Ingest::logStatusFailSave($reportFileName, $scope);
        $this->assertTrue($updateStatusFailSave);

        $ingest = Ingest::findOne(['file_name'=>$reportFileName, 'report_type'=>$reportType, 'fetch_status'=>$fetchStatus, 'parse_status'=>$parseStatus, 'remote_file_status'=>$remoteFileStatus, 'store_status'=>$storeStatus]);
        $this->assertNotNull($ingest);
        $this->assertInstanceOf('common\models\Ingest', $ingest);
        $this->assertEquals($reportFileName, $ingest->file_name);
        $this->assertEquals($reportType, $ingest->report_type);
        $this->assertEquals($fetchStatus, $ingest->fetch_status);
        $this->assertEquals($remoteFileStatus, $ingest->remote_file_status);
        $this->assertEquals($parseStatus, $ingest->parse_status);
        $this->assertEquals($storeStatus, $ingest->store_status);
    }

    public function testLogNoResultsReportStatus()
    {
        $noResultsReportFileName = "Report-GIGA-em-manuscripts-latest-214-20220611007777.csv";
        $scope = "manuscripts";
        $reportType = 1;
        $fetchStatus = 3;
        $remoteFileStatus = 0;
        $parseStatus = 0;
        $storeStatus = 0;

        $updateNoResultsStatus = Ingest::logNoResultsReportStatus($noResultsReportFileName, $scope);
        $this->assertTrue($updateNoResultsStatus);

        $ingest = Ingest::findOne(['file_name'=>$noResultsReportFileName, 'report_type'=>$reportType, 'fetch_status'=>$fetchStatus, 'parse_status'=>$parseStatus, 'remote_file_status'=>$remoteFileStatus, 'store_status'=>$storeStatus]);
        $this->assertNotNull($ingest);
        $this->assertInstanceOf('common\models\Ingest', $ingest);
        $this->assertEquals($noResultsReportFileName, $ingest->file_name);
        $this->assertEquals($reportType, $ingest->report_type);
        $this->assertEquals($fetchStatus, $ingest->fetch_status);
        $this->assertEquals($remoteFileStatus, $ingest->remote_file_status);
        $this->assertEquals($parseStatus, $ingest->parse_status);
        $this->assertEquals($storeStatus, $ingest->store_status);
    }
}