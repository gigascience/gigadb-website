<?php

declare(strict_types=1);

namespace unit;

use GigaDB\models\UserDao;
use GigaDB\services\TokenService;
use PHPUnit\Framework\TestCase;

class TokenServiceYii2Test extends TestCase
{
    public function testGenerateTokenForUserWithEmailOnly()
    {
        $email = "test@test.fr";
        $jwtTtl = 3600;
        $jwtBuilder = \Yii::$app->jwt->getBuilder();

        $user = $this->createMock(\User::class);
        $user->expects($this->once())
            ->method('getFullName')
            ->willReturn('fullName');
        $user->expects($this->once())
            ->method('__get')
            ->with('role')->willReturn('test_role');

        $userDao = $this->createMock(UserDao::class);
        $userDao->expects($this->once())->method('findByEmail')->with($email)->willReturn($user);

        $tokenService = new TokenService($jwtTtl, $jwtBuilder, new \Lcobucci\JWT\Signer\Hmac\Sha256(), $userDao, new \DateTime());
        $token = $tokenService->generateTokenForUser($email);

        $data = \Yii::$app->jwt->getValidationData();
        $data->setIssuer('www.gigadb.org');
        $data->setAudience('fuw.gigadb.org');
        $data->setSubject('API Access request from client');

        $this->assertTrue($token->validate($data));

        $this->assertEquals($token->getClaim('name'), 'fullName');
        $this->assertEquals($token->getClaim('email'), $email);
        $this->assertEquals($token->getClaim('role'), 'test_role');
    }
}
