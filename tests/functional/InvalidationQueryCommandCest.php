<?php

/**
 * Class InvalidationQueryCommandCest
 *
 * generated with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept generate:cest functional InvalidationQueryCommandCest.php
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run functional InvalidationQueryCommandCest
 */
class InvalidationQueryCommandCest
{
    public function _before(FunctionalTester $I)
    {
    }

    // tests
    public function tryToGetTheLatestCreatedAtFromTableDatasetlog(FunctionalTester $I)
    {
        $date = date('Y-m-d H:i:s');
        $I->haveInDatabase("dataset_log", [
                "dataset_id" => 8,
                "message" => "File Pygoscelis_adeliae.RepeatMasker.out.gz updated",
                "created_at" => $date,
                "model" => "File",
                "model_id" => 17679,
                "url" => "/adminFile/update/id/17679"
            ]);

        $output = shell_exec(" ./protected/yiic invalidationquery getlatestcreateusingqueryfrommainconfigfile");
        codecept_debug($output);
        $I->assertContains($date, $output);
    }

    public function tryToGetTheLatestCreationDateFromTableCurationlog(FunctionalTester $I)
    {
        $date = date('Y-m-d H:i:s');
        $I->haveInDatabase("curation_log", [
            "dataset_id" => 8,
            "creation_date" => $date,
            "created_by" => "John Smith",
            "last_modified_date" => $date,
            "last_modified_by" => "John Smith",
            "action" => "Approved",
            "comments" => "None",
        ]);

        $output = shell_exec(" ./protected/yiic invalidationquery getlatestcreateusingqueryfrommainconfigfile");
        codecept_debug($output);
        $I->assertContains($date, $output);
    }

    public function tryToGetTheLatestCreatedAtFromDatasetlogAndCreationDateFromCurationlog(FunctionalTester $I)
    {
        $date = date('Y-m-d H:i:s');
        $I->haveInDatabase("dataset_log", [
            "dataset_id" => 8,
            "message" => "File Pygoscelis_adeliae.RepeatMasker.out.gz updated",
            "created_at" => $date,
            "model" => "File",
            "model_id" => 17679,
            "url" => "/adminFile/update/id/17679"
        ]);

        $I->haveInDatabase("curation_log", [
            "dataset_id" => 8,
            "creation_date" => $date,
            "created_by" => "John Smith",
            "last_modified_date" => $date,
            "last_modified_by" => "John Smith",
            "action" => "Approved",
            "comments" => "None",
        ]);

        $output = shell_exec(" ./protected/yiic invalidationquery getlatestcreateusingqueryfrommainconfigfile");
        codecept_debug($output);
        $I->assertContains($date, $output);
    }
}
