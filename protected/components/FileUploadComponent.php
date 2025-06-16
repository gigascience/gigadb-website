<?php

class FileUploadComponent extends CApplicationComponent
{
    /**
     * @throws CException
     */
    public function getFileUploadService($webClient, $identifier, $tokenService = null)
    {
        if (!$webClient instanceof \GuzzleHttp\Client) {
            throw new CException('An error occurred');
        }

        if (!$tokenService) {
            $tokenService = $this->createTokenService();
        }
        return new  FileUploadService([
            'tokenSrv'       => $tokenService,
            'webClient'      => $webClient,
            'requesterEmail' => Yii::app()->user->email,
            'identifier'     => $identifier,
            'dataset'        => new DatasetDAO(['identifier' => $identifier]),
            'dryRunMode'     => false,
        ]);
    }

    public function createTokenService(bool $withUser = true, bool $withTtl = true): TokenService
    {
        $args = [
            'jwtTTL'     => 3600,
            'jwtBuilder' => Yii::$app->jwt->getBuilder(),
            'jwtSigner'  => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
            'users'      => new UserDAO(),
            'dt'         => new DateTime(),
        ];

        if(!$withUser) {
            unset($args['users']);
        }

        if(!$withTtl) {
            unset($args['jwtTTL']);
        }
        return new TokenService($args);
    }
}
