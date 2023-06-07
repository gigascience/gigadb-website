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
