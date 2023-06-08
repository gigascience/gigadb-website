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
        # Check old data is present
        $I->seeInDatabase('dataset', ['identifier' => '100002', 'ftp_site' => 'ftp://climb.genomics.cn/pub/10.5524/100001_101000/100002']);
        $I->seeInDatabase('file', ['name' => 'Pygoscelis_adeliae.gff.gz', 'location' => "ftp://climb.genomics.cn/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz"]);
        $I->seeInDatabase('dataset', ['identifier' => '100003', 'ftp_site' => 'ftp://climb.genomics.cn/pub/10.5524/100001_101000/100003']);
        $I->seeInDatabase('file', ['name' => 'millet.chr.version2.3.fa.gz', 'location' => "ftp://climb.genomics.cn/pub/10.5524/100001_101000/100020/millet.chr.version2.3.fa.gz"]);
        $I->seeInDatabase('dataset', ['identifier' => '100004', 'ftp_site' => 'ftp://climb.genomics.cn/pub/10.5524/100001_101000/100004']);
        $I->seeInDatabase('file', ['id' => '88266', 'location' => "ftp://climb.genomics.cn/pub/10.5524/100001_101000/100020/readme.txt"]);

        # Run tool to update file URLs for dataset 100002
        $I->runShellCommand("echo yes | ./yii_test update/urls --prefix=https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live --separator=/pub/ --doi=100002 --next=3 --excluded=['100020', '100039'] --apply");

        # Check output
        $I->canSeeInShellOutput('Number of file changes: 7 on dataset DOI 100002');
        $I->canSeeInShellOutput('Number of file changes: 6 on dataset DOI 100003');
        $I->canSeeInShellOutput('Number of file changes: 2 on dataset DOI 100004');

        # Check records have been updated
        $I->seeInDatabase('dataset', ['identifier' => '100002', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100002']);
        $I->seeInDatabase('file', ['name' => 'Pygoscelis_adeliae.gff.gz', 'location' => "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz\n"]);
        $I->seeInDatabase('dataset', ['identifier' => '100003', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100003']);
        $I->seeInDatabase('file', ['name' => 'millet.chr.version2.3.fa.gz', 'location' => "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100020/millet.chr.version2.3.fa.gz\n"]);
        $I->seeInDatabase('dataset', ['identifier' => '100004', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100004']);
        $I->seeInDatabase('file', ['id' => '88266', 'location' => "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100020/readme.txt\n"]);

        # Run tool again with the same arguments as before
        $I->runShellCommand("echo yes | ./yii_test update/urls --prefix=https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live --separator=/pub/ --doi=100002 --next=3 --excluded=['100020', '100039'] --apply");

        # Check output
        $I->dontSeeInShellOutput('Number of file changes: 7 on dataset DOI 100002');
        $I->canSeeInShellOutput('Number of file changes: 1 on dataset DOI 100005');
        $I->canSeeInShellOutput('Number of file changes: 24 on dataset DOI 100039');
    }
}
