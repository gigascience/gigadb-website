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
                 "U",
                 "U"
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
}