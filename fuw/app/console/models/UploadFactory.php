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

		$ftpd_endpoint = Yii::$app->params['dataset_filedrop']["ftpd_endpoint"] ?? "localhost";
		$ftp_link = "ftp://downloader-{$this->doi}:$downloader_token@$ftpd_endpoint:9021/$fileName";
		return $ftp_link;
	}

	/**
	 * Construct and save the upload model from supplied metadata array
	 *
	 * @param object $upload 
	 * @param array $metadata
	 * @param int $filedropAccountId
	 * @return bool whether or not the upload was successfully saved
	 */
	public function createUpload(object $upload, array $metadata, int $filedropAccountId): bool
	{
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
}