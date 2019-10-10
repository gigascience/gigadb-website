<?php


class TokenServiceTest extends CTestCase
{
  public function testCreateToken()
  {
    $jwt_key = "fooTESTbar$#^%@#";
    $jwt_ttl = 31104000 ;
    $email = "foo@bar.com";

    // Setup mocks
    $mockUserDAO = $this->getMockBuilder(UserDAO::class)
                    ->setMethods(['findByEmail'])
                    ->getMock();

    $mockUser = $this->getMockBuilder(User::class)
                        ->setMethods(['getFullName', 'getRole'])
                        ->getMock();

    $mockDateTime = $this->getMockBuilder(\DateTime::class)
                          ->setMethods(['modify'])
                          ->getMock();

    $mockModifiedDateTimes = $this->getMockBuilder(\DateTime::class)
                          ->setMethods(['format'])
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

    $mockUser->expects($this->once())
                 ->method('getRole')
                 ->willReturn("admin");

    // 2. calculate validity period (notbefore and expiry datetime)
    $mockDateTime->expects($this->exactly(2))
                 ->method('modify')
                 ->withConsecutive(
                 "+60 seconds",
                 "+1 year"
             )
             ->willReturn($mockModifiedDateTimes);

    //3. format both datetimes as seconds since epoch to feed the JWT builder
    $testStartTime = time()+60 ;
    $testExpiryTime = $testStartTime+$jwt_ttl ;
    $mockModifiedDateTimes->expects($this->exactly(2))
                 ->method('format')
                 ->withConsecutive(
                 "U",
                 "U"
             )
             ->will($this->onConsecutiveCalls(
              $testStartTime,
              $testExpiryTime
            ));

    // Instantiate the token service after injecting the mock and invoke token creation
    $tokenSrv = new TokenService(['jwtKey' => $jwt_key,
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
    $data->setSubject('Access to FUW API');

    // set the time of validation to be after testStartTime
    $data->setCurrentTime(time() + 61);

    $this->assertTrue($token->validate($data));

    // 5. verify that the necessary user info can be claimed from token
    $this->assertEquals($token->getClaim('name'),"Foo Bar");
    $this->assertEquals($token->getClaim('email'),"$email");
    $this->assertEquals($token->getClaim('role'),"admin");


  }
}