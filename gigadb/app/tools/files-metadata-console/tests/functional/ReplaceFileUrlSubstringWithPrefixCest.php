<?php

namespace tests\functional;

use app\components\DatasetFilesUpdater;
use FunctionalTester;

class ReplaceFileUrlSubstringWithPrefixCest
{
    /**
     * @param FunctionalTester $I
     */
    public function tryReplaceFileUrlSubstringWithPrefix(\FunctionalTester $I): void
    {
        $I->runShellCommand("echo yes | ./yii_test update/urls --prefix=https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live --separator=/pub/ --doi=100002 --next=3 --excluded=['100020', '100039'] --apply");
        $I->canSeeInShellOutput('Number of file changes: 7 on dataset DOI 100002');
        $I->canSeeInShellOutput('Number of file changes: 6 on dataset DOI 100003');
        $I->canSeeInShellOutput('Number of file changes: 2 on dataset DOI 100004');
    }
}
