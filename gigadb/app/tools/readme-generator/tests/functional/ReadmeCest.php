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
        if (file_exists("/home/curators/readme_100142.txt")) {
            unlink("/home/curators/readme_100142.txt");
        }
    }

    /**
     * Test actionCreate function in ReadmeController
     *
     * @param FunctionalTester $I
     */
    public function tryCreate(FunctionalTester $I)
    {
        $I->runShellCommand("/app/yii_test readme/create --doi 100142 --outdir=/home/curators");
        $I->seeInShellOutput("[DOI] 10.5524/100142");
        $I->runShellCommand("ls /home/curators");
        $I->seeInShellOutput("readme_100142.txt");
    }

    /**
     * Test functionality using a DOI for a dataset that does not exist
     *
     * @param FunctionalTester $I
     */
    public function tryCreateWithBadDoi(FunctionalTester $I)
    {
        # Test actionCreate function in ReadmeController should fail
        $I->runShellCommand("/app/yii_test readme/create --doi 888888 --outdir=/home/curators", false);
        $I->seeResultCodeIs(65);

        # Test getReadme function in ReadmeGenerator class to
        # throw exception when no dataset can be found for a DOI
        $expectedExceptionMessage = 'Dataset 888888 not found';
        $I->expectThrowable(new Exception($expectedExceptionMessage), function() {
            $readmeGenerator = new \app\components\ReadmeGenerator();
            $readmeGenerator->getReadme('888888');
        });
    }
}
