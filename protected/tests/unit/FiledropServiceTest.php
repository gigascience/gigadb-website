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

		$mockTokenSrv->expects($this->once())
                 ->method('generateTokenForUser')
                 ->willReturn($mockToken);

        $mockWebClient->expects($this->once())
                 ->method('request')
                 ->willReturn($mockResponse);



        $mockResponse->expects($this->once())
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

        $this->assertTrue($filedropSrv->emailInstructions(1,"foo","bar"));

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

        $this->assertFalse($filedropSrv->emailInstructions(1,"foo",""));
        $this->assertFalse($filedropSrv->emailInstructions(1,"","bar"));

	}

}


?>