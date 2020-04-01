<?php

/**
 * Service to manage JSON Web Token used to authenticate
 * to File Upload Wizard API
 *
 *
 * @property string $jwtTTL time validity for the JSON Web Tokens
 * @property \Lcobucci\JWT\Builder $jwtBuilder JSON Web Token builder library
 * @property UserDAO $users finders for acessing User data
 * @property DateTime $dt DateTime object for time calculation
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class TokenService extends yii\base\Component
{

	public $jwtTTL;
	public $jwtBuilder;
	public $jwtSigner;
	public $users;
	public $dt;

	/**
	 * Initializes the application component.
	 * This method overrides the parent implementation by setting default cache key prefix.
	 */
	public function init()
	{
		parent::init();

	}

	/**
	 * Generate JWT token for a user
	 *
	 * @param string $email the email of the user to lookup and generate a token for
	 * @return \Lcobucci\JWT\Token the signed token
	 */
	public function generateTokenForUser(string $email): \Lcobucci\JWT\Token
	{
		$user = $this->users->findByEmail($email);
		$signer = $this->jwtSigner;
		$issuedTime = $this->dt->format('U');
		$notBeforeTime = $issuedTime ;
		$expirationTime = $this->dt->modify("+1 hour")->format('U');

		$client_token = $this->jwtBuilder
            ->setIssuer('www.gigadb.org') // Configures the issuer (iss claim)
            ->setAudience('fuw.gigadb.org') // Configures the audience (aud claim)
            ->setSubject('API Access request from client') // Configures the subject
            ->set('email', $email)
            ->set('name', $user->getFullName())
            ->set('role', $user->role)
            ->setIssuedAt($issuedTime) // Configures the time that the token was issue (iat claim)
            ->setNotBefore($notBeforeTime) // Configures the time before which the token cannot be accepted (nbf claim)
            ->setExpiration($expirationTime) // Configures the expiration time of the token (exp claim) 1 year
            ->sign($signer, Yii::$app->jwt->key)// creates a signature using [[Jwt::$key]]
            ->getToken(); // Retrieves the generated token

		return $client_token;
	}

	/**
	 * Make HTTP POST to File Upload Wizard to create user
	 *
	 * @param \Lcobucci\JWT\Token $token authentication token
	 * @param \GuzzleHttp\Client $webClient web client
	 * @param string $username username for the user to create
	 * @param string $email email for the user to create
	 *
	 * @return ?array whether the call has been made and succeed or not. If succes, return an array of created User's properties.
	 */
	public function createUser($token, $webClient, string $username, string $email): ?array
	{

		$api_endpoint = "http://fuw-admin-api/users";

		try {
			$response = $webClient->request('POST', $api_endpoint, [
								    'headers' => [
								        'Authorization' => "Bearer ".$token,
								    ],
								    'form_params' => [
								        'username' => $username,
								        'email' => $email,
								    ],
								    'connect_timeout' => 5,
								]);
			if (201 === $response->getStatusCode() ) {
				return json_decode($response->getBody(), true);
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
	 * Generate JWT token for a mockup url
	 *
	 * we need to make sure we use a new builder here (by calling Yii::$app->jwt->getBuilder())
	 * as we cannot use the one ($this->jwtBuilder) for the normal api authentication 
	 * in generateTokenForUser() as we need to call both methods in the same function
	 *
	 * @param string $email the email of the reviewer the mockup url is created for
	 * @param int $validity how many months the mockup url should be valid for
	 * @param string $doi identifier for the dataset for which to create a mockup
	 * @return \Lcobucci\JWT\Token the signed token
	 */
	public function generateTokenForMockup(string $email, int $validity, string $doi): \Lcobucci\JWT\Token
	{
		$signer = $this->jwtSigner;
		$issuedTime = $this->dt->format('U');
		$notBeforeTime = $issuedTime ;
		$expirationTime = $this->dt->modify("+$validity months")->format('U');

		$client_token = $this->jwtBuilder
            ->setIssuer('www.gigadb.org') // Configures the issuer (iss claim)
            ->setAudience('fuw.gigadb.org') // Configures the audience (aud claim)
            ->setSubject('JWT token for a unique and time-limited mockup url') // Configures the subject
            ->set('reviewerEmail', $email)
            ->set('monthsOfValidity', $validity)
            ->set('DOI', $doi)
            ->setIssuedAt($issuedTime) // Configures the time that the token was issue (iat claim)
            ->setNotBefore($notBeforeTime) // Configures the time before which the token cannot be accepted (nbf claim)
            ->setExpiration($expirationTime) // Configures the expiration time of the token (exp claim) 1 year
            ->sign($signer, Yii::$app->jwt->key)// creates a signature using [[Jwt::$key]]
            ->getToken(); // Retrieves the generated token

		return $client_token;
	}

}
?>