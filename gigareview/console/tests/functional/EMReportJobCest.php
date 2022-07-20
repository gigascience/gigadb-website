<?php
namespace console\tests\functional;
use console\tests\FunctionalTester;
use yii\console\ExitCode;

class EMReportJobCest
{
    public function _before(FunctionalTester $I)
    {
    }

    // tests
    public function tryToPushManuscriptQueueJobToTable(FunctionalTester $I)
    {
//        $I->runShellCommand("./yii_test fetch-reports/fetch", false);
//        $I->runShellCommand("/usr/local/bin/php /app/yii manuscripts-q/run --verbose", false);
        $I->amConnectedToDatabase('reviewdb_test');
        $I->canSeeResultCodeIs(Exitcode::OK);
    }
}
