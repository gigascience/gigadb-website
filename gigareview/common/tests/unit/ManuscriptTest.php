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
        $manuscript->manuscript_number = "GIGA-D-22-12345";
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
            $this->assertArrayHasKey('manuscript_number', $instance, "Key manuscript_number is not found in Manuscript instance!");
            $this->assertArrayHasKey('article_title', $instance, "Key article_title is not found in Manuscript instance!");
            $this->assertArrayHasKey('editorial_status_date', $instance, "Key editorial_status_date is not found in Manuscript instance!");
            $this->assertArrayHasKey('editorial_status', $instance, "Key editorial_status is not found in Manuscript instance!");
            $this->assertRegExp('/^GIGA\-D\-\d{2}\-\d{5}$/', $sampleCsvReportData[0]['manuscript_number'], "Value ".$sampleCsvReportData[0]['manuscript_number']." pattern is not matched in Manuscript csv file!");
            $this->assertRegExp('/^Final\sDecision\s[a-zA-Z]+$/', $sampleCsvReportData[0]['editorial_status'], "Value ".$sampleCsvReportData[0]['editorial_status']." pattern is not matched in Manuscript csv file!");
            $this->assertEquals($sampleCsvReportData[0]['manuscript_number'], $instance->manuscript_number, "Value ".$sampleCsvReportData[0]['manuscript_number']." is not matched in Manuscript instance!");
            $this->assertEquals($sampleCsvReportData[0]['article_title'], $instance->article_title, "Value ".$sampleCsvReportData[0]['article_title']." is not matched in Manuscript instance!");
            $this->assertEquals($sampleCsvReportData[0]['editorial_status_date'], $instance->editorial_status_date, "Value ".$sampleCsvReportData[0]['editorial_status_date']." is not matched in Manuscript instance!");
            $this->assertEquals($sampleCsvReportData[0]['editorial_status'], $instance->editorial_status, "Value ".$sampleCsvReportData[0]['editorial_status']." is not matched in Manuscript instance!");
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

        array_map(function($sampleData, $manuscriptInstance){
            $this->assertInstanceOf('common\models\Manuscript', $manuscriptInstance, "Manuscript Instance is not instantiated!");
            $this->assertArrayHasKey('manuscript_number', $manuscriptInstance, "Key manuscript_number is not found in Manuscript instance!");
            $this->assertArrayHasKey('article_title', $manuscriptInstance, "Key article_title is not found in Manuscript instance!");
            $this->assertArrayHasKey('editorial_status_date', $manuscriptInstance, "Key editorial_status_date is not found in Manuscript instance!");
            $this->assertArrayHasKey('editorial_status', $manuscriptInstance, "Key editorial_status is not found in Manuscript instance!");
            $this->assertRegExp('/^GIGA\-D\-\d{2}\-\d{5}$/', $sampleData['manuscript_number'],"Value ".$sampleData['manuscript_number']." pattern is not matched in Manuscript csv file!");
            $this->assertRegExp('/^Final\sDecision\s[a-zA-Z]+$/', $sampleData['editorial_status'], "Value ".$sampleData['editorial_status']." pattern is not matched in Manuscript csv file!");
            $this->assertEquals($sampleData['manuscript_number'], $manuscriptInstance->manuscript_number, "Value ".$sampleData['manuscript_number']." is not matched in Manuscript instance!");
            $this->assertEquals($sampleData['article_title'], $manuscriptInstance->article_title, "Value ".$sampleData['article_title']." is not matched in Manuscript instance!");
            $this->assertEquals($sampleData['editorial_status_date'], $manuscriptInstance->editorial_status_date, "Value ".$sampleData['editorial_status_date']." is not matched in Manuscript instance!");
            $this->assertEquals($sampleData['editorial_status'], $manuscriptInstance->editorial_status, "Value ".$sampleData['editorial_status']." is not matched in Manuscript instance!");

        }, $sampleCsvReportData, $manuscriptInstances);
    }
}