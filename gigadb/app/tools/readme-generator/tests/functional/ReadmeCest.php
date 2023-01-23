<?php

class ReadmeCest
{
    /**
     * Teardown code that is run after each test
     * 
     * Currently just removes the readme file for dataset DOI 100005.
     * 
     * @return void
     */
    public function _after()
    {
        if (file_exists("/home/curators/readme_100005.txt")) {
            unlink("/home/curators/readme_100005.txt");
        }
    }

    /**
     * Test actionCreate function in ReadmeController
     *
     * @param FunctionalTester $I
     */
    public function tryCreate(FunctionalTester $I)
    {
        $I->runShellCommand("/app/yii_test readme/create --doi 100005 --outdir=/home/curators");
        $I->seeInShellOutput("[DOI] 10.5524/100005");
        $I->runShellCommand("ls /home/curators");
        $I->seeInShellOutput("readme_100005.txt");
    }
}
