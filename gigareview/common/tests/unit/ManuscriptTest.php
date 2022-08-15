<?php
namespace common\tests;

use common\models\Manuscript;

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

    public function testCanCreateInstanceFromReportWithOnlyOneEntry()
    {
        $sampleCsvReportData = [
            [
                'manuscript_number' => 'GIGA-D-22-00054',
                'article_title' => 'A machine learning framework for discovery and enrichment of metagenomics metadata from open access publications',
                'editorial_status_date' => '6/7/2022',
                'editorial_status' => 'Final Decision Accept'
            ]
        ];

        $manuscriptInstance = Manuscript::createInstancesFromEmReport($sampleCsvReportData);
        $this->assertNotNull($manuscriptInstance);
        $this->assertIsArray($manuscriptInstance);

        foreach ($manuscriptInstance as $instance) {
            $this->assertInstanceOf('common\models\Manuscript', $instance, "Manuscript Instance is not instantiated!");
            $this->assertArrayHasKey('manuscript_number', $instance, "Key is not found in Manuscript instance!");
            $this->assertArrayHasKey('article_title', $instance, "Key is not found in Manuscript instance!");
            $this->assertArrayHasKey('editorial_status_date', $instance, "Key is not found in Manuscript instance!");
            $this->assertArrayHasKey('editorial_status', $instance, "Key is not found in Manuscript instance!");
            $this->assertEquals($sampleCsvReportData[0]['manuscript_number'], $instance->manuscript_number, "Value is not matched in Manuscript instance!");
            $this->assertEquals($sampleCsvReportData[0]['article_title'], $instance->article_title, "Value is not matched in Manuscript instance!");
            $this->assertEquals($sampleCsvReportData[0]['editorial_status_date'], $instance->editorial_status_date, "Value is not matched in Manuscript instance!");
            $this->assertEquals($sampleCsvReportData[0]['editorial_status'], $instance->editorial_status, "Value is not matched in Manuscript instance!");
        }
    }

    public function testCanCreateInstancesFromReportWithTwoEntries()
    {
        $sampleCsvReportData = [
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
        ];

        $manuscriptInstances = Manuscript::createInstancesFromEmReport($sampleCsvReportData);
        $this->assertNotNull($manuscriptInstances);
        $this->assertIsArray($manuscriptInstances);

        for ($i=0; $i <= count($sampleCsvReportData) - 1; $i++) {
            $this->assertInstanceOf('common\models\Manuscript', $manuscriptInstances[$i], "Manuscript Instance is not instantiated!");
            $this->assertArrayHasKey('manuscript_number', $manuscriptInstances[$i], "Key is not found in Manuscript instance!");
            $this->assertArrayHasKey('article_title', $manuscriptInstances[$i], "Key is not found in Manuscript instance!");
            $this->assertArrayHasKey('editorial_status_date', $manuscriptInstances[$i], "Key is not found in Manuscript instance!");
            $this->assertArrayHasKey('editorial_status', $manuscriptInstances[$i], "Key is not found in Manuscript instance!");
            $this->assertEquals($sampleCsvReportData[$i]['manuscript_number'], $manuscriptInstances[$i]->manuscript_number, "Value is not matched in Manuscript instance!");
            $this->assertEquals($sampleCsvReportData[$i]['article_title'], $manuscriptInstances[$i]->article_title, "Value is not matched in Manuscript instance!");
            $this->assertEquals($sampleCsvReportData[$i]['editorial_status_date'], $manuscriptInstances[$i]->editorial_status_date, "Value is not matched in Manuscript instance!");
            $this->assertEquals($sampleCsvReportData[$i]['editorial_status'], $manuscriptInstances[$i]->editorial_status, "Value is not matched in Manuscript instance!");
        }
    }
}