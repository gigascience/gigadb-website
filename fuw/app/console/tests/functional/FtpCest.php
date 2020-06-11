<?php 

namespace console\tests;

use console\tests\FunctionalTester;
use Yii;
use console\controllers\TusdController;
use common\models\Upload;
use backend\models\FiledropAccount;
use backend\fixtures\FiledropAccountFixture;
use yii\console\ExitCode;

class FtpCest
{
    public function _before(FunctionalTester $I)
    {
    }

    // tests
    public function tryWithSuccessToCreateUploadForFile(FunctionalTester $I)
    {
    	$doi = "300001";

        $I->copyDir(codecept_data_dir()."ftp",codecept_output_dir());
        $I->copyDir(codecept_data_dir()."testrepo",codecept_output_dir());

    	$accountId = $I->haveInDatabase('filedrop_account',	[
	        'doi' => $doi,
	        'status' => FiledropAccount::STATUS_ACTIVE,
	    ]);

    	$outcome = Yii::$app->createControllerByID('ftp')->run('process-upload',[
    		"dataset_dir" => codecept_output_dir()."/$doi", 
            "file_repo" => codecept_output_dir()."/file_repo", 
    		"datafeed_path" => "/app/console/tests/_data",
    		"token_path" => "/app/console/tests/_data",
    	]);

    	$I->assertEquals(Exitcode::OK, $outcome);

        $expectedUploads = Upload::find([
            "doi" => $doi,
            "status" => Upload::STATUS_UPLOADING, 
            "extension" =>"FASTA",
            "filedrop_account_id" => $accountId,
        ])->all();

		$I->assertEquals(2, count($expectedUploads));

        $I->seeFileFound("seq1.fa", codecept_output_dir()."file_repo/$doi");
        $I->seeFileFound("seq2.fa", codecept_output_dir()."file_repo/$doi");
        $I->dontSeeFileFound("seq1.fa", codecept_output_dir()."$doi");
        $I->dontSeeFileFound("seq2.fa", codecept_output_dir()."$doi");

    }

    
    public function tryWithDefaultOptions(FunctionalTester $I)
    {
    	$controller = Yii::$app->createControllerByID('ftp');
    	$I->assertEquals("/var/www/files/data/", $controller->datafeed_path);
    	$I->assertEquals("/var/private", $controller->token_path);
        $I->assertEquals("/home/downloader",$controller->file_repo);
    	$I->assertNull($controller->dataset_dir);

    }
}
