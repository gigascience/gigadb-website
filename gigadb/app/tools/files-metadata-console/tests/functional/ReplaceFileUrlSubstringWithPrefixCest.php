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
        $out = $I->grabEntryFromDatabase('file', array('name' => 'Pygoscelis_adeliae.gff.gz'));
        codecept_debug($out);
        $I->seeInDatabase('file', ['name' => 'Pygoscelis_adeliae.gff.gz', 'location' => "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz\n"]);
        $I->canSeeInShellOutput('Number of file changes: 6 on dataset DOI 100003');
        $I->seeInDatabase('file', ['name' => 'Pygoscelis_adeliae.gff.gz', 'location' => "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz\n"]);
        $I->canSeeInShellOutput('Number of file changes: 2 on dataset DOI 100004');
    }
}
