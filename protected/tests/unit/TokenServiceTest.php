<?php

/**
 * Unit test for TokenService
 *
 * NB: example of testing Date/Time function
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class TokenServiceTest extends CTestCase
{
  public function testCreateToken()
  {
    $email = "foo@bar.com"; //dummy email of requester of the token
    $jwt_key = "fooTESTbar$#^%@#"; //private key for JWT tokens
    $jwt_ttl = 3600 ; //1 hour validity
    $issuedTime = 1569171152; //dummy time of issue of the token

    // Setup mocks
    $mockUserDAO = $this->getMockBuilder(UserDAO::class)
                    ->setMethods(['findByEmail'])
                    ->getMock();

    $mockUser = $this->getMockBuilder(User::class)
                        ->setMethods(['getFullName'])
                        ->getMock();

    $mockDateTime = $this->getMockBuilder(\DateTime::class)
                          ->setMethods(['modify','format'])
                          ->getMock();

    // 0. find user record
    $mockUserDAO->expects($this->once())
                 ->method('findByEmail')
                 ->with($email)
                 ->willReturn($mockUser);

    // 1. get user's full name and role
    $mockUser->expects($this->once())
                 ->method('getFullName')
                 ->willReturn("Foo Bar");

    // 2. calculate validity period (notbefore and expiry datetime)
    $mockDateTime->expects($this->once())
                 ->method('modify')
                 ->with("+1 hour")
             ->willReturn($mockDateTime);

    //3. format both datetimes as seconds since epoch to feed the JWT builder
    $mockExpirationTime = $issuedTime + $jwt_ttl ;
    $mockDateTime->expects($this->exactly(2))
                 ->method('format')
                 ->withConsecutive(
                 ["U"],
                 ["U"]
             )
             ->will($this->onConsecutiveCalls(
              $issuedTime,
              $mockExpirationTime
            ));

    // Instantiate the token service after injecting the mock and invoke token creation
    $tokenSrv = new TokenService([
                                  'jwtTTL' => $jwt_ttl,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => $mockUserDAO,
                                  'dt' => $mockDateTime,
                                ]);

    $token = $tokenSrv->generateTokenForUser($email);

    // 4. test that we have a valid token created
    $data = Yii::$app->jwt->getValidationData(); // It will use the current time to validate (iat, nbf and exp)
    $data->setIssuer('www.gigadb.org');
    $data->setAudience('fuw.gigadb.org');
    $data->setId('4f1g23a12aa');
    $data->setSubject('API Access request from client');

    // set the time of usage to be straight after issue
    // (to reflect real call scenario, it should be valid)
    $data->setCurrentTime($issuedTime);
    $this->assertTrue($token->validate($data));


    // set the time of usage to be just before 1 hour later ( it should be valid)
    $data->setCurrentTime($issuedTime+$jwt_ttl-1);
    $this->assertTrue($token->validate($data));

    // set the time of usage to more than 1 hour later (it should not be valid)
    $data->setCurrentTime($issuedTime+$jwt_ttl+1);
    $this->assertFalse($token->validate($data));

    // 5. verify that the necessary user info can be claimed from token
    $this->assertEquals($token->getClaim('name'),"Foo Bar");
    $this->assertEquals($token->getClaim('email'),"$email");
    $this->assertEquals($token->getClaim('role'),"user");


  }

  /**
     * test createUser() passing
     *
     */
    public function testCreateUser()
    {

      // set mocks
      $mockToken = $this->createMock(\Lcobucci\JWT\Token::class);
      $mockWebClient = $this->createMock(\GuzzleHttp\Client::class);
      $mockResponse = $this->createMock(\GuzzleHttp\Psr7\Response::class);

      // set expected parameters to the HTTP request:
      $filedrop_id = 1;
      $api_endpoint = "http://fuw-admin-api/users";
      $username = "foo bar";
      $email = "foo@bar.com";
      $method = "POST";
      $connect_timeout = 5 ;
      $auth_header = ['Authorization' => "Bearer ".$mockToken];
      $form_params = [
                      'username' => $username,
                      'email' => $email,
                      ];
      $jsonResponse = json_encode(["id" => 3434, "username" =>"foobar", "email" => "foo@bar.com"]);

      $mockResponse->expects($this->once())
               ->method('getBody')
               ->willReturn($jsonResponse);

      $mockWebClient->expects($this->once())
               ->method('request')
               ->with($method, $api_endpoint, [ 'headers' => $auth_header,
                                              'form_params' => $form_params,
                                              'connect_timeout' => $connect_timeout
                      ])
               ->willReturn($mockResponse);



      $mockResponse->expects($this->once())
               ->method('getStatusCode')
               ->willReturn(201);

      // Instantiate the token service after injecting the mock and invoke token creation
      $tokenSrv = new TokenService([
                                  'jwtTTL' => $jwt_ttl,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => $mockUserDAO,
                                  'dt' => $mockDateTime,
                                ]);

      $userData = $tokenSrv->createUser(
        $mockToken, $mockWebClient, $username, $email
      );
      $this->assertNotNull($userData);
      $this->assertNotNull($userData['id']);
      $this->assertNotNull($userData['username']);
      $this->assertNotNull($userData['email']);
    }

public function testCreateMockupToken()
  {
    $email = "foo@bar.com"; //dummy email of requester of the token
    $validity = 3;
    $jwt_key = "fooTESTbar$#^%@#"; //private key for JWT tokens
    $jwt_ttl = 2629800*$validity ; //3 months validity
    $issuedTime = 1569171152; //dummy time of issue of the token

    // Setup mocks

    $mockDateTime = $this->getMockBuilder(\DateTime::class)
                          ->setMethods(['modify','format'])
                          ->getMock();


    // 2. calculate validity period (notbefore and expiry datetime)
    $mockDateTime->expects($this->once())
                 ->method('modify')
                 ->with("+$validity months")
             ->willReturn($mockDateTime);

    //3. format both datetimes as seconds since epoch to feed the JWT builder
    $mockExpirationTime = $issuedTime + $jwt_ttl ;
    $mockDateTime->expects($this->exactly(2))
                 ->method('format')
                 ->withConsecutive(
                 ["U"],
                 ["U"]
             )
             ->will($this->onConsecutiveCalls(
              $issuedTime,
              $mockExpirationTime
            ));

    // Instantiate the token service after injecting the mock and invoke token creation
    $tokenSrv = new TokenService([
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'dt' => $mockDateTime,
                                ]);

    $token = $tokenSrv->generateTokenForMockup($email,$validity);

    // 4. test that we have a valid token created
    $data = Yii::$app->jwt->getValidationData(); // It will use the current time to validate (iat, nbf and exp)
    $data->setIssuer('www.gigadb.org');
    $data->setAudience('fuw.gigadb.org');
    $data->setId('3256tag4f1g23a12aa');
    $data->setSubject('JWT token for a unique and time-limited mockup url');

    // set the time of usage to be straight after issue
    // (to reflect real call scenario, it should be valid)
    $data->setCurrentTime($issuedTime);
    $this->assertTrue($token->validate($data));


    // set the time of usage to be just before 3 months later ( it should be valid)
    $data->setCurrentTime($issuedTime+$jwt_ttl-1);
    $this->assertTrue($token->validate($data));

    // set the time of usage to more than 3 months later (it should not be valid)
    $data->setCurrentTime($issuedTime+$jwt_ttl+1);
    $this->assertFalse($token->validate($data));

    // 5. verify that the necessary user info can be claimed from token
    $this->assertEquals($token->getClaim('reviewerEmail'), $email);
    $this->assertEquals($token->getClaim('monthsOfValidity'), $validity);


  }
}