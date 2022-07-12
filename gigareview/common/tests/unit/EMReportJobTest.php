<?php
namespace common\tests;

use common\models\EMReportJob;

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
    public function testCanParseManuscriptReport()
    {
        $manuscriptPath = "console/tests/_data/Report-GIGA-em-manuscripts-latest-214-20220607004243.csv";
        $manuscript = new EMReportJob();

        file_put_contents('test_report.txt', print_r($manuscript->parseManuscriptReport($manuscriptPath),true));

    }
}