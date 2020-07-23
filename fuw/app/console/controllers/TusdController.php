<?php

namespace console\controllers;

use Yii;
use \yii\helpers\Console;
use \yii\console\Controller;
use common\models\Upload;
use console\models\UploadFactory;
use yii\console\ExitCode;

/**
 * Console controller that runs on the Tusd container service to load file metadata from file
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class TusdController extends Controller
{

	/**
	 * @var string $doi Identifier of the dataset associated to the file in the wizard by the author */
	public $doi;

	/**
	 * @var string $json json structure with Tusd file metadata */
	public $json;

	/**
	 * @var string $jsonfile Tusd file metadata */
	public $jsonfile;

	/**
	 * @var string $token_path path to download token file */
	public $datafeed_path = "/var/www/files/data/" ;

	/**
	 * @var string $token_path path to download token file */
	public $token_path = "/var/private" ;

	/**
	 * @var string $file_inbox directory where Tusd save files */
	public $file_inbox = "/var/inbox";
	
	/**
	 * @var string $file_repo filedropbox repository for dataset files to be reviewed */
	public $file_repo = "/var/repo";

	/**
     * Command to create an Upload record from Tusd metadata json file
     * Usage:
     * "yii tusd/process-upload --doi <DOI associated to the file> --json <Tusd manifest JSON string>"
     * or:
     * "yii tusd/process-upload --doi <DOI associated to the file> --jsonfile <Tusd manifest JSON file>"
     * @uses \common\models\Upload
    */
	 public function actionProcessUpload()
	 {
	 	$this->stdout("actionUpload begins...\n", Console::BOLD);

	 	if(!($this->doi && ($this->json || $this->jsonfile))) {
	 		Yii::error("wrong number of arguments, exiting abnormally");
	 		return ExitCode::USAGE;
	 	}

 		$account = (new \yii\db\Query())
		    ->from('filedrop_account')
		    ->where(['doi' => $this->doi])
		    ->one();
 		if(!$account) {
 			Yii::error("Filedrop account not found for DOI {$this->doi}, exiting abnormally");
 			return ExitCode::DATAERR;
 		}
 		
 		if($this->jsonfile) {
 			$this->json = file_get_contents($this->jsonfile);
 		}

		$datafeedPath = "/var/www/files/data/";
		$factory = new UploadFactory($this->doi, $datafeedPath, $this->token_path);
		$result = $factory->createUploadFromJSON(
					$account['id'],
					$this->json
				);

		$moved = $this->moveFiles();

 		if( $result && $moved ) {
		 	return ExitCode::OK;
		}
		return ExitCode::CANTCREAT;

	 }


	/**
	 * Move the file to the filedrop box directory
	 *
	 * @return bool
	 */
	public function moveFiles(): bool
	{
		$metadata = json_decode($this->json, true);
		$copiedFile = Yii::$app->fs->copy(
			str_replace(Yii::$app->fs->path, "",$this->file_inbox."/".$metadata["ID"].".bin"), 
			str_replace(Yii::$app->fs->path,"",$this->file_repo."/".$this->doi."/".$metadata["MetaData"]["filename"])
			);
		$copiedMeta = Yii::$app->fs->copy(
			str_replace(Yii::$app->fs->path, "",$this->file_inbox."/".$metadata["ID"].".info"), 
			str_replace(Yii::$app->fs->path,"",$this->file_repo."/".$this->doi."/meta/".$metadata["MetaData"]["filename"].".info.json")
			);
		$deletedFile = $copiedFile && Yii::$app->fs->delete(
			str_replace(Yii::$app->fs->path, "",$this->file_inbox."/".$metadata["ID"].".bin")
		);
		$deletedMeta = $copiedMeta && Yii::$app->fs->delete(
			str_replace(Yii::$app->fs->path, "",$this->file_inbox."/".$metadata["ID"].".info")
		);		

		return $copiedFile && $copiedMeta && $deletedFile && $deletedMeta;
	}


	public function options($actionID)
    {
        // $actionId might be used in subclasses to provide options specific to action id
        return ['color', 'interactive', 'help','doi', 'json','jsonfile', 'datafeed_path','token_path','file_inbox', 'file_repo'];
    }

}