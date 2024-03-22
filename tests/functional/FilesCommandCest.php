<?php

/**
 * Functional tests for functions in FilesCommand
 *
 */
class FilesCommandCest
{
    public function _before(FunctionalTester $I)
    {
        // Remove md5 file attribute values for penguin dataset in database
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10669'));
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10670'));
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10671'));
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10672'));
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10673'));
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10674'));
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10675'));
    }

    /**
     * Check DOI 100006 can be used to download a md5 file that is then used to
     * update md5 file attribute values
     */
    public function tryToUpdateMD5FileAttributes(FunctionalTester $I)
    {
        // Execute FileCommand function to update md5 values for penguin dataset 100006
        $output = shell_exec("./protected/yiic_test files updateMD5FileAttributes --doi=100006");
        codecept_debug($output);

        // Assert expected md5 values in file attributes table
        $I->seeInDatabase('file_attributes', ['id' => '10669', 'value' => '23c3241e6bc362d659a4b589c8d9e01c']);
        $I->seeInDatabase('file_attributes', ['id' => '10670', 'value' => '47b8f47ca31cfd06d5ad62ceceb99860']);
        $I->seeInDatabase('file_attributes', ['id' => '10671', 'value' => '43b35c4e828bed20dbb071d2c5a40f17']);
        $I->seeInDatabase('file_attributes', ['id' => '10672', 'value' => '5afc9d8348bf4b52ee6e9c2bae9fd542']);
        $I->seeInDatabase('file_attributes', ['id' => '10673', 'value' => 'bd9bed43475eaa22b6ab62b9fb7a3909']);
        $I->seeInDatabase('file_attributes', ['id' => '10674', 'value' => '55c764721558086197bfbd663e1567a6']);
        $I->seeInDatabase('file_attributes', ['id' => '10675', 'value' => '826b699c854cc0f06e982d836410a81b']);
    }

    /**
     * Check dummy DOI is not associated with any dataset
     */
    public function tryToUpdateMD5FileAttributesWithFakeDOI(FunctionalTester $I)
    {
        // Execute FileCommand function with fake doi
        $output = shell_exec("./protected/yiic_test files updateMD5FileAttributes --doi=888888");
        $I->assertContains("No dataset found in database with DOI 888888", $output);
    }

}
