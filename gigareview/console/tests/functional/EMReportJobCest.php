<?php
namespace console\tests\functional;
use console\tests\FunctionalTester;
use yii\console\ExitCode;
use console\models\ManuscriptsWorker;

class EMReportJobCest
{
    public function _before(FunctionalTester $I)
    {
        $I->runShellCommand("./yii_test migrate/down all --interactive=0", false);
        $I->runShellCommand("./yii_test migrate/up --interactive=0", false);
    }

    // tests
    public function tryToPushManuscriptQueueJobToTable(FunctionalTester $I)
    {
        $I->runShellCommand("./yii_test fetch-reports/fetch", false);
        $I->runShellCommand("/usr/local/bin/php /app/yii_test manuscripts-q/run --verbose", false);
        $I->canSeeInDatabase('manuscript', ["manuscript_number" => "GIGA-D-22-00054", "article_title" => "A machine learning framework for discovery and enrichment of metagenomics metadata from open access publications", "editorial_status" => "Final Decision Accept", "editorial_status_date" => "2022-06-07"]);
        $I->canSeeInDatabase('manuscript', ["manuscript_number" => "GIGA-D-22-00060", "article_title" => "A chromosome-level genome of the booklouse, Liposcelis brunnea provides insight into louse evolution and environmental stress adaptation", "editorial_status" => "Final Decision Reject", "editorial_status_date" => "2022-06-07"]);
        $I->canSeeInDatabase('manuscript', ["manuscript_number" => "GIGA-D-22-00030", "article_title" => "A novel ground truth multispectral image dataset with weight, anthocyanins and brix index measures of grape berries tested for its utility in machine learning pipelines", "editorial_status" => "Final Decision Pending", "editorial_status_date" => "2022-06-07"]);
        $I->canSeeResultCodeIs(Exitcode::OK);
    }

    public function tryToMatchManuscriptReportWithTable(FunctionalTester $I)
    {
        $manuscriptCsvReport = "console/tests/_data/Report-GIGA-em-manuscripts-latest-214-20220607004243.csv";

        $manuscriptWorker = new ManuscriptsWorker();
        $manuscriptCsvData = $manuscriptWorker->parseManuscriptReport($manuscriptCsvReport);

        $I->runShellCommand("./yii_test fetch-reports/fetch", false);
        $I->runShellCommand("/usr/local/bin/php /app/yii_test manuscripts-q/run --verbose", false);

        $manuscriptCsvRow = count($manuscriptCsvData) - 1;

        for ($i = 0; $i <= $manuscriptCsvRow; $i++) {
            $I->canSeeInDatabase('manuscript', $manuscriptCsvData[$i]);
        }

        $I->canSeeResultCodeIs(Exitcode::OK);
    }
}
