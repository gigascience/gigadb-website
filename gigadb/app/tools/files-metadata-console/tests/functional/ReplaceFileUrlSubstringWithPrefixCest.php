<?php

namespace tests\functional;

use app\components\DatasetFilesURLUpdater;
use FunctionalTester;
use Yii;

class ReplaceFileUrlSubstringWithPrefixCest
{
    public function _before(\FunctionalTester $I)
    {
//        $db = $I->getModule('Db');
        $I->haveInDatabase('file', ['dataset_id' => 213, 'name' => 'tt_indexcov.html', 'location' => 'http://indexcov.s3-website-us-east-1.amazonaws.com/', 'extension' => 'html', 'size' => 4096, 'format_id' => 41, 'type_id' => 113]);
        $I->haveInDatabase('file', ['dataset_id' => 213, 'name' => 'LAGOS-NE-LOCUSv1.01','location' => 'http://dx.doi.org/10.6073/pasta/940b25d022c695b440e1bdbc49fbb77b', 'extension' => 'unknown', 'size' => 217, 'format_id' => 41, 'type_id' => 113]);
        $I->haveInDatabase('file', ['dataset_id' => 213, 'name' => 'hisat2-cufflinks_wf_pe.cwl','location' => 'https://view.commonwl.org/workflows/github.com/pitagora-network/pitagora-cwl/blob/master/workflows/hisat2-cufflinks/paired_end/hisat2-cufflinks_wf_pe.cwl', 'extension' => 'cwl', 'size' => 1, 'format_id' => 41, 'type_id' => 113]);
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryDryRunExecution(\FunctionalTester $I): void
    {
        # Check old data is present
        $I->seeInDatabase('dataset', ['identifier' => '100002', 'ftp_site' => 'ftp://climb.genomics.cn/pub/10.5524/100001_101000/100002']);
        $I->seeInDatabase('file', ['name' => 'Pygoscelis_adeliae.gff.gz', 'location' => "ftp://climb.genomics.cn/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz"]);

        # Run tool to update file URLs for dataset 100002
        $I->runShellCommand("./yii_test update/urls --separator=/pub/ --next=3 --exclude='100003,100004'");

        # Check output
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100002...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100002...\nDONE (7/7)");
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100142...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100142...\nWARNING (1/4)");
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100039...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100039...\nDONE (24/24)");

        # Check records have not been updated with new Wasabi URLs
        $I->dontseeInDatabase('dataset', ['identifier' => '100002', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/' . Yii::$app->params['DEPLOYMENT_ENV'] . '/pub/10.5524/100001_101000/100002']);
        $I->dontseeInDatabase('file', ['name' => 'Pygoscelis_adeliae.gff.gz', 'location' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/' . Yii::$app->params['DEPLOYMENT_ENV'] . '/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz']);
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
        $I->runShellCommand("./yii_test update/urls --separator=/pub/ --next=3 --exclude='100003,100004' --apply");

        # Check output
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100002...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100002...\nDONE (7/7)");
        $I->dontSeeInShellOutput("\tTransforming ftp_site for dataset 100003...\nDONE");
        $I->dontSeeInShellOutput("\tTransforming ftp_site for dataset 100004...\nDONE");
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100142...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100142...\nWARNING (1/4)");
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100039...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100039...\nDONE (24/24)");

        # Check records have been updated
        $I->seeInDatabase('dataset', ['identifier' => '100002', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/' . Yii::$app->params['DEPLOYMENT_ENV'] . '/pub/10.5524/100001_101000/100002']);
        $I->seeInDatabase('file', ['name' => 'Pygoscelis_adeliae.gff.gz', 'location' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/' . Yii::$app->params['DEPLOYMENT_ENV'] . '/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz']);
        $I->dontseeInDatabase('dataset', ['identifier' => '100003', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/' . Yii::$app->params['DEPLOYMENT_ENV'] . '/pub/10.5524/100001_101000/100003']);
        $I->dontseeInDatabase('file', ['name' => 'millet.chr.version2.3.fa.gz', 'location' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/' . Yii::$app->params['DEPLOYMENT_ENV'] . '/pub/10.5524/100001_101000/100003/millet.chr.version2.3.fa.gz']);
        $I->dontseeInDatabase('dataset', ['identifier' => '100004', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/' . Yii::$app->params['DEPLOYMENT_ENV'] . '/pub/10.5524/100001_101000/100004']);
        $I->dontseeInDatabase('file', ['id' => '88266', 'location' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/' . Yii::$app->params['DEPLOYMENT_ENV'] . '/pub/10.5524/100001_101000/100003/readme.txt']);
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
        $I->seeInDatabase('file', ['name' => 'millet.chr.version2.3.fa.gz', 'location' => "ftp://climb.genomics.cn/pub/10.5524/100001_101000/100003/millet.chr.version2.3.fa.gz"]);
        $I->seeInDatabase('dataset', ['identifier' => '100004', 'ftp_site' => 'ftp://climb.genomics.cn/pub/10.5524/100001_101000/100004']);
        $I->seeInDatabase('file', ['id' => '88266', 'location' => "ftp://climb.genomics.cn/pub/10.5524/100001_101000/100003/readme.txt"]);
        $I->seeInDatabase('dataset', ['identifier' => '100142', 'ftp_site' => 'https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142']);

        # Run tool to update file URLs for dataset 100002
        $I->runShellCommand("./yii_test update/urls --separator=/pub/ --next=3 --apply");

        # Check output
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100002...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100002...\nDONE (7/7)");
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100003...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100003...\nDONE (6/6)");
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100004...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100004...\nDONE (2/2)");

        # Check records have been updated
        $I->seeInDatabase('dataset', ['identifier' => '100002', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/' . Yii::$app->params['DEPLOYMENT_ENV'] . '/pub/10.5524/100001_101000/100002']);
        $I->seeInDatabase('file', ['name' => 'Pygoscelis_adeliae.gff.gz', 'location' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/' . Yii::$app->params['DEPLOYMENT_ENV'] . '/pub/10.5524/100001_101000/100006/phylogeny_study_update/Pygoscelis_adeliae.gff.gz']);
        $I->seeInDatabase('dataset', ['identifier' => '100003', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/' . Yii::$app->params['DEPLOYMENT_ENV'] . '/pub/10.5524/100001_101000/100003']);
        $I->seeInDatabase('file', ['name' => 'millet.chr.version2.3.fa.gz', 'location' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/' . Yii::$app->params['DEPLOYMENT_ENV'] . '/pub/10.5524/100001_101000/100003/millet.chr.version2.3.fa.gz']);
        $I->seeInDatabase('dataset', ['identifier' => '100004', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/' . Yii::$app->params['DEPLOYMENT_ENV'] . '/pub/10.5524/100001_101000/100004']);
        $I->seeInDatabase('file', ['id' => '88266', 'location' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/' . Yii::$app->params['DEPLOYMENT_ENV'] . '/pub/10.5524/100001_101000/100003/readme.txt']);

        # Run tool again with the same arguments as before
        $I->runShellCommand("./yii_test update/urls --separator=/pub/ --next=3 --exclude='100003,100004' --apply");

        # Check output
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100142...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100142...\nWARNING (1/4)");
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100039...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100039...\nDONE (24/24)");

        # Check ftp_site URL has been updated for dataset 100142
        $I->seeInDatabase('dataset', ['identifier' => '100142', 'ftp_site' => 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/' . Yii::$app->params['DEPLOYMENT_ENV'] . '/pub/10.5524/100001_101000/100142']);
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryStopDoi(\FunctionalTester $I): void
    {
        # Run tool to update file URLs for dataset 100002
        $I->runShellCommand("./yii_test update/urls --separator=/pub/ --next=3 --stop=100004 --apply");

        # Check output
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100002...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100002...\nDONE (7/7)");
        $I->canSeeInShellOutput("\tTransforming ftp_site for dataset 100003...\nDONE");
        $I->canSeeInShellOutput("\tTransforming file locations for dataset 100003...\nDONE (6/6)");
        $I->cantSeeInShellOutput("\tTransforming ftp_site for dataset 100004...\nDONE");
        $I->cantSeeInShellOutput("\tTransforming file locations for dataset 100004...\nDONE (2/2)");
        $I->canSeeInShellOutput("Stop DOI 100004 been reached - processing will stop now.");
    }
}
