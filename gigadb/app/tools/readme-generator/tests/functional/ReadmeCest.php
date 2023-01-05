<?php

use Yii;
use yii\console\ExitCode;

class ReadmeCest {
    public function _before() {
    }

    public function _after() {
    }

    /**
     * Test actionCreate function in ReadmeController
     *
     * @param FunctionalTester $I
     */
    public function tryCreate(\FunctionalTester $I) {
        $I->runShellCommand("/app/yii readme/create --doi 100142 --outdir=/home/curators");
        $I->seeInShellOutput("[DOI] 10.5524/100142");
        $I->runShellCommand("ls /home/curators");
        $I->seeInShellOutput("readme_100142.txt");
    }
}