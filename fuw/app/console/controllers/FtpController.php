<?php

namespace console\controllers;

use Yii;
use \yii\helpers\Console;
use \yii\console\Controller;
use common\models\Upload;
use backend\models\FiledropAccount;
use console\models\UploadFactory;
use yii\console\ExitCode;

/**
 * Console controller for the watcher container service to load metadata for ftp upload
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FtpController extends Controller
{
	/**
	 * @var string $dataset_dir path to directory where a dataset files have been uploaded. last component of path must be a DOI */
	public $dataset_dir;

	/**
	 * @var string $file_repo filedropbox repository for dataset files to be reviewed */
	public $file_repo = "/var/repo";


	/**
	 * @var string $token_path path to download token file */
	public $datafeed_path = "/var/www/files/data/" ;

	/**
	 * @var console\models\UploadFactory $uploadFactory class to create Upload models */
	public $uploadFactory;

	/**
	 * @var string $token_path path to download token file */
	public $token_path = "/var/private" ;

	/**
	* @var backend\models\FiledropAccount $dropboxAccount DropBox account for the upload */
	private $dropboxAccount;

	/**
	* @var string $datafeedPath path to the data feeds */
	private $datafeedPath = "/var/www/files/data/";

	/**
     * Command to create an Upload record from Tusd metadata json file
     * Usage:
     * "yii ftp/process-upload --dataset_dir <directory containing uploaded files for a DOI>
	 *
     * @uses \common\models\Upload
     * @uses \common\models\FiledropAccount
    */
	 public function actionProcessUpload()
	 {

	 	if(!($this->dataset_dir)) {
	 		Yii::error("wrong number of arguments, exiting abnormally");
	 		$this->stdout("wrong number of arguments, exiting abnormally".PHP_EOL, Console::FG_RED);
	 		return ExitCode::USAGE;
	 	}
	 	$dirComponents = explode("/",$this->dataset_dir) ;
	 	$doi = $dirComponents[count($dirComponents)-1];
		$this->stdout("ftp: DOI $doi extracted...".PHP_EOL, Console::FG_GREEN);
 		$this->dropboxAccount = FiledropAccount::findOne([ "doi" => $doi]);
 		if(!$this->dropboxAccount) {
 			$this->stdout("Filedrop account not found for DOI $doi, exiting abnormally".PHP_EOL, Console::FG_RED);
 			Yii::error("Filedrop account not found for DOI $doi, exiting abnormally");
 			return ExitCode::DATAERR;
 		}

	 	$this->uploadFactory = $this->uploadFactory ?? new UploadFactory($doi, $this->datafeedPath, $this->token_path);

 		$directoryHandle = opendir($this->dataset_dir);
 		$someSuccess = false;
		while (($file = readdir($directoryHandle)) !== false) {
			if ($file === '.' || $file === '..') {
				continue;
			}
			if ( true == is_dir($this->dataset_dir."/$file") ) {
				continue;
			}

			$fileArray = ["doi" => $doi, "path" => $this->dataset_dir, "name" => $file];
			$saved = $this->uploadFactory->createUploadFromFile(
						$this->dropboxAccount->id,
						$fileArray
					);

			$copied = Yii::$app->fs->copy(
				str_replace(Yii::$app->fs->path, "", $this->dataset_dir."/$file"), 
				str_replace(Yii::$app->fs->path,"", $this->file_repo."/$doi/$file")
			);

			$deleted = $copied && Yii::$app->fs->delete(
				str_replace(Yii::$app->fs->path, "", $this->dataset_dir."/$file")
			);

			if( !$saved || !$copied || !$deleted) {
				$this->stdout("Error while processing upload for: ".$this->dataset_dir."/$file".PHP_EOL, Console::FG_RED);
				Yii::error("Error while processing upload for: ".$this->dataset_dir."/$file");
			}
			else {
				$someSuccess = true;
			}
		}
		closedir($directoryHandle);

		if( $someSuccess ) {
		 	return ExitCode::OK;
		}
		return ExitCode::CANTCREAT;
	 }

	public function options($actionID)
    {
        // $actionId might be used in subclasses to provide options specific to action id
        return ['color', 'interactive', 'help','dataset_dir','file_repo', 'datafeed_path','token_path'];
    }

}