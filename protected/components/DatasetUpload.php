<?php

use \PhpOffice\PhpSpreadsheet\Reader;
use \yii\helpers\FileHelper;

/**
 * Business object for integration between GigaDB and File Upload Wizard for file upload
 *
 * @param int $datasetDAO dataset DAO
 * @param FileUploadService $fileUpload instance of File  Upload service client to the FUW API
 * @param array $config config for sender and twig templates (set in params-local.php.dist)
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class DatasetUpload extends yii\base\BaseObject
{
	private $_datasetDAO;
	private $_fileUploadSrv;
	private $_config;
	/** @var string $_filetypesJSON list of file types supported by GigaDB, exported to JSON from DB*/
	private $_fileTypesJSON;
	/** @var string $_fileformatsJSON list of file formats supported by GigaDB, exported to JSON from DB*/
	private $_fileFormatsJSON;


	public function __construct (DatasetDAO $datasetDAO, FileUploadService $fileUploadSrv, array $config = [])
	{
		parent::__construct();
		$this->_datasetDAO = $datasetDAO;
		$this->_fileUploadSrv = $fileUploadSrv;
		$this->_config = $config;

		$this->_fileTypesJSON = file_get_contents("/var/www/files/data/filetypes.json");
		$this->_fileFormatsJSON = file_get_contents("/var/www/files/data/fileformats.json");
	}

	/** 
	 * Getter for _filetypesJSON
	 * @return array
	 */
	public function getFiletypesArray(): array
	{
		return json_decode($this->_fileTypesJSON, true);
	}

	/** 
	 * Getter for _filetypesJSON
	 * @return string
	 */
	public function getFiletypesJSON(): string
	{
		return $this->_fileTypesJSON;
	}


	/** 
	 * Getter for _fileformatsJSON
	 * @return array
	 */
	public function getFileformatsArray(): array
	{
		return json_decode($this->_fileFormatsJSON, true);
	}

	/** 
	 * Getter for _fileformatsJSON
	 * @return string
	 */
	public function getFileformatsJSON(): string
	{
		return $this->_fileFormatsJSON;
	}

	/**
	 * method to set Dataset Upload status to DataAvailableForReview and notify reviewers
	 *
	 * @param string $content email content of notification
	 * @return bool wether or not operation is successful
	 */
	public function setStatusToDataAvailableForReview(string $content): bool
	{
		$statusChanged = $this->_datasetDAO->transitionStatus("UserUploadingData","DataAvailableForReview") || $this->_datasetDAO->transitionStatus("DataPending","DataAvailableForReview");
		if ($statusChanged) {
			$emailSent = $this->_fileUploadSrv->emailSend(
				$this->_config["sender"], 
				$this->_config["recipient"], 
				"Data available for review", 
				$content
			);
            CurationLog::createlog("DataAvailableForReview", $this->_datasetDAO->getId());
			return $statusChanged && $emailSent;
		}
		return false;
	}

	/**
	 * method to set Dataset Upload status to Submitted,  notify curators and add curation log
	 *
	 * @param string $content email content of notification
	 * @return bool wether or not operation is successful
	 */
	public function setStatusToSubmitted(string $content): bool
	{
		$statusChanged = $this->_datasetDAO->transitionStatus("DataAvailableForReview","Submitted");
		if ($statusChanged) {
			Yii::log("Status changed to Submitted",'info');
			$emailSent = $this->_fileUploadSrv->emailSend(
				$this->_config["sender"], 
				$this->_config["curators_email"], 
				"Dataset has been submitted", 
				$content
			);
			return $emailSent;
		}
		Yii::log("Failed to change status to Submitted",'error');
		return $statusChanged;
	}

	/**
	 * method to set Dataset Upload status to DataPwnding and notify curators
	 *
	 * @param string $content email content of notification
	 * @param string $authorEmail email of the author
	 * @return bool wether or not operation is successful
	 */
	public function setStatusToDataPending(string $content, string $authorEmail): bool
	{
		$statusChanged = $this->_datasetDAO->transitionStatus("Submitted","DataPending");
		if ($statusChanged) {
			Yii::log("Status changed to DataPending",'info');
			$emailSent = $this->_fileUploadSrv->emailSend(
				$this->_config["sender"], 
				$authorEmail, 
				"Dataset has been set to DataPending", 
				$content
			);
			return $emailSent;
		}
		Yii::log("Failed to change status to DataPending",'error');
		return $statusChanged;
	}

	/**
	 * method to render the email content to be sent when dataset status change
	 *
	 * @param string $targetStatus status we are changing dataset upload status to
	 * @return string renderered content
	 */
	public function renderNotificationEmailBody(string $targetStatus): string
	{
			$vars = [
				"identifier" => $this->_datasetDAO->getIdentifier()
			];
			// create a template loader from specific directory in file system
	        $loader = new \Twig\Loader\FilesystemLoader(
		        	$this->_config['template_path']
		        );

	        // instantiate template environment object for rendering to be called upon
	        $twig = new \Twig\Environment($loader);

	        // render the email instructions from template
	        return $twig->render("$targetStatus.twig", $vars);
	}

	/**
	 * method to parse a spreadsheet of metadata for uploaded files
	 *
	 * @param string $inputFile path to the spreadsheet
	 * @return array array of arrays (metadata and errors)
	 */
	public function parseFromSpreadsheet(string $inputFile): array
	{
		$errors = [];
		$metadata = [];
		$columns = [
			'File Name' => 'name',
			'Data Type' => 'datatype',
			'File Format' => 'extension',
			'Description' => 'description',
			'Sample IDs' => 'sample_ids',
			'Attribute 1' => 'attr1',
			'Attribute 2' => 'attr2',
			'Attribute 3' => 'attr3',
			'Attribute 4' => 'attr4',
			'Attribute 5' => 'attr5',
		];

		// Determine the mime type from extension and check it's supported 
		$mimeType = FileHelper::getMimeTypeByExtension($inputFile);
		$extension = FileHelper::getExtensionsByMimeType($mimeType);
		if(!in_array($mimeType, $this->_config['spreadsheet_supported_format'])) {
			$errors[] = "Unsupported format of spreadsheet: $mimeType ($inputFile)" ;
			return [[],$errors];
		}
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Csv");
		if("text/tab-separated-values" === $mimeType) {
			$reader->setDelimiter("\t");
		}

		/**  Load $filepath to a Spreadsheet Object  **/
		$spreadsheet = $reader->load($inputFile);
		$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
		$headers = array_shift($sheetData);
		$diff = array_diff(array_keys($columns), array_map('trim',$headers));
		$metadata = [] ;
		if (isset($diff) && count($diff) > 0 ) {
			$errors[] = "Could not load spreadsheet, missing column(s): ".implode(",", $diff);
			return [$metadata, $errors];
		}
		foreach($sheetData as $row) {
			$metadata[] = array_combine($columns, $row);
		}
		return [$metadata, $errors];
	}

	/**
	 * Method to merge metadata from spreadsheet into the existing data
	 * 
	 * it uses the filename as commonality. it separate uploads and attributes
	 * as in the database they go in two different tables
	 *
	 * @param string $storedUploads stored uploads metadata with IDs
	 * @param string $sheetData metadata loaded from spreadsheet
	 * @return array array of arrays (changed uploads data, attributes and errors)
	 */
	public function mergeMetadata(array $storedUploads, array $sheetData): array
	{
		$changedUploads = [];
		$newAttributes = [] ;
		$errors = [] ;

		foreach($storedUploads as $upload) {
			$dataPos = array_search( $upload['name'], array_column($sheetData, 'name') );
			if ($dataPos !== false) { // if filename matches

				// checking the data type column is correct, if not add error and continue
				if(!in_array(trim($sheetData[$dataPos]['datatype']), array_keys($this->getFiletypesArray()))) {
					$errors[] = "(".$upload['name'].") "."Cannot load file, incorrect Data type: ".trim($sheetData[$dataPos]['datatype']);
					continue;
				}

				// checking the file format column is correct, if not add error and continue
				if(!in_array(strtoupper(trim($sheetData[$dataPos]['extension'])), array_keys($this->getFileformatsArray()))) {
					$errors[] = "(".$upload['name'].") "."Cannot load file, incorrect File format: ".trim($sheetData[$dataPos]['extension']);
					continue;
				}

				// checking and converting sample ids
				if($sheetData[$dataPos]['sample_ids']) {
					$newSamples = explode(";",$sheetData[$dataPos]['sample_ids']);
					$oldSamples = $upload['sample_ids'] ? explode(",",$upload['sample_ids']) : [];
					$allSamples = array_merge($oldSamples,$newSamples);
					$sheetData[$dataPos]['sample_ids'] = implode(", ", array_map('trim',$allSamples));

				}


				// merging sheetData into the stored upload data
				$changedUploads[$upload['id']] = array_merge(
									$upload, 
									array_map('trim', array_slice($sheetData[$dataPos],0,5))
								);

				// merging attributes
				$tempAttr = [];
				foreach (range(1, 5) as $number) {
					if ( isset($sheetData[$dataPos]['attr'.$number])
						&& $sheetData[$dataPos]['attr'.$number] !== ''
					) {
						$matches = preg_split("/::/", trim($sheetData[$dataPos]['attr'.$number]));
						if (isset($matches) && is_array($matches) && count($matches) === 3) {
							$tempAttr[$number-1] = [ "upload_id" => $upload['id'] ];
							list($tempAttr[$number-1]['name'], 
								$tempAttr[$number-1]['value'], 
								$tempAttr[$number-1]['unit']
							) = $matches;
						}
						else {
							$errors[] = "(".$upload['name'].") "."Malformed attribute: ".trim($sheetData[$dataPos]['attr'.$number]);
						}
					}			    
				}
				if(!empty($tempAttr))
					$newAttributes = array_merge($newAttributes, $tempAttr);
			}
		}
		return [$changedUploads, $newAttributes, $errors];
	}

}

?>