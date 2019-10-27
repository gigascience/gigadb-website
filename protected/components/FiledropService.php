<?php

/**
 * Service to manage the creation and housekeeping of Filedrop accounts
 * used for author to upload files related to submitted manuscript
 *
 *
 * @property \TokenService $tokenSrv we need the service of JWT token generation
 * @property \GuzzleHttp\Client $webClient the web agent for making REST call
 * @property \User $requester the logged in user
 * @property string $identifier DOI of the dataset for which to create a filedrop account
 * @property string $instructions text to sent authors for uploading data
 * @property DatasetDAO $dataset Instance of DatasetDAO for working with dataset resultsets
 * @property boolean $dryRunMode whether or not to simulate final resource changes
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FiledropService extends yii\base\Component
{
	/**
 	 * {@inheritdoc}
   	 */
	public $tokenSrv;
	/**
 	 * {@inheritdoc}
   	 */
	public $webClient;
	/**
 	 * {@inheritdoc}
   	 */
	public $requester;
	/**
 	 * {@inheritdoc}
   	 */
	public $identifier;
	/**
 	 * {@inheritdoc}
   	 */
	public $instructions;
	/**
 	 * {@inheritdoc}
   	 */
	public $dataset;
	/**
 	 * {@inheritdoc}
   	 */
	public $dryRunMode;

	/**
	 * Make HTTP POST to File Upload Wizard to create Filedrop account
	 *
	 * @return bool whether the call has been made and succeed or not
	 */
	public function createAccount(): bool
	{
		$api_endpoint = "http://fuw-admin-api/filedrop-accounts";

		if ("admin" !== $this->requester->role) {
			Yii::log("The requesting user doesn't have admin role","error");
			return false;
		}
		// 'postID=:postID', array(':postID'=>10)
		$dataset = Dataset::model()->find('identifier=:doi',[":doi" => $this->identifier]) ;
		if ( !isset($dataset) || "AssigningFTPbox" !== $dataset->upload_status ) {
			Yii::log("Upload status required for DOI {$this->identifier}: AssigningFTPbox", "error");
			Yii::log("Gotten: {$dataset->upload_status}","error");
			return false;
		}
		$token = $this->tokenSrv->generateTokenForUser($this->requester->email);

		try {
			$response = $this->webClient->request('POST', $api_endpoint, [
								    'headers' => [
								        'Authorization' => "Bearer $token",
								    ],
								    'form_params' => [
								        'doi' => $this->identifier,//TODO:check it's right status
								        'dryRunMode' => $this->dryRunMode,
								    ],
								    'connect_timeout' => 5,
								]);
			if (201 === $response->getStatusCode() ) {
				$this->dataset->transitionStatus("AssigningFTPbox","UserUploadingData", $this->instructions);
				return true;
			}
		}
		catch(RequestException $e) {
			Yii::log( Psr7\str($e->getRequest()) , "error");
		    if ($e->hasResponse()) {
		        Yii::log( Psr7\str($e->getResponse()), "error");
		    }
		}
		return false;
	}
}
?>