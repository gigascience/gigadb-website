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
     *
     * Test fetching command on a set of examples
     *
     * @param FunctionalTester $I
     * @return void
     *
     * @example ["manuscripts", "/Report-GIGA-em-manuscripts-latest-214-20220607004243.csv", "manuscripts_q"]
     * @example ["authors", "/Report-GIGA-em-authors-latest-214-20220607004243.csv", "authors_q"]
     * @example ["reviewers", "/Report-GIGA-em-reviews-latest-214-20220607004243.csv", "reviewers_q"]
     * @example ["reviewersQuestionsResponses", "/Report-GIGA-em-reviewers-questions-responses-latest-214-20220607004243.csv", "reviewersQuestionsResponses_q"]
     *
     */
    public function tryToFetchAndPublishReports(FunctionalTester $I, \Codeception\Example $example)
    {
        $I->runShellCommand("./yii_test fetch-reports/fetch", false);
        $I->canSeeInShellOutput("Got content for {$example[1]}");
        $I->canSeeShellOutputMatches("/Pushed a new job with ID \d+ for report {$example[0]} to {$example[2]}/");
        $I->canSeeResultCodeIs(Exitcode::OK);
    }


    /**
     * @param FunctionalTester $I
     * @param \Codeception\Example $example
     * @return void
     *
     * @example ["manuscripts", "Report-GIGA-em-manuscripts-latest", "/Report-GIGA-em-manuscripts-latest-214-20220607004243.csv"]
     * @example ["authors", "Report-GIGA-em-authors-latest", "/Report-GIGA-em-authors-latest-214-20220607004243.csv"]
     * 
     */
    public function tryToListRemoteFiles(FunctionalTester $I, \Codeception\Example $example)
    {
        $I->runShellCommand("./yii_test fetch-reports/list {$example[1]}", false);
        $I->canSeeInShellOutput("{$example[2]}");
        $I->canSeeResultCodeIs(Exitcode::OK);
    }

}
