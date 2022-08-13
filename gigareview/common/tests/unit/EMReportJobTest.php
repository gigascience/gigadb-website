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

        $sampleCsvReportPath = "console/tests/_data/Report-GIGA-em-manuscripts-latest-214-20220607004243.csv";

        $parsedCsvReportData = EMReportJob::parseReport($sampleCsvReportPath);

        $this->assertEquals($expectCsvReportData, $parsedCsvReportData, "Csv failed to parse!");

    }

    public function testCanStoreOneInstanceToManuscriptTable()
    {
        $mockManuscriptOne = $this->make(Manuscript::class,
            [
            'manuscript_number' => 'GIGA-D-22-00054',
            'article_title' => 'A machine learning framework for discovery and enrichment of metagenomics metadata from open access publications',
            'editorial_status_date' => '6/7/2022',
            'editorial_status' => 'Final Decision Accept'
            ]
        );

        $mockManuscript[] = $mockManuscriptOne;

        foreach ($mockManuscript as $manuscript) {
            $this->assertInstanceOf('common\models\Manuscript', $manuscript, "Mock manuscript instance not created!");
        }

        $emReportJob = new EMReportJob();
        $storeStatus = $emReportJob->storeManuscripts($mockManuscript);

        $this->assertTrue(is_bool($storeStatus) === true, "Return is not a bool");
        $this->assertTrue($storeStatus === true, "Record stored to manuscript table");
    }

    public function testCanStoreTwoInstancesToManuscriptTable()
    {
        $mockManuscriptOne = $this->make(Manuscript::class,
                [
                    'manuscript_number' => 'GIGA-D-22-00054',
                    'article_title' => 'A machine learning framework for discovery and enrichment of metagenomics metadata from open access publications',
                    'editorial_status_date' => '6/7/2022',
                    'editorial_status' => 'Final Decision Accept',
                ]
        );

        $mockManuscriptTwo = $this->make(Manuscript::class,
            [
                'manuscript_number' => 'GIGA-D-22-00060',
                'article_title' => 'A chromosome-level genome of the booklouse, Liposcelis brunnea provides insight into louse evolution and environmental stress adaptation',
                'editorial_status_date' => '6/7/2022',
                'editorial_status' => 'Final Decision Reject'
            ]
        );

        $mockManuscripts = [];
        array_push($mockManuscripts, $mockManuscriptOne, $mockManuscriptTwo);

        foreach ($mockManuscripts as $mockManuscript) {
            $this->assertInstanceOf('common\models\Manuscript', $mockManuscript,"Mock manuscript instance not created!");
        }

        $emReportJob = new EMReportJob();
        $storeStatus = $emReportJob->storeManuscripts($mockManuscripts);

        $this->assertTrue(is_bool($storeStatus) === true, "Return is not a bool");
        $this->assertTrue($storeStatus === true, "Records stored to manuscript table");
    }

    public function testCannotStoreEmptyInstanceToManuscriptTable()
    {
        $mockManuscriptEmpty = $this->make(Manuscript::class);

        $mockManuscripts[] = $mockManuscriptEmpty;

        foreach ($mockManuscripts as $mockManuscript) {
            $this->assertInstanceOf('common\models\Manuscript', $mockManuscript, "Mock manuscript instance not created!");
        }

        $emReportJob = new EMReportJob();
        $storeStatus = $emReportJob->storeManuscripts($mockManuscripts);

        $this->assertTrue(is_bool($storeStatus) === true, "Return is not a bool");
        $this->assertFalse($storeStatus === false, "Records stored to manuscript table");
    }
}