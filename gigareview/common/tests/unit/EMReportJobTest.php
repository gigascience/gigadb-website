<?php
namespace common\tests;
use common\models\EMReportJob;
use common\models\Manuscript;

class EMReportJobTest extends \Codeception\Test\Unit
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
    public function testCsvCanBeParsedByParseReport()
    {
        $expectCsvReportData = [
            [
                'manuscript_number' => 'GIGA-D-22-00054',
                'article_title' => 'A machine learning framework for discovery and enrichment of metagenomics metadata from open access publications',
                'editorial_status_date' => '6/7/2022',
                'editorial_status' => 'Final Decision Accept'
            ],
            [
                'manuscript_number' => 'GIGA-D-22-00060',
                'article_title' => 'A chromosome-level genome of the booklouse, Liposcelis brunnea provides insight into louse evolution and environmental stress adaptation',
                'editorial_status_date' => '6/7/2022',
                'editorial_status' => 'Final Decision Reject'
            ],
            [
                'manuscript_number' => 'GIGA-D-22-00030',
                'article_title' => 'A novel ground truth multispectral image dataset with weight, anthocyanins and brix index measures of grape berries tested for its utility in machine learning pipelines',
                'editorial_status_date' => '6/7/2022',
                'editorial_status' => 'Final Decision Pending'
            ],
        ];

        $sampleCsvReport = "console/tests/_data/Report-GIGA-em-manuscripts-latest-214-20220607004243.csv";

        $parsedCsvReportData = EMReportJob::parseReport($sampleCsvReport);

        $this->assertEquals($expectCsvReportData, $parsedCsvReportData, "Csv failed to parse!");

    }

    public function testCanStoreToManuscriptTable()
    {
        $sampleCsvReport = "console/tests/_data/Report-GIGA-em-manuscripts-latest-214-20220607004243.csv";
        $parsedCsvReportData = EMReportJob::parseReport($sampleCsvReport);
        $manuscriptInstance = Manuscript::createInstancesFromEmReport($parsedCsvReportData);

        $emReportJob = new EMReportJob();
        $storeStatus = $emReportJob->storeManuscripts($manuscriptInstance);

        $this->assertTrue(is_bool($storeStatus) === true, "return is not a bool");
        $this->assertTrue($storeStatus === true, "records stored to manuscript table");

    }
}