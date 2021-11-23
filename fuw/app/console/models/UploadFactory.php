<?php
namespace console\models;

use common\models\Upload;
use Yii;

/**
 * Domain class with method to support Tusd metadata upload controller action
 *
 * @param $doi
 * @param $datafeed_path
 * @param $token_path
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class UploadFactory extends \yii\base\BaseObject
{

	const COMMON_EXTENSION_MAPPINGS = array(
					"TXT" => "TEXT",
					"MD" => "TEXT",
					"RDF" => "XML",
					"JPG" => "JPG",
					"JPEG" => "JPG",
					"FA" => "FASTA",
					"FQ" => "FASTQ",
				);
	
	/**
	 * @var string $token_path path to the ftp tokens */ 
	private $token_path;

	/**
	 * @var string $datafeed_path path to the file format feeds */ 
	private $datafeed_path;	

	/**
	 * @var string $doi DOI associated to file upload */ 
	private $doi;
	
	public function __construct(string $doi, string $datafeedPath, string $tokenPath)
	{
		$this->doi = $doi;
		$this->datafeed_path = $datafeedPath;
		$this->token_path = $tokenPath;
	}

	/**
	 * Determine file format based on file extension
	 *
	 * @param string $fileName
	 * @return string the file format
	 */
	function getFileFormatFromFile(string $fileName): string
	{
		$reference = json_decode(file_get_contents($this->datafeed_path."/fileformats.json"),true);

		$ext = strtoupper(pathinfo($fileName, PATHINFO_EXTENSION));
		if ( isset(self::COMMON_EXTENSION_MAPPINGS[$ext]) ) {
			$ext = self::COMMON_EXTENSION_MAPPINGS[$ext];
		}
		if (true == in_array($ext, array_keys($reference)) ) {
			return $ext;
		}
		return "UNKNOWN" ;
	}

	/**
	 * Construct the ftp link
	 *
	 * @param string $fileName the name of the file
	 * @return string the ftp url
	 */
	public function generateFTPLink(string $fileName): string
	{
		$handle = fopen($this->token_path."/{$this->doi}/downloader_token.txt", "r");
		$line = fgets($handle) ;
		if (true == $line) {
			$downloader_token = chop($line);
		}
		fclose($handle);

        $downloadBaseUrl = Yii::$app->params['dataset_filedrop']["download_base_url"] ?? "http://localhost";

		return "$downloadBaseUrl/filedrop/{$this->doi}/$fileName";
	}

	/**
	 * Construct and save the upload model from supplied metadata json
	 *
	 * @param int $filedropAccountId
	 * @param string $json
	 * @param object||null $upload An instance of upload, crewate here if not supplied
	 * @return bool whether or not the upload was successfully saved
	 */
	public function createUploadFromJSON(int $filedropAccountId, string $json, object $upload = null): bool
	{

		$metadata = json_decode($json, true);
 		if( $metadata === null || count($metadata) === 0) {
 			Yii::error("The JSON string is empty or not readable");
 			Yii::error($json);
 			return false;
 		}
		if($this->doi !== $metadata["MetaData"]["dataset"]) {
 			Yii::error("DOI mismatch, exiting abnormally");
 			return false;	 				
		}

		$upload = $upload ?? new Upload();
 		$upload->filedrop_account_id = $filedropAccountId;
 		$upload->status = Upload::STATUS_UPLOADING;
 		$upload->doi = $metadata["MetaData"]["dataset"];
 		$upload->name = $metadata["MetaData"]["filename"];
 		$upload->initial_md5 = $metadata["MetaData"]["checksum"];
 		$upload->size = $metadata["Size"];
 		$upload->extension = $this->getFileFormatFromFile(
					 			$metadata["MetaData"]["filename"]
					 		);
 		$upload->location = $this->generateFTPLink(
 			$metadata["MetaData"]["filename"],
 			$metadata["MetaData"]["dataset"]
 		);

 		$outcome = $upload->save();
 		if($outcome) {
 			return true;
 		}
 		Yii::error($upload->errors);
 		return false;
	}

	/**
	 * Construct and save the upload model from supplied path to file
	 *
	 * @param int $filedropAccountId
	 * @param array $file array (with keys 'doi', 'path' and 'name') with info about the file
	 * @param object||null $upload An instance of upload, crewate here if not supplied
	 * @return bool whether or not the upload was successfully saved
	 */
	public function createUploadFromFile(int $filedropAccountId, array $file, object $upload = null): bool
	{
		$upload = $upload ?? new Upload();
		$file_stats = stat("{$file['path']}/{$file['name']}");
 		$upload->filedrop_account_id = $filedropAccountId;
 		$upload->status = Upload::STATUS_UPLOADING;
 		$upload->doi = $file["doi"];
 		$upload->name = $file["name"];
 		$upload->size = $file_stats[7];
 		$upload->extension = $this->getFileFormatFromFile(
					 			$file["name"]
					 		);
 		$upload->location = $this->generateFTPLink(
 			$file["name"],
 			$file["doi"]
 		);

 		$outcome = $upload->save();
 		if($outcome) {
 			return true;
 		}
 		Yii::error($upload->errors);
 		return false;
	}	
}