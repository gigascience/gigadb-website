<?php

class ReadmeCest
{
    /**
     * Teardown code that is run after each test
     * 
     * Currently just removes the readme file for dataset DOI 100142.
     * 
     * @return void
     */
    public function _after()
    {
        if (file_exists("/app/readmeFiles/readme_100004.txt")) {
            unlink("/app/readmeFiles/readme_100004.txt");
        }
        if (file_exists("/app/readmeFiles/readme_100003.txt")) {
            unlink("/app/readmeFiles/readme_100003.txt");
        }
        if (file_exists("/app/readmeFiles/readme_100925.txt")) {
            unlink("/app/readmeFiles/readme_100925.txt");
        }
        if (file_exists("/app/readmeFiles/readme_100142.txt")) {
            unlink("/app/readmeFiles/readme_100142.txt");
        }
    }

    /**
     * Test create readme file for 100004 dataset
     *
     * @param FunctionalTester $I
     */
    public function tryCreate(FunctionalTester $I)
    {
        $I->cantSeeInDatabase("file", ["id" => 6300, "dataset_id" => 212, "name" => "readme_100004.txt", "size" => 2090, "location" => "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/dev/pub/10.5524/100001_101000/100004/readme_100004.txt", "extension" => "txt"]);
        $I->cantSeeInDatabase("file_attributes", ["file_id" => 6300, "attribute_id" => 605, "value" => "9e020a4791800f1ea46ff50c14040579"]);
        $I->runShellCommand("/app/yii_test readme/create --doi 100004 --outdir=/app/readmeFiles --bucketPath wasabi:gigadb-datasets/dev/pub/10.5524");
        $I->seeInShellOutput("[DOI]\n10.5524/100004\n");
        $I->runShellCommand("ls /app/readmeFiles");
        $I->seeInShellOutput("readme_100004.txt");
        $I->canSeeInDatabase("file", ["id" => 6300, "dataset_id" => 212, "name" => "readme_100004.txt", "size" => 2090, "location" => "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/dev/pub/10.5524/100001_101000/100004/readme_100004.txt", "extension" => "txt"]);
        $I->canSeeInDatabase("file_attributes", ["file_id" => 6300, "attribute_id" => 605, "value" => "9e020a4791800f1ea46ff50c14040579"]);
    }

    /**
     * Test update existing readme file for 100003 dataset
     *
     * @param FunctionalTester $I
     */
    public function tryUpdate(FunctionalTester $I)
    {
        $I->canSeeInDatabase("file", ["id" => 88266, "dataset_id" => 211, "name" => "readme.txt", "size" => 1, "location" => "ftp://climb.genomics.cn/pub/10.5524/100001_101000/100003/readme.txt", "extension" => "txt"]);
        $I->cantSeeInDatabase("file_attributes", ["file_id" => 88266, "attribute_id" => 605, "value" => "a10a862d2f2cf362dab3cf21b4a7d000"]);
        $I->runShellCommand("/app/yii_test readme/create --doi 100003 --outdir=/app/readmeFiles --bucketPath wasabi:gigadb-datasets/dev/pub/10.5524");
        $I->seeInShellOutput("[DOI]\n10.5524/100003\n");
        $I->runShellCommand("ls /app/readmeFiles");
        $I->seeInShellOutput("readme_100003.txt");
        $I->canSeeInDatabase("file", ["id" => 88266, "dataset_id" => 211, "name" => "readme_100003.txt", "size" => 2322, "location" => "https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/dev/pub/10.5524/100001_101000/100003/readme_100003.txt", "extension" => "txt"]);
        $I->canSeeInDatabase("file_attributes", ["file_id" => 88266, "attribute_id" => 605, "value" => "a10a862d2f2cf362dab3cf21b4a7d000"]);
    }

    public function tryCompareWithGoldenReadme(FunctionalTester $I)
    {
        $I->runShellCommand("/app/yii_test readme/create --doi 100142 --outdir=/app/readmeFiles --bucketPath wasabi:gigadb-datasets/dev/pub/10.5524");
        $I->assertFileExists("/app/readmeFiles/readme_100142.txt", "readme_100142.txt non exists");
        $generatedReadmeContent = file_get_contents("/app/readmeFiles/readme_100142.txt");
        $goldenReadmeContent = file_get_contents("tests/_data/golden_readme_100142.txt");
        $I->assertEquals($goldenReadmeContent, $generatedReadmeContent, "The generated readme file does not match the golden readme file");
    }
    /**
     * Test functionality using a DOI for a dataset that does not exist
     *
     * @param FunctionalTester $I
     */
    public function tryCreateWithBadDoi(FunctionalTester $I)
    {
        # Test actionCreate function in ReadmeController should fail
        $I->runShellCommand("/app/yii_test readme/create --doi 888888 --outdir=/app/readmeFiles --bucketPath wasabi:gigadb-datasets/dev/pub/10.5524", false);
        $I->seeResultCodeIs(65);

        # Test getReadme function in ReadmeGenerator class to
        # throw exception when no dataset can be found for a DOI
        $expectedExceptionMessage = 'Dataset 888888 not found';
        $I->expectThrowable(new Exception($expectedExceptionMessage), function() {
            $readmeGenerator = new \app\components\ReadmeGenerator();
            $readmeGenerator->getReadme('888888');
        });
    }

    public function tryListAuthors(FunctionalTester $I)
    {
        $I->runShellCommand("/app/yii_test readme/create --doi 100925 --outdir=/app/readmeFiles --bucketPath wasabi:gigadb-datasets/dev/pub/10.5524");
        $I->canSeeInShellOutput("[DOI]\n10.5524/100925\n");
        $I->canSeeInShellOutput("[Citation]\nOu M; Huang R; Yang C; Gui B; Luo Q; Zhao J; Li Y; Liao L; Zhu Z; Wang Y; Chen K (2021): Supporting data for \"Chromosome-level genome assemblies of <i>C. argus</i> and <i>C. maculata</i> and comparative analysis of their temperature adaptability\" GigaScience Database. https://dx.doi.org/10.5524/100925");
        $I->cantSeeInShellOutput("[Citation]\nWang, Y; Ou, M; Huang, R; Luo, Q; Zhao, J; Chen, K; Yang, C; Gui, B; Li, Y; Liao, L; Zhu, Z (2021): Supporting data for \"Chromosome-level genome assemblies of <i>C. argus</i> and <i>C. maculata</i> and comparative analysis of their temperature adaptability\"\nGigaScience Database. https://dx.doi.org/10.5524/100925");
        $I->assertFileExists("/app/readmeFiles/readme_100925.txt", "readme_100925.txt non exists");
        $generatedReadmeContent = file_get_contents("/app/readmeFiles/readme_100925.txt");
        $goldenReadmeContent = file_get_contents("tests/_data/golden_readme_100925.txt");
        $I->assertEquals($goldenReadmeContent, $generatedReadmeContent, "The generated readme file does not match the golden readme file");


    }
}
