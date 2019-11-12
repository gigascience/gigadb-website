<?php
/**
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
	 * load default instructions from a twig template and interpolate in variables
	 * combined from multiple sources
	 *
	 * {@inheritdoc}
	 *
	 */
	public function getDefaultUploadInstructions(): string
	{
		// Retrieve info about the filedrop account from FUW API
        $filedrop_account = $this->_filedrop->getAccount($this->_id);
        // Retrieve info about the dataset
        $dataset_info = $this->_filedrop->dataset->getTitleAndStatus();

        // prepare array of variables to be interpolated
        $vars = array_merge($filedrop_account, $dataset_info, $this->_config);

       	// create a template loader from specific directory in file system
        $loader = new \Twig\Loader\FilesystemLoader(
	        	$this->_config['template_path']
	        );

        // instantiate template environment object for rendering to be called upon
        $twig = new \Twig\Environment($loader);

        // render the email instructions from template
        $instructions = $twig->render('emailInstructions.twig', $vars);

		return $instructions;
	}
}

?>