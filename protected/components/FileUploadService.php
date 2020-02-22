<?php

/**
 * Service to be a REST client library to File Upload Wizard's public API (frontend)
 * 
 *
 *
 * @property \TokenService $tokenSrv we need the service of JWT token generation
 * @property \GuzzleHttp\Client $webClient the web agent for making REST call
 * @property \User $requester the logged in user
 * @property string $identifier DOI of the dataset for which to create a filedrop account
 * @property string $instructions text to sent authors for uploading data
 * @property DatasetDAO $dataset Instance of DatasetDAO for working with dataset resultsets
 * @property boolean $dryRunMode whether or not to simulate final resource changes
 * @property \Lcobucci\JWT\Token $token generated for multiple call to the api per session
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */

use GuzzleHttp\Middleware;

class FileUploadService extends yii\base\Component
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
 	 * {@inheritdoc}
   	 */
	public $token;

	
	/**
	 * Make HTTP PUT to File Upload Wizard to update an upload
	 *
	 * @param int $uploadId Id of the upload to update
	 * @param array $postData array of values to update the uploads's attribute with
	 *
	 * @return bool whether or not the update was succesful
	 */
	public function emailSend(string $sender, string $recipient, string $subject, string $content): bool
	{

		// construct the parameters to send to the API in the body of the POST request
		$emailParams = array_combine(["sender","recipient","subject", "content"], func_get_args());

		// Grab the client's handler instance.
		$clientHandler = $this->webClient->getConfig('handler');
		// Create a middleware that echoes parts of the request.
		$tapMiddleware = Middleware::tap(function ($request) {
		    Yii::log( $request->getHeaderLine('Content-Type') , 'info');
		    // application/json
		    Yii::log( $request->getBody(), 'info');
		    // {"foo":"bar"}
		});

		$api_endpoint = "http://fuw-public-api/notifications/emailSend";

		// reuse token to avoid "You must unsign before making changes" error
		// when multiple API calls in same session
		$this->token = $this->token ?? $this->tokenSrv->generateTokenForUser($this->requester->email);
		try {
			$response = $this->webClient->request('POST', $api_endpoint, [
								    'headers' => [
								        'Authorization' => "Bearer ".$this->token,
								    ],
								    'form_params' => $emailParams,
								    'connect_timeout' => 5,
								    'handler' => $tapMiddleware($clientHandler),
								]);
			if (200 === $response->getStatusCode() ) {
				// Yii::log($response->getBody(),'info');
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

	/**
	 * Make HTTP GET to File Upload Wizard to retrieve files uploads
	 *
	 * @param string $doi DOI of the files to return
	 *
	 * @return array||null return an array of uploads or null if not found
	 */
	public function getUploads(string $doi): ?array
	{
		$api_endpoint = "http://fuw-public-api/uploads";

		// reuse token to avoid "You must unsign before making changes" error
		// when multiple API calls in same session
		$this->token = $this->token ?? $this->tokenSrv->generateTokenForUser($this->requester->email);

		try {
			$response = $this->webClient->request('GET', $api_endpoint, [
								    'headers' => [
								        'Authorization' => "Bearer ".$this->token,
								    ],
								    'query' => [ 'filter[doi]' => $doi,  'filter[status]' => 0],
								    'connect_timeout' => 5,
								]);
			if (200 === $response->getStatusCode() ) {
				// Yii::log($response->getBody(),'info');
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
	 * Make HTTP PUT to File Upload Wizard to update an upload
	 *
	 * @param int $uploadId Id of the upload to update
	 * @param array $postData array of values to update the uploads's attribute with
	 *
	 * @return bool whether or not the update was succesful
	 */
	public function updateUpload(int $uploadId, array $postData): bool
	{

		// Grab the client's handler instance.
		$clientHandler = $this->webClient->getConfig('handler');
		// Create a middleware that echoes parts of the request.
		$tapMiddleware = Middleware::tap(function ($request) {
		    Yii::log( $request->getHeaderLine('Content-Type') , 'info');
		    // application/json
		    Yii::log( $request->getBody(), 'info');
		    // {"foo":"bar"}
		});

		$api_endpoint = "http://fuw-public-api/uploads/$uploadId";

		// reuse token to avoid "You must unsign before making changes" error
		// when multiple API calls in same session
		$this->token = $this->token ?? $this->tokenSrv->generateTokenForUser($this->requester->email);
		// Yii::log(print_r($postData,true),'info');
		try {
			$response = $this->webClient->request('PUT', $api_endpoint, [
								    'headers' => [
								        'Authorization' => "Bearer ".$this->token,
								    ],
								    'form_params' => $postData,
								    'connect_timeout' => 5,
								    'handler' => $tapMiddleware($clientHandler),
								]);
			if (200 === $response->getStatusCode() ) {
				// Yii::log($response->getBody(),'info');
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

	/**
	 * Make HTTP PUT to File Upload Wizard to archive an upload
	 *
	 * @param array $uploadIds Ids of the upload to delete
	 *
	 * @return array ids of archived uploads
	 */
	public function deleteUploads(array $uploadIds): array
	{
		// Grab the client's handler instance.
		$clientHandler = $this->webClient->getConfig('handler');
		// Create a middleware that echoes parts of the request.
		$tapMiddleware = Middleware::tap(function ($request) {
		    Yii::log( $request->getHeaderLine('Content-Type') , 'info');
		    // application/json
		    Yii::log( $request->getBody(), 'info');
		    // {"foo":"bar"}
		});

		$api_endpoint = "http://fuw-public-api/uploads/";

		// reuse token to avoid "You must unsign before making changes" error
		// when multiple API calls in same session
		$this->token = $this->token ?? $this->tokenSrv->generateTokenForUser($this->requester->email);
		// Yii::log(print_r($postData,true),'info');
		$responses = [];
		$postData = null;
		foreach($uploadIds as $uploadId) {
			//prepare post data
			$postData = ["status" => 2];
			try {
				$response = $this->webClient->request('PUT', $api_endpoint."$uploadId", [
									    'headers' => [
									        'Authorization' => "Bearer ".$this->token,
									    ],
									    'form_params' => $postData,
									    'connect_timeout' => 5,
									    'handler' => $tapMiddleware($clientHandler),
									]);
				if (200 === $response->getStatusCode() ) {
					$responses[] =  json_decode($response->getBody(), true);
				}
			}
			catch(RequestException $e) {
				Yii::log( Psr7\str($e->getRequest()) , "error");
			    if ($e->hasResponse()) {
			        Yii::log( Psr7\str($e->getResponse()), "error");
			    }
			}
		}
		return $responses;
	}

	/**
	 * Make HTTP GET to File Upload Wizard to retrieve attributes
	 *
	 * @param int $uploadId id of the file upload
	 *
	 * @return array||null return an array of attributes or null if not found
	 */
	public function getAttributes(int $uploadId): ?array
	{
		$api_endpoint = "http://fuw-public-api/attributes";

		// reuse token to avoid "You must unsign before making changes" error
		// when multiple API calls in same session
		$this->token = $this->token ?? $this->tokenSrv->generateTokenForUser($this->requester->email);

		try {
			$response = $this->webClient->request('GET', $api_endpoint, [
								    'headers' => [
								        'Authorization' => "Bearer ".$this->token,
								    ],
								    'query' => [ 'filter[upload_id]' => $uploadId ],
								    'connect_timeout' => 5,
								]);
			if (200 === $response->getStatusCode() ) {
				// Yii::log($response->getBody(),'info');
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
	 * Make HTTP POST to File Upload Wizard to set attributes
	 *
	 * approach: delete recorded attributes and add new ones (if any)
	 *
	 * @param int $uploadId Id of the upload to update
	 * @param array $postData array of values to update the uploads's attribute with
	 *
	 * @return bool whether or not the update was succesful
	 */
	public function setAttributes(int $uploadId, array $postData): bool
	{

		// Grab the client's handler instance.
		$clientHandler = $this->webClient->getConfig('handler');
		// Create a middleware that echoes parts of the request.
		$tapMiddleware = Middleware::tap(function ($request) {
		    Yii::log( $request->getHeaderLine('Content-Type') , 'info');
		    // application/json
		    Yii::log( $request->getBody(), 'info');
		    // {"foo":"bar"}
		});

		$api_endpoint = "http://fuw-public-api/attributes/replace_for/$uploadId";

		// reuse token to avoid "You must unsign before making changes" error
		// when multiple API calls in same session
		$this->token = $this->token ?? $this->tokenSrv->generateTokenForUser($this->requester->email);
		// Yii::log(print_r($postData,true),'info');
		try {
			$response = $this->webClient->request('POST', $api_endpoint, [
								    'headers' => [
								        'Authorization' => "Bearer ".$this->token,
								    ],
								    'form_params' => $postData,
								    'connect_timeout' => 5,
								    'handler' => $tapMiddleware($clientHandler),
								]);
			if (200 === $response->getStatusCode() ) {
				// Yii::log($response->getBody(),'info');
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