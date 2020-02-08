<?php
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

}

?>