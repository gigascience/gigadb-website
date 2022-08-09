<?php
namespace common\tests;

use common\models\Manuscript;
use Manuscript as GlobalManuscript;

class ManuscriptTest extends \Codeception\Test\Unit
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
    public function testCanCreateTimestamp()
    {
        $manuscript = new Manuscript();
        $this->assertNotNull($manuscript);
        $this->assertNull($manuscript->created_at);
        $this->assertNull($manuscript->updated_at);
        $manuscript->save();
        $this->assertNotNull($manuscript->created_at);
        $manuscript->manuscript_number = "Test-GIGA-D-22-12345";
        sleep(1);
        $manuscript->save();
        $this->assertGreaterThan($manuscript->created_at, $manuscript->updated_at);
    }

    public function testCreateInstanceFromReport()
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

        $manuscriptData = Manuscript::createInstanceFromEmReport($sampleCsvReport);
        $this->assertNotNull($manuscriptData);

        for ($i=0; $i <= count($expectCsvReportData) - 1; $i++) {
            $this->assertEquals($expectCsvReportData[$i]['manuscript_number'], $manuscriptData[$i]->manuscript_number, "Manuscript number is not matched!");
            $this->assertEquals($expectCsvReportData[$i]['article_title'], $manuscriptData[$i]->article_title, "Article title is not matched!");
            $this->assertEquals($expectCsvReportData[$i]['editorial_status_date'], $manuscriptData[$i]->editorial_status_date, "Editorial status date is not matched!");
            $this->assertEquals($expectCsvReportData[$i]['editorial_status'], $manuscriptData[$i]->editorial_status, "Editorial status is not matched!");
        }
    }

//    public function testCanSaveToManuscriptTable()
//    {
////        $sampleCsvReport = "console/tests/_data/Report-GIGA-em-manuscripts-latest-214-20220607004243.csv";
////        $saveToManuscriptTable = Manuscript::saveManuscriptReport($sampleCsvReport);
////
////        $this->assertNotNull($saveToManuscriptTable);
////        $this->assertTrue(is_a($saveToManuscriptTable, Manuscript::class));
//
//        // file_put_contents('test-manuscript.txt', print_r(is_bool($saveToManuscriptTable),true));
//        // $this->assertTrue(is_bool($saveToManuscriptTable) === true, "bool is returned");
//        // $this->assertTrue(true === $saveToManuscriptTable, "No new entry is saved to manuscript table");
//
//    }
}