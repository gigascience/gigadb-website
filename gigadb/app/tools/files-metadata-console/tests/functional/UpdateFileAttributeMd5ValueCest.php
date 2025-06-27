<?php

namespace tests\functional;

use app\components\DatasetFilesUpdater;
use Exception;

//use GigaDB\services\URLsService;
//use GuzzleHttp\Client;

/**
 * Functional tests to update md5 value in UpdateController
 *
 */
class UpdateFileAttributeMd5ValueCest
{
    public function _before(\FunctionalTester $I)
    {
        // Remove md5 file attribute values for penguin dataset in database
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10669'));
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10670'));
        $I->updateInDatabase('file_attributes', array('value' => ''), array('id' => '10671'));
    }

    /**
     * Check DOI 100006 can be used to update md5 file attribute values
     */
    public function tryToUpdateMD5FileAttributes(\FunctionalTester $I)
    {
        try {
            $out = shell_exec("./yii_test update/md5-values --doi=100039");
            codecept_debug($out);
            $I->assertEquals('Number of changes: 3' . PHP_EOL, $out);
        }
        catch (Exception $e) {
            codecept_debug($e->getMessage());
        }

        // Assert expected md5 values in file attributes table
        $I->seeInDatabase('file_attributes', ['id' => '10669', 'value' => 'd30b8b3549777953aeec9c82e8ac8265']);
        $I->seeInDatabase('file_attributes', ['id' => '10670', 'value' => 'da3aa9c474329f45a5f1053e1e99cc0d']);
        $I->seeInDatabase('file_attributes', ['id' => '10671', 'value' => '35850810fcf14328b9811029b5a0d5b9']);
    }

    /**
     * Check dummy DOI is not associated with any dataset
     */
    public function tryToUpdateMD5FileAttributesWithFakeDOI(\FunctionalTester $I)
    {
        try {
            $out = shell_exec("./yii_test update/md5-values --doi=888888");
            codecept_debug($out);
            # A non-existing DOI will return an error messsage and null output
            $I->assertEquals(null, $out, "Did not receive null output");
        }
        catch (Exception $e) {
            codecept_debug($e->getMessage());
        }
    }

    /**
     * Check only BUSCO_full_table.tsv file in dataset 102722 has md5 value
     * updated, but not file with same name in dataset 102442
     */
    public function tryUpdateMd5ValueOfSameNameFilesInCorrectDataset(\FunctionalTester $I): void
    {
        $out = shell_exec("./yii_test update/md5-values --doi=102722");
        codecept_debug($out);
        $I->assertEquals('Number of changes: 2' . PHP_EOL, $out, "Unexpected number of md5 value changes");

        // Assert expected md5 values in file attributes table
        $I->seeInDatabase('file_attributes', ['file_id' => '551147', 'value' => '61d9dab96def1cf77f7e29bsiumai888']);
        $I->seeInDatabase('file_attributes', ['file_id' => '551150', 'value' => '2590e2216fc2de16ca972ece01bae2d0']);
        // The BUSCO_full_table.tsv file in dataset 10422 should be updated with md5 value
        $I->dontSeeInDatabase('file_attributes', ['file_id' => '537944', 'value' => '61d9dab96def1cf77f7e29bsiumai888']);
    }
}
