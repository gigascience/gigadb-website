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
	 * @return array||null if successfully created, the filedrop account is returned as array, null otherwise
	 */
	public function createAccount(): ?array
	{
		$api_endpoint = "http://fuw-admin-api/filedrop-accounts";

		if ("admin" !== $this->requester->role) {
			Yii::log("The requesting user doesn't have admin role","error");
			return null;
		}
		// 'postID=:postID', array(':postID'=>10)
		$dataset = Dataset::model()->find('identifier=:doi',[":doi" => $this->identifier]) ;
		if ( !isset($dataset) || "AssigningFTPbox" !== $dataset->upload_status ) {
			Yii::log("Upload status required for DOI {$this->identifier}: AssigningFTPbox", "error");
			Yii::log("Gotten: {$dataset->upload_status}","error");
			return null;
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
				return json_decode($response->getBody(), true);
			}
		}
		catch(RequestException $e) {
			Yii::log( Psr7\str($e->getRequest()) , "error");
		    if ($e->hasResponse()) {
		        Yii::log( Psr7\str($e->getResponse()), "error");
		    }
		}
		return null;
	}

	/**
	 * Make HTTP PUT to File Upload Wizard to save and send email instructions
	 *
	 * @param int $filedrop_id internal id of a filedrop account to update
	 * @param string $subject subject to use for the email to be sent
	 * @param string $instructions email content
	 *
	 * @return bool whether the call has been made and succeed or not
	 */
	public function emailInstructions(int $filedrop_id, string $subject, string $instructions): bool
	{

		$api_endpoint = "http://fuw-admin-api/filedrop-accounts/$filedrop_id";

		$token = $this->tokenSrv->generateTokenForUser($this->requester->email);

		try {
			$response = $this->webClient->request('PUT', $api_endpoint, [
								    'headers' => [
								        'Authorization' => "Bearer $token",
								    ],
								    'form_params' => [
								        'doi' => $this->identifier,
								        'subject' => $subject,
								        'instructions' => $instructions,
								        'to' => $this->requester->email,
								        'send' => true,
								    ],
								    'connect_timeout' => 5,
								]);
			if (200 === $response->getStatusCode() ) {
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