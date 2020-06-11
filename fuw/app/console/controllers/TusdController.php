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
	* @var backend\models\FiledropAccount $dropboxAccount DropBox account for the upload */
	private $dropboxAccount;


	/**
     * Command to create an Upload record from Tusd metadata json file
     * Usage:
     * "yii tusd/upload --doi <DOI associated to the file> --json <Tusd manifest JSON string>"
     * or:
     * "yii tusd/upload --doi <DOI associated to the file> --jsonfile <Tusd manifest JSON file>"
     * @uses \common\models\Upload
     * @uses \common\models\FiledropAccount
    */
	 public function actionUpload()
	 {
	 	$this->stdout("actionUpload begins...\n", Console::BOLD);

	 	if(!($this->doi && ($this->json || $this->jsonfile))) {
	 		Yii::error("wrong number of arguments, exiting abnormally");
	 		return ExitCode::USAGE;
	 	}

 		$this->dropboxAccount = FiledropAccount::findOne([ "doi" => $this->doi]);
 		if(!$this->dropboxAccount) {
 			Yii::error("Filedrop account not found for DOI {$this->doi}, exiting abnormally");
 			return ExitCode::DATAERR;
 		}
 		
 		if($this->jsonfile) {
 			$this->json = file_get_contents($this->jsonfile);
 		}

		$datafeedPath = "/var/www/files/data/";
		$factory = new UploadFactory($this->doi, $datafeedPath, $this->token_path);
		$result = $factory->createUploadFromJSON(
					$this->dropboxAccount->id,
					$this->json
				);

 		if( $result ) {
		 	return ExitCode::OK;
		}
		return ExitCode::CANTCREAT;

	 }

	public function options($actionID)
    {
        // $actionId might be used in subclasses to provide options specific to action id
        return ['color', 'interactive', 'help','doi', 'json','jsonfile', 'datafeed_path','token_path'];
    }

}