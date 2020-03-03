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


	public function __construct (DatasetDAO $datasetDAO, FileUploadService $fileUploadSrv, array $config = [])
	{
		$this->_datasetDAO = $datasetDAO;
		$this->_fileUploadSrv = $fileUploadSrv;
		$this->_config = $config;
	}

	/**
	 * method to set Dataset Upload status to DataAvailableForReview and notify reviewers
	 *
	 * @param string $content email content of notification
	 * @return bool wether or not operation is successful
	 */
	public function setStatusToDataAvailableForReview(string $content): bool
	{
		$statusChanged = $this->_datasetDAO->transitionStatus("UserUploadingData","DataAvailableForReview");
		if ($statusChanged) {
			$emailSent = $this->_fileUploadSrv->emailSend(
				$this->_config["sender"], 
				$this->_config["recipient"], 
				"Data available for review", 
				$content
			);
			return $statusChanged && $emailSent;
		}
		return false;
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
	 * @param string $inputType MIME type of the spreadsheet file
	 * @param string $inputFile path to the spreadsheet
	 * @return array array of arrays (metadata and errors)
	 */
	public function parseFromSpreadsheet(string $mimeType, string $inputFile): array
	{
		$errors = [];
		$metadata = [];
		$columns = [
			'File Name' => 'name',
			'Data Type' => 'datatype',
			'File Format' => 'extension',
			'Description' => 'description',
			'Sample ID' => 'sampleId',
			'Attribute 1' => 'attr1',
			'Attribute 2' => 'attr2',
			'Attribute 3' => 'attr3',
			'Attribute 4' => 'attr4',
			'Attribute 5' => 'attr5',
		];

		/** Determine the extension from the mime type **/
		$extension = FileHelper::getExtensionsByMimeType($mimeType);
		$inputType = ucfirst($extension[0]);
		/**  Create a new Reader of the type defined in $extension  **/
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputType);
		/**  Load $filepath to a Spreadsheet Object  **/
		$spreadsheet = $reader->load($inputFile);
		$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
		$headers = array_shift($sheetData);
		$metadata = [] ;
		foreach($sheetData as $row) {
			$metadata[] = array_combine($columns, $row);
		}
		return [$metadata, $errors];
	}

}

?>