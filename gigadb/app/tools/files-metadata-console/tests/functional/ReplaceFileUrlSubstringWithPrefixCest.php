<?php

namespace tests\functional;

use app\components\DatasetFilesUpdater;
use FunctionalTester;

class ReplaceFileUrlSubstringWithPrefixCest
{
    /**
     * Teardown code that is run after each test
     *
     * @return void
     */
    public function _after()
    {
        # pgdmp file is used to restore the test database to original state
        # because of problems getting Db module to work in functional.suite.yml
        shell_exec("pg_restore -c -h database -p 5432 -U gigadb -d gigadb_testdata --no-owner /gigadb/app/tools/files-metadata-console/sql/gigadb_testdata.pgdmp");
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryReplaceFileUrlSubstringWithPrefix(\FunctionalTester $I): void
    {
        $I->runShellCommand("echo yes | ./yii_test update/urls --prefix=https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live --separator=/pub/ --doi=100002 --next=3 --excluded=['100020', '100039'] --apply");
//        $I->canSeeInShellOutput("Downloading production backup for $dateStamp");
//        $I->canSeeInShellOutput("Restoring the backup for $dateStamp");
//        $I->seeResultCodeIs(Exitcode::OK);
    }
}
