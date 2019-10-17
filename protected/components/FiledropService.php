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
	public $dryRunMode;

	/**
	 * Will make an HTTP call to File Upload Wizard to trigger creation of Filedrop account
	 *
	 */
	public function createAccount()
	{
		$api_endpoint = "http://fuw-admin-api/filedrop-accounts";

		$token = $tokenSrv->generateTokenForUser($requester->email); //TODO:check it's an admin

		try {
			$response = $this->webClient->request('POST', $api_endpoint, [
								    'headers' => [
								        'Authorization' => "Bearer $jwt_token",
								    ],
								    'form_params' => [
								        'doi' => $this->identifier,//TODO:check it's right status
								        'dryRunMode' => true,
								    ],
								    'connect_timeout' => 5,
								]);
		}
		catch(RequestException $e) {
			Yii::log( Psr7\str($e->getRequest()) , "error");
		    if ($e->hasResponse()) {
		        Yii::log( Psr7\str($e->getResponse()), "error");
		    }
		}
	}
}
?>