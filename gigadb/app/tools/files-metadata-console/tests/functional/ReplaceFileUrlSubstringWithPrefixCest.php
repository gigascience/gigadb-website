<?php

namespace tests\functional;

use app\components\DatasetFilesURLUpdater;
use FunctionalTester;

class ReplaceFileUrlSubstringWithPrefixCest
{
    /**
     * @param FunctionalTester $I
     */
    public function tryDryRunExecution(\FunctionalTester $I): void
    {
        # Check old data is present
        $I->seeInDatabase('dataset', ['identifier' => '100002', 'ftp_site' => 'ftp://climb.genomics.cn/pub/10.5524/100001_101000/100002']);
        $I->seeInDatabase('file', ['name' => 'Pygoscelis_adeliae.gff.gz', 'location' => "ftp://climb.genomics.cn/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz"]);

        # Run tool to update file URLs for dataset 100002
        $I->runShellCommand("echo yes | ./yii_test update/urls --prefix=https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live --separator=/pub/ --doi=100002 --next=3 --excluded='100003,100004'");

        # Check output
        $I->canSeeInShellOutput('Number of file location changes: 0 on dataset DOI 100002');
        $I->canSeeInShellOutput('Number of file location changes: 0 on dataset DOI 100005');
        $I->canSeeInShellOutput('Number of file location changes: 0 on dataset DOI 100039');

        # Check records have not been updated with new Wasabi URLs
        $I->dontseeInDatabase('dataset', ['identifier' => '100002', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100002']);
        $I->dontseeInDatabase('file', ['name' => 'Pygoscelis_adeliae.gff.gz', 'location' => "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz"]);
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryExcludingDoisFromFileUrlChanges(\FunctionalTester $I): void
    {
        # Check old data is present
        $I->seeInDatabase('dataset', ['identifier' => '100002', 'ftp_site' => 'ftp://climb.genomics.cn/pub/10.5524/100001_101000/100002']);
        $I->seeInDatabase('file', ['name' => 'Pygoscelis_adeliae.gff.gz', 'location' => "ftp://climb.genomics.cn/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz"]);

        # Run tool to update file URLs for dataset 100002
        $I->runShellCommand("./yii_test update/urls --prefix=https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live --separator=/pub/ --doi=100002 --next=3 --excluded='100003,100004' --apply");

        # Check output
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100002...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100002...\nDONE (7/7)");
        $I->dontSeeInShellOutput("\tTransforming ftp_site for dataset 100003...\nDONE");
        $I->dontSeeInShellOutput("\tTransforming ftp_site for dataset 100004...\nDONE");
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100005...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100005...\nDONE (1/1)");
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100039...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100039...\nDONE (24/24)");

        # Check records have been updated
        $I->seeInDatabase('dataset', ['identifier' => '100002', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100002']);
        $I->seeInDatabase('file', ['name' => 'Pygoscelis_adeliae.gff.gz', 'location' => "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz"]);
        $I->dontseeInDatabase('dataset', ['identifier' => '100003', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100003']);
        $I->dontseeInDatabase('file', ['name' => 'millet.chr.version2.3.fa.gz', 'location' => "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100020/millet.chr.version2.3.fa.gz\n"]);
        $I->dontseeInDatabase('dataset', ['identifier' => '100004', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100004']);
        $I->dontseeInDatabase('file', ['id' => '88266', 'location' => "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100020/readme.txt\n"]);
    }

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
        $I->runShellCommand("echo yes | ./yii_test update/urls --prefix=https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live --separator=/pub/ --doi=100002 --next=3 --apply");

        # Check output
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100002...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100002...\nDONE (7/7)");
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100003...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100003...\nDONE (6/6)");
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100004...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100004...\nDONE (2/2)");

        # Check records have been updated
        $I->seeInDatabase('dataset', ['identifier' => '100002', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100002']);
        $I->seeInDatabase('file', ['name' => 'Pygoscelis_adeliae.gff.gz', 'location' => "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz"]);
        $I->seeInDatabase('dataset', ['identifier' => '100003', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100003']);
        $I->seeInDatabase('file', ['name' => 'millet.chr.version2.3.fa.gz', 'location' => "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100020/millet.chr.version2.3.fa.gz"]);
        $I->seeInDatabase('dataset', ['identifier' => '100004', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100004']);
        $I->seeInDatabase('file', ['id' => '88266', 'location' => "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100020/readme.txt"]);

        # Run tool again with the same arguments as before
        $I->runShellCommand("echo yes | ./yii_test update/urls --prefix=https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live --separator=/pub/ --doi=100002 --next=3 --excluded='100003,100004' --apply");

        # Check output
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100005...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100005...\nDONE (1/1)");
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100039...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100039...\nDONE (24/24)");
    }
}
