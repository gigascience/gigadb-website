<?php
/**
 * Data Transfer Object (DTO) between GigaDB and File Upload Wizard for account management
 * Get info for uploading dataset files to a filedrop account and generating email instructions
 *
 * @param int $account_id internal id of filedrop_account associated with a dataset
 * @param FiledropService $filedrop instance of filedrop account service client to the FUW API
 * @param array $config config for ftp endpoint and twig templates (set in params-local.php.dist)
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class DatasetUpload extends yii\base\BaseObject
{
	private $_id;
	private $_filedrop;
	private $_config;

	// public function __construct (array $filedropAccount, array $ftpConfig, DatasetDAO $datasetDAO)
	public function __construct (int $account_id, FiledropService $filedrop, array $config)
	{
		$this->_id = $account_id;
		$this->_filedrop = $filedrop;
		$this->_config = $config;
	}

	/**
	 * Connect to File Upload Wizard API to retrieve properties of a filedrop account
	 *
	 * @return array associative array of filedrop account properties
	 */
	public function getFiledropAccountDetails(): array
	{
		return $this->_filedrop->getAccount($this->_id);
	}

	/**
	 * Update the instructions in File Upload Wizard API
	 *
	 * @param string $newInstructions updated instructions
	 * @return bool whether operation succeeded or not
	 **/
	public function changeUploadInstructions(string $newInstructions): bool
	{
		return $this->_filedrop->saveInstructions($this->_id, $newInstructions);
	}

	/**
	 * Send the instructions by email through File Upload Wizard API
	 *
	 * @param string $recipient whom to send the email
	 * @param string $subject subject of the email
	 * @param string $instructions instructions to send
	 * @return bool whether operation succeeded or not
	 **/
	public function sendUploadInstructions(string $recipient, string $subject, string $instructions): bool
	{
		$response = $this->_filedrop->emailInstructions($this->_id, $recipient, $subject, $instructions);
		return isset($response) && is_array($response);
	}

	/**
	 * load default instructions from a twig template and interpolate in variables
	 * combined from multiple sources
	 *
	 * @param array $filedropAccount filedrop account properties
	 *
	 * {@inheritdoc}
	 *
	 */
	public function renderUploadInstructions(array $filedropAccount): string
	{
        // Retrieve info about the dataset
        $dataset_info = $this->_filedrop->dataset->getTitleAndStatus();

        $instructions = $filedropAccount['instructions']; //preferably use saved instructions
        if (!$instructions) { // otherwise create from template and interpolate properties
	        // prepare array of variables to be interpolated
	        $vars = array_merge($filedropAccount, $dataset_info, $this->_config);

	       	// create a template loader from specific directory in file system
	        $loader = new \Twig\Loader\FilesystemLoader(
		        	$this->_config['template_path']
		        );

	        // instantiate template environment object for rendering to be called upon
	        $twig = new \Twig\Environment($loader);

	        // render the email instructions from template
	        $instructions = $twig->render('emailInstructions.twig', $vars);
        }

		return $instructions;
	}
}

?>