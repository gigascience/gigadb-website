<?php

declare(strict_types=1);

class UpdateDatasetDataciteCest
{
    public function TryRunningCommand(\CliTester $I)
    {
        $I->seeInDatabase('dataset', ['id' => 8]);

        $I->runShellCommand('./protected/yiic updatedatasetdatacite 2>&1');

        $I->seeInShellOutput('[OK] Dataset 100006 successfully updated');
        $I->seeInShellOutput('[ERROR] Failed for dataset 102484: 422 - DOI 10.80027/102484: Missing child element(s). Expected is ( {http://datacite.org/schema/kernel-4}creator ). at line 4, column 0');

        $I->seeInDatabase('curation_log', ['dataset_id' => 8]);
        $I->seeInDatabase('curation_log', ['dataset_id' => 8, 'comments' => 'Metadata response: 201 - OK (10.80027/100006)']);
        $I->seeInDatabase('curation_log', ['dataset_id' => 2740, 'comments' => 'Metadata response: 422 - DOI 10.80027/102484: Missing child element(s). Expected is ( {http://datacite.org/schema/kernel-4}creator ). at line 4, column 0']);
        $I->seeInDatabase('dataset_log', ['dataset_id' => 2740, 'message' => 'Metadata response: ERROR']);
        $I->seeInDatabase('dataset_log', ['dataset_id' => 8, 'message' => 'Metadata response: OK']);
    }
}
