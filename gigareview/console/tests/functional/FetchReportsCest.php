<?php
namespace console\tests\functional;
use console\tests\FunctionalTester;
use yii\console\ExitCode;

class FetchReportsCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function tryToProcessAcceptedManuscripts(FunctionalTester $I)
    {
        $I->runShellCommand("./yii_test fetch-reports/fetch ", false);
        $I->canSeeInShellOutput("Got content for em-manuscripts-latest.xlsx");
        $I->canSeeShellOutputMatches("/Pushed a new job with ID \d+ for report EM_MANUSCRIPTS to em_manuscripts_q/");
        $I->canSeeResultCodeIs(Exitcode::OK);
    }


    public function tryToProcessAuthors(FunctionalTester $I)
    {
        $I->runShellCommand("./yii_test fetch-reports/fetch ", false);
        $I->canSeeInShellOutput("Got content for em-authors-latest.xlsx");
        $I->canSeeShellOutputMatches("/Pushed a new job with ID \d+ for report EM_AUTHORS to em_authors_q/");
        $I->canSeeResultCodeIs(Exitcode::OK);
    }
}
