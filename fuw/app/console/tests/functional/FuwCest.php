<?php

namespace console\tests;

use console\tests\FunctionalTester;
use Yii;
use common\models\Upload;
use backend\models\FiledropAccount;
use backend\fixtures\FiledropAccountFixture;
use yii\console\ExitCode;

class FuwCest {

    public function tryRemoveDropbox(FunctionalTester $I) {
        $doi = "300001";

    	$accountId = $I->haveInDatabase('filedrop_account',	[
	        'doi' => $doi,
	        'status' => FiledropAccount::STATUS_ACTIVE,
	    ]);

        $outcome = Yii::$app->createControllerByID('fuw')->run('remove-dropbox',[
            "doi" => $doi,
        ]);

        $I->assertEquals(Exitcode::OK, $outcome);

    }
}