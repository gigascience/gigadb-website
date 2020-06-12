<?php 

namespace console\tests;

use console\tests\FunctionalTester;
use Yii;
use console\controllers\TusdController;
use common\models\Upload;
use backend\models\FiledropAccount;
use backend\fixtures\FiledropAccountFixture;
use yii\console\ExitCode;

class TusdCest
{
public function _before(FunctionalTester $I)
    {
        Yii::$app->fs->write("8cd11d9b349dbf7d4539d25a2af03fe2.bin","foobar");
        Yii::$app->fs->write("8cd11d9b349dbf7d4539d25a2af03fe2.info",
        	file_get_contents(codecept_data_dir()."tusd.info")
        );
    }

    public function _after(FunctionalTester $I)
    {
        if(Yii::$app->fs->has("8cd11d9b349dbf7d4539d25a2af03fe2.bin"))
            Yii::$app->fs->delete("8cd11d9b349dbf7d4539d25a2af03fe2.bin");
        if(Yii::$app->fs->has("8cd11d9b349dbf7d4539d25a2af03fe2.info"))
            Yii::$app->fs->delete("8cd11d9b349dbf7d4539d25a2af03fe2.info");

        if(Yii::$app->fs->has("file_repo/300001/seq1.fa"))
            Yii::$app->fs->delete("file_repo/300001/seq1.fa");

        if(Yii::$app->fs->has("file_repo/300001/meta/seq1.fa.info.json"))
            Yii::$app->fs->delete("file_repo/300001/meta/seq1.fa.info.json");                 
    }

    // tests
    public function tryWithSuccessToCreateUploadForFile(FunctionalTester $I)
    {
    	$doi = "300001";

    	$tusdFileManifest = file_get_contents(codecept_data_dir()."tusd.info");

    	$accountId = $I->haveInDatabase('filedrop_account',	[
	        'doi' => $doi,
	        'status' => FiledropAccount::STATUS_ACTIVE,
	    ]);

    	$outcome = Yii::$app->createControllerByID('tusd')->run('process-upload',[
    		"doi" => $doi, 
    		"json" => $tusdFileManifest,
    		"datafeed_path" => "/app/console/tests/_data",
    		"token_path" => "/app/console/tests/_data",
    		"file_inbox" => codecept_output_dir(),
    		"file_repo" => codecept_output_dir()."file_repo",
    	]);

    	$I->assertEquals(Exitcode::OK, $outcome);

		$I->assertEquals(1, Upload::find([
    		"doi" => $doi,
    		"name" => "seq1.fa", 
    		"status" => Upload::STATUS_UPLOADING, 
    		"extension" =>"FASTA",
    		"size" => "117",
    		"initial_md5" => "58e51b8d263ca3e89712c65c4485a8c9",
    		"filedrop_account_id" => $accountId,
    	])->count());

        $I->seeFileFound("seq1.fa", codecept_output_dir()."file_repo/$doi");
        $I->seeFileFound("seq1.fa.info.json", codecept_output_dir()."file_repo/$doi/meta");
        $I->dontSeeFileFound("8cd11d9b349dbf7d4539d25a2af03fe2.bin", codecept_output_dir());
        $I->dontSeeFileFound("8cd11d9b349dbf7d4539d25a2af03fe2.info", codecept_output_dir());    	

    }

    public function tryCreateUploadForFileFromJSONFile(FunctionalTester $I)
    {
    	$doi = "300001";

    	$accountId = $I->haveInDatabase('filedrop_account',	[
	        'doi' => $doi,
	        'status' => FiledropAccount::STATUS_ACTIVE,
	    ]);

    	$outcome = Yii::$app->createControllerByID('tusd')->run('process-upload',[
    		"doi" => $doi, 
    		"jsonfile" => codecept_data_dir()."tusd.info",
    		"datafeed_path" => "/app/console/tests/_data",
    		"token_path" => "/app/console/tests/_data",
    		"file_inbox" => codecept_output_dir(),
    		"file_repo" => codecept_output_dir()."file_repo",    		
    	]);

    	$I->assertEquals(Exitcode::OK, $outcome);

		$I->assertEquals(1, Upload::find([
    		"doi" => $doi,
    		"name" => "seq1.fa", 
    		"status" => Upload::STATUS_UPLOADING, 
    		"extension" =>"FASTA",
    		"size" => "117",
    		"initial_md5" => "58e51b8d263ca3e89712c65c4485a8c9",
    		"filedrop_account_id" => $accountId,
    	])->count());

        $I->seeFileFound("seq1.fa", codecept_output_dir()."file_repo/$doi");
        $I->seeFileFound("seq1.fa.info.json", codecept_output_dir()."file_repo/$doi/meta");
        $I->dontSeeFileFound("8cd11d9b349dbf7d4539d25a2af03fe2.bin", codecept_output_dir());
        $I->dontSeeFileFound("8cd11d9b349dbf7d4539d25a2af03fe2.info", codecept_output_dir());      	  	
    }

    public function tryWithDefaultOptions(FunctionalTester $I)
    {
    	$controller = Yii::$app->createControllerByID('tusd');
    	$I->assertEquals("/var/www/files/data/", $controller->datafeed_path);
    	$I->assertEquals("/var/private", $controller->token_path);
    	$I->assertEquals("/var/inbox", $controller->file_inbox);
    	$I->assertEquals("/var/repo", $controller->file_repo);
    	$I->assertNull($controller->doi);
    	$I->assertNull($controller->json);

    }
}
