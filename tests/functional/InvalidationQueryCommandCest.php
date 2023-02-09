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
    public function tryToGetTheLatestCreateTimesFromTablesDatasetlogAndCurationlog(FunctionalTester $I)
    {
        $date = date('Y-m-d H:i:s');
        $I->haveInDatabase("dataset_log", [
                "id" => 8,
                "dataset_id" => 8,
                "message" => "File Pygoscelis_adeliae.RepeatMasker.out.gz updated",
                "created_at" => $date,
                "model" => "File",
                "model_id" => 17679,
                "url" => "/adminFile/update/id/17679"
            ]);
//        $I->haveInDatabase("curation_log", [
//            "id" => 8,
//            "dataset_id" => 8,
//            "creation_date" => $date,
//            "created_by" => "John Smith",
//            "last_modified_date" => $date,
//            "last_modified_by" => "John Smith",
//            "action" => "Approved",
//            "comments" => "None",
//        ]);

        $output = shell_exec(" ./protected/yiic invalidationquery getmaxcreatebyleftjoindatasetlogandcurationlog");
        codecept_debug($output);
        $I->assertContains($date, $output);
    }
//    public function tryToSeeTheUpdatedLocationUrlInDatasetPageWithCachingOn(FunctionalTester $I)
//    {
//        //Swithing on Caching
//        define('DISABLE_CACHE', false);
//        //Login as admin
//        $I->amOnPage("/site/login");
//        $I->submitForm('form.form-horizontal',[
//                'LoginForm[username]' => 'admin@gigadb.org',
//                'LoginForm[password]' => 'gigadb']
//        );
//        $I->canSee("Admin");
//        $I->amOnPage("/adminFile/update/id/17679");
//        $I->fillField("File[location]", "https://test.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.RepeatMasker.out.gz");
//        $I->click("Save");
//        $I->canSee("https://test.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.RepeatMasker.out.gz");
//        $I->canSeeInDatabase("file",
//            [
//                "id" => 17679,
//                "dataset_id" => 8,
//                "location" => "https://test.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.RepeatMasker.out.gz"
//            ]
//        );
//        $I->amOnPage("/dataset/view/id/100006");
//        $I->click("Files");
//        $I->seeLink("Pygoscelis_adeliae.RepeatMasker.out.gz", "https://test.org/pub/gigadb/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.RepeatMasker.out.gz");
//    }
}
