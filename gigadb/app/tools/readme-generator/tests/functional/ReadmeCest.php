<?php

class ReadmeCest
{
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
}
