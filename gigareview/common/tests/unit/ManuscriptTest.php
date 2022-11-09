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
            $this->assertStringMatchesFormat('Final Decision Accept', $sampleCsvReportData[0]['editorial_status'], "Value ".$sampleCsvReportData[0]['editorial_status']." pattern is not matched in Manuscript csv file!");
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
                'editorial_status' => 'Final Decision Accept'
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
            $this->assertStringMatchesFormat('Final Decision Accept', $sampleData['editorial_status'], "Value ".$sampleData['editorial_status']." pattern is not matched in Manuscript csv file!");
            $this->assertEquals($sampleData['manuscript_number'], $manuscriptInstance->manuscript_number, "Value ".$sampleData['manuscript_number']." is not matched in Manuscript instance!");
            $this->assertEquals($sampleData['article_title'], $manuscriptInstance->article_title, "Value ".$sampleData['article_title']." is not matched in Manuscript instance!");
            $this->assertEquals($sampleData['editorial_status_date'], $manuscriptInstance->editorial_status_date, "Value ".$sampleData['editorial_status_date']." is not matched in Manuscript instance!");
            $this->assertEquals($sampleData['editorial_status'], $manuscriptInstance->editorial_status, "Value ".$sampleData['editorial_status']." is not matched in Manuscript instance!");

        }, $sampleCsvReportData, $manuscriptInstances);
    }

    /**
     * Checks validation of editorial status dates
     */
    public function testEditorialStatusDateValidation()
    {
        $sampleCsvReportData = [
            [
                'manuscript_number' => 'GIGA-D-22-00001',
                'article_title' => 'Factum per Litteras',
                # Since dates in Manuscript objects need to be in MM/dd/yyyy
                # format, this editorial_status_date is not valid
                'editorial_status_date' => '13/6/2022',
                'editorial_status' => 'Final Decision Accept'
            ],
            [
                'manuscript_number' => 'GIGA-D-22-00002',
                'article_title' => 'Pedes in Terra ad Sidera Visus',
                # Valid editorial_status_date
                'editorial_status_date' => '05/16/2022',
                'editorial_status' => 'Final Decision Accept'
            ]
        ];

        $manuscriptInstances = Manuscript::createInstancesFromEmReport($sampleCsvReportData);

        foreach ($manuscriptInstances as $instance) {
            if($instance->validate()) {
                # Confirm second manuscript is ok
                $this->assertStringContainsString("Pedes in Terra ad Sidera Visus", $instance->article_title);
            }
            else {
                # Confirm problem with first manuscript object
                $this->assertStringContainsString("Factum per Litteras", $instance->article_title);
                # Confirm problem is with editorial_status_date attribute
                $first_errors = $instance->getFirstErrors();
                $this->assertArrayHasKey('editorial_status_date', $first_errors, "No editorial_status_date error found!");
                $this->assertStringContainsString('Editorial Status Date is invalid', $first_errors['editorial_status_date']);
            }
        }
    }

    /**
     * Checks validation of editorial statuses
     */
    public function testEditorialStatusValidation()
    {
        $sampleCsvReportData = [
            [
                'manuscript_number' => 'GIGA-D-22-00001',
                'article_title' => 'Factum per Litteras',
                'editorial_status_date' => '6/12/2022',
                'editorial_status' => 'Final Decision Accept'  # Valid
            ],
            [
                'manuscript_number' => 'GIGA-D-22-00006',
                'article_title' => 'Alea iacta est',
                'editorial_status_date' => '6/12/2022',
                'editorial_status' => 'Final Decision Reject'  # Invalid
            ]
        ];

        $manuscriptInstances = Manuscript::createInstancesFromEmReport($sampleCsvReportData);

        foreach ($manuscriptInstances as $instance) {
            if($instance->validate()) {
                # Confirm first manuscript is ok
                $this->assertStringContainsString("Final Decision Accept", $instance->editorial_status);
            }
            else {
                # Confirm problem with second manuscript object
                $this->assertStringContainsString("Alea iacta est", $instance->article_title);
                # Confirm problem is with editorial_status attribute
                $first_errors = $instance->getFirstErrors();
                $this->assertArrayHasKey('editorial_status', $first_errors, "There should be a editorial_status validation error found!");
                $this->assertStringContainsString('Editorial Status is invalid', $first_errors['editorial_status']);
            }
        }
    }

    /**
     * Checks validation of manuscript numbers
     */
    public function testManuscriptNumberValidation()
    {
        $sampleCsvReportData = [
            [
                'manuscript_number' => 'GIGA-D-22-00001',
                'article_title' => 'Factum per Litteras',
                'editorial_status_date' => '6/12/2022',
                'editorial_status' => 'Final Decision Accept'  # Valid
            ],
            [
                'manuscript_number' => 'GIGA-D-22-abcde',
                'article_title' => 'Acta, non verba',
                'editorial_status_date' => '6/12/2022',
                'editorial_status' => 'Final Decision Accept'  # Invalid
            ]
        ];

        $manuscriptInstances = Manuscript::createInstancesFromEmReport($sampleCsvReportData);

        foreach ($manuscriptInstances as $instance) {
            if($instance->validate()) {
                # Confirm first manuscript is ok
                $this->assertStringContainsString("Factum per Litteras", $instance->article_title);
            }
            else {
                # Confirm problem with second manuscript object
                $this->assertStringContainsString("Acta, non verba", $instance->article_title);
                # Confirm problem is with manuscript_number attribute
                $first_errors = $instance->getFirstErrors();
                $this->assertArrayHasKey('manuscript_number', $first_errors, "There should be a manuscript number validation error found!");
                $this->assertStringContainsString('Manuscript Number is invalid', $first_errors['manuscript_number']);
            }
        }
    }
}