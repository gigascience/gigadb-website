<?php
namespace console\tests\functional;
use console\tests\FunctionalTester;
use yii\console\ExitCode;

class FetchReportsCest
{
    public function _before(FunctionalTester $I)
    {
    }

    /**
     * @param FunctionalTester $I
     * @return void
     *
     * @example ["manuscripts", "/em-manuscripts-latest.xlsx", "manuscripts_q"]
     * @example ["authors", "/em-authors-latest.xlsx", "authors_q"]
     * @example ["reviewers", "/em-reviewers-latest.xlsx", "reviewers_q"]
     * @example ["reviewersQuestionsResponses", "/em-reviewers-questions-responses-latest.xlsx", "reviewersQuestionsResponses_q"]
     *
     */
    public function tryToFetchAndPublishReports(FunctionalTester $I, \Codeception\Example $example)
    {
        $I->runShellCommand("./yii_test fetch-reports/fetch", false);
        $I->canSeeInShellOutput("Got content for {$example[1]}");
        $I->canSeeShellOutputMatches("/Pushed a new job with ID \d+ for report {$example[0]} to {$example[2]}/");
        $I->canSeeResultCodeIs(Exitcode::OK);
    }

}
