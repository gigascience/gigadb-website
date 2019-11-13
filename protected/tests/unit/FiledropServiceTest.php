<?php

namespace GigaDB\Tests\UnitTests;

/**
 * Unit tests for FiledropService
 *
 * mostly for testing error modes for emailInstructions
 * and interactions with client and token services
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 *
 */
class FiledropServiceTest extends \CTestCase
{

	/**
	 * test emailInstructions() passing
	 *
	 */
	public function testEmailInstructions()
	{


		// set mocks
		$mockTokenSrv = $this->createMock(\TokenService::class);
		$mockToken = $this->createMock(\Lcobucci\JWT\Token::class);
		$mockWebClient = $this->createMock(\GuzzleHttp\Client::class);
		$mockResponse = $this->createMock(\GuzzleHttp\Psr7\Response::class);

        // set expected parameters to the HTTP request:
        $filedrop_id = 1;
        $api_endpoint = "http://fuw-admin-api/filedrop-accounts/$filedrop_id";
        $doi = "100001";
        $recipient = "user@domain.tld";
        $subject = "hello world";
        $instructions = "foo bar";
        $method = "PUT";
        $connect_timeout = 5 ;
        $auth_header = ['Authorization' => "Bearer ".$this->mockToken];
        $form_params = [
                        'doi' => $doi,
                        'subject' => $subject,
                        'instructions' => $instructions,
                        'to' => $recipient,
                        'send' => true,
                        ];

		$mockTokenSrv->expects($this->once())
                 ->method('generateTokenForUser')
                 ->willReturn($mockToken);

        $mockWebClient->expects($this->once())
                 ->method('request')
                 ->with($method, $api_endpoint, [ 'headers' => $auth_header,
                                                'form_params' => $form_params,
                                                'connect_timeout' => $connect_timeout
                        ])
                 ->willReturn($mockResponse);



        $mockResponse->expects($this->once())
                 ->method('getStatusCode')
                 ->willReturn(200);

		// Instantiate FiledropService
        $filedropSrv = new \FiledropService([
            "tokenSrv" => $mockTokenSrv,
            "webClient" => $mockWebClient,
            "requester" => \User::model()->findByPk(344),
            "identifier"=> $doi,
            "dryRunMode"=>true,
            ]);

        // make the call
        $this->assertTrue($filedropSrv->emailInstructions($filedrop_id,$recipient,$subject,$instructions));

	}

    /**
     * test emailInstructions() passing
     *
     */
    public function testSaveInstructions()
    {

        // set mocks
        $mockTokenSrv = $this->createMock(\TokenService::class);
        $mockToken = $this->createMock(\Lcobucci\JWT\Token::class);
        $mockWebClient = $this->createMock(\GuzzleHttp\Client::class);
        $mockResponse = $this->createMock(\GuzzleHttp\Psr7\Response::class);

        // set expected parameters to the HTTP request:
        $filedrop_id = 1;
        $api_endpoint = "http://fuw-admin-api/filedrop-accounts/$filedrop_id";
        $doi = "100001";
        $instructions = "foo bar";
        $method = "PUT";
        $connect_timeout = 5 ;
        $auth_header = ['Authorization' => "Bearer ".$this->mockToken];
        $form_params = [
                        'doi' => $doi,
                        'instructions' => $instructions,
                        ];
        $mockTokenSrv->expects($this->once())
                 ->method('generateTokenForUser')
                 ->willReturn($mockToken);

        $mockWebClient->expects($this->once())
                 ->method('request')
                 ->with($method, $api_endpoint, [ 'headers' => $auth_header,
                                                'form_params' => $form_params,
                                                'connect_timeout' => $connect_timeout
                        ])
                 ->willReturn($mockResponse);



        $mockResponse->expects($this->once())
                 ->method('getStatusCode')
                 ->willReturn(200);

        // Instantiate FiledropService
        $filedropSrv = new \FiledropService([
            "tokenSrv" => $mockTokenSrv,
            "webClient" => $mockWebClient,
            "requester" => \User::model()->findByPk(344),
            "identifier"=> $doi,
            "dryRunMode"=>true,
            ]);

        $this->assertTrue($filedropSrv->saveInstructions(1,$instructions));

    }

	/**
	 * test emailInstructions() with incorrect arguments
	 *
	 */
	public function testEmailInstructionsIncorrectArguments()
	{

		// set mocks
		$mockTokenSrv = $this->createMock(\TokenService::class);
		$mockToken = $this->createMock(\Lcobucci\JWT\Token::class);
		$mockWebClient = $this->createMock(\GuzzleHttp\Client::class);
		$mockResponse = $this->createMock(\GuzzleHttp\Psr7\Response::class);

		$mockTokenSrv->expects($this->never())
                 ->method('generateTokenForUser')
                 ->willReturn($mockToken);

        $mockWebClient->expects($this->never())
                 ->method('request')
                 ->willReturn($mockResponse);



        $mockResponse->expects($this->never())
                 ->method('getStatusCode')
                 ->willReturn(200);

		// Instantiate FiledropService
        $filedropSrv = new \FiledropService([
            "tokenSrv" => $mockTokenSrv,
            "webClient" => $mockWebClient,
            "requester" => \User::model()->findByPk(344),
            "identifier"=> "foobar",
            "dryRunMode"=>true,
            ]);

        $this->assertFalse($filedropSrv->emailInstructions(1,"","foo","bar"));
        $this->assertFalse($filedropSrv->emailInstructions(1,"user@domain.tld","foo",""));
        $this->assertFalse($filedropSrv->emailInstructions(1,"user@domain.tld","","bar"));

	}

}


?>