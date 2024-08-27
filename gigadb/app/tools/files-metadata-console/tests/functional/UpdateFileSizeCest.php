<?php

namespace tests\functional;

use Exception;

class UpdateFileSizeCest
{
    public function _before(\FunctionalTester $I)
    {
        // Remove file size values in database
        $I->updateInDatabase('file', array('size' => 0), array('id' => 453));
        $I->updateInDatabase('file', array('size' => 0), array('id' => 454));
        $I->updateInDatabase('file', array('size' => 0), array('id' => 457));

    }

    public function tryUpdateFileSizes(\FunctionalTester $I): void
    {
        try {
            $out = shell_exec("./yii_test update/file-sizes --doi=100039");
            codecept_debug($out);
            $I->assertEquals('Number of changes: 3' . PHP_EOL, $out);
        }
        catch (Exception $e) {
            codecept_debug($e->getMessage());
        }

        // Assert expected file sizes in file table
        $I->seeInDatabase('file', ['id' => 453, 'size' => 4245]);
        $I->seeInDatabase('file', ['id' => 454, 'size' => 593]);
        $I->seeInDatabase('file', ['id' => 457, 'size' => 581]);
    }

    /**
     * Check dummy DOI is not associated with any dataset
     */
    public function tryToUpdateFileSizesWithFakeDOI(\FunctionalTester $I)
    {
        try {
            $out = shell_exec("./yii_test update/file-sizes --doi=888888");
            codecept_debug($out);
            # A non-existing DOI will return an error message and null output
            $I->assertEquals(null, $out, "Did not receive null output");
        }
        catch (Exception $e) {
            codecept_debug($e->getMessage());
        }
    }
}
